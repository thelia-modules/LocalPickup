<?php

/*
 * This file is part of the Thelia package.
 * http://www.thelia.net
 *
 * (c) OpenStudio <info@thelia.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LocalPickup\EventListeners;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use LocalPickup\LocalPickup;
use OpenApi\Events\DeliveryModuleOptionEvent;
use OpenApi\Events\OpenApiEvents;
use OpenApi\Model\Api\DeliveryModuleOption;
use OpenApi\Model\Api\ModelFactory;
use Propel\Runtime\Exception\PropelException;
use SmartyException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Notifier\Exception\TransportExceptionInterface;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\TexterInterface;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Template\ParserInterface;
use Thelia\Core\Template\TemplateHelperInterface;
use Thelia\Exception\TheliaProcessException;
use Thelia\Mailer\MailerFactory;
use Thelia\Model\CountryQuery;
use Thelia\Model\MessageQuery;
use Thelia\Model\ModuleQuery;
use Thelia\Model\Order;
use Thelia\Model\OrderStatus;

class APIListener implements EventSubscriberInterface
{
    /** @var ModelFactory */
    protected $modelFactory;

    /** @var RequestStack */
    protected $requestStack;

    /**
     * @var MailerFactory
     */
    protected $mailer;

    /**
     * APIListener constructor.
     */
    public function __construct(
        ModelFactory                             $modelFactory,
        RequestStack                             $requestStack,
        MailerFactory                            $mailer,
        private readonly ?TexterInterface        $texter,
        private readonly ParserInterface         $parser,
        private readonly TemplateHelperInterface $templateHelper
    )
    {
        $this->modelFactory = $modelFactory;
        $this->requestStack = $requestStack;
        $this->mailer = $mailer;
    }

    public function getDeliveryModuleOptions(DeliveryModuleOptionEvent $deliveryModuleOptionEvent): void
    {
        $module = ModuleQuery::create()->findOneByCode(LocalPickup::getModuleCode());
        if ($deliveryModuleOptionEvent->getModule()->getId() !== $module->getId()) {
            return;
        }

        $isValid = true;
        $locale = $this->requestStack->getCurrentRequest()->getSession()->getLang()->getLocale();

        $postage = LocalPickup::getConfigValue(LocalPickup::PRICE_VAR_NAME, 0);
        $commentary = LocalPickup::getConfigValue(
            LocalPickup::DESCRIPTION_VAR_NAME,
            '',
            $locale
        );

        $postageTax = 0;

        $minimumDeliveryDate = '';
        $maximumDeliveryDate = '';

        $images = $module->getModuleImages();
        $imageId = 0;

        $title = $module->setLocale($locale)->getTitle();

        if ($images->count() > 0) {
            $imageId = $images->getFirst()->getId();
        }

        /** @var DeliveryModuleOption $deliveryModuleOption */
        $deliveryModuleOption = $this->modelFactory->buildModel('DeliveryModuleOption');
        $deliveryModuleOption
            ->setCode(LocalPickup::getModuleCode())
            ->setValid($isValid)
            ->setTitle($title)
            ->setImage($imageId)
            ->setMinimumDeliveryDate($minimumDeliveryDate)
            ->setMaximumDeliveryDate($maximumDeliveryDate)
            ->setPostage($postage)
            ->setPostageTax($postageTax)
            ->setPostageUntaxed($postage - $postageTax);

        // Pre-5.3.x compatibility
        if (method_exists($deliveryModuleOption, 'setDescription')) {
            $deliveryModuleOption->setDescription($commentary);
        }

        $deliveryModuleOptionEvent->appendDeliveryModuleOptions($deliveryModuleOption);
    }

    /**
     * @throws PropelException
     * @throws TransportExceptionInterface
     * @throws SmartyException
     */
    public function getOrderStatus(OrderEvent $orderEvent): void
    {
        $order = $orderEvent->getOrder();
        if (!$this->isEligibleForLocalPickupNotification($order)) {
            return;
        }
        $this->sendLocalPickupEmail($order);
        if ($this->texter) {
            $this->sendSmsIfNeeded($order);
        }
    }

    private function isEligibleForLocalPickupNotification(Order $order): bool
    {
        return $order->getDeliveryModuleId() === LocalPickup::getModuleId()
            && $order->getOrderStatus()->getCode() === OrderStatus::CODE_SENT;
    }

    private function sendLocalPickupEmail(Order $order): void
    {
        $this->mailer->sendEmailToCustomer(
            LocalPickup::EMAIL_CUSTOM_LOCAL_PICKUP,
            $order->getCustomer(),
            [
                'order_id' => $order->getId(),
                'order_ref' => $order->getRef(),
                'comment' => LocalPickup::getConfigValue(
                    LocalPickup::EMAIL_VAR_NAME,
                    '',
                    $order->getLang()->getLocale()
                ),
            ]
        );
    }

    /**
     * @throws PropelException|TransportExceptionInterface
     * @throws SmartyException
     */
    private function sendSmsIfNeeded(Order $order): void
    {
        $phoneNumber = $order->getOrderAddressRelatedByDeliveryOrderAddressId()->getPhone();
        $cellPhoneNumber = $order->getOrderAddressRelatedByDeliveryOrderAddressId()->getCellphone();

        $numberToUse = $cellPhoneNumber ?? $phoneNumber;
        if ($numberToUse === null || $this->isSurtaxedNumber($numberToUse)) {
            return;
        }

        $langCode = $this->getOrderLangCode($order);
        $internationalNumber = $this->internationalizePhoneNumber($numberToUse, $langCode);
        $message = MessageQuery::create()
            ->filterByName(LocalPickup::SMS_CUSTOM_LOCAL_PICKUP)
            ->findOne();
        if (!$message) {
            throw new TheliaProcessException('Template  ' . LocalPickup::SMS_CUSTOM_LOCAL_PICKUP . ' not found.');
        }
        $this->parser->setTemplateDefinition(
            $this->templateHelper->getActiveAdminTemplate(),
            true
        );
        $sms = new SmsMessage(
            $internationalNumber,
            $this->parser->render($message->getHtmlTemplateFileName(), ['order_id' => $order->getId()])
        );
        $this->texter->send($sms);
    }

    private function isSurtaxedNumber(string $phoneNumber): bool
    {
        $surtaxedPatterns = [
            '/^089[0-9]{1}/',   // Start by 089
            '/^08[0-9]{2}/',    // Start by 08 (can be surtaxed)
            '/^36[0-9]{2}/',    // Short numbers (for instance 36xx, often surtaxed)
        ];

        foreach ($surtaxedPatterns as $pattern) {
            if (preg_match($pattern, $phoneNumber)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @throws NumberParseException
     */
    private function internationalizePhoneNumber(string $phoneNumber, string $region = 'FR'): string
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        /** @var PhoneNumber $phoneNumberObject */
        $phoneNumberObject = $phoneUtil->parse($phoneNumber, $region);

        return $phoneUtil->format($phoneNumberObject, PhoneNumberFormat::E164);
    }

    /**
     * @throws PropelException
     */
    private function getOrderLangCode(Order $order): string
    {
        $country = CountryQuery::create()
            ->findOneById($order->getOrderAddressRelatedByDeliveryOrderAddressId()->getCountryId());

        return $country ? $country->getIsoalpha2() : 'FR';
    }

    public static function getSubscribedEvents(): array
    {
        $listenedEvents = [];

        /* Check for old versions of Thelia where the events used by the API didn't exists */
        if (class_exists(DeliveryModuleOptionEvent::class)) {
            $listenedEvents[OpenApiEvents::MODULE_DELIVERY_GET_OPTIONS] = ['getDeliveryModuleOptions', 129];
        }

        $listenedEvents[TheliaEvents::ORDER_UPDATE_STATUS] = ['getOrderStatus', 99];

        return $listenedEvents;
    }
}
