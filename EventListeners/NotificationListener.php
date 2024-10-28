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
use Propel\Runtime\Exception\PropelException;
use SmartyException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Notifier\Exception\TransportExceptionInterface;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\TexterInterface;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Template\ParserInterface;
use Thelia\Exception\TheliaProcessException;
use Thelia\Log\Tlog;
use Thelia\Mailer\MailerFactory;
use Thelia\Model\CountryQuery;
use Thelia\Model\MessageQuery;
use Thelia\Model\Order;
use Thelia\Model\OrderStatus;

readonly class NotificationListener implements EventSubscriberInterface
{
    /**
     * APIListener constructor.
     */
    public function __construct(
        private MailerFactory    $mailer,
        private ?TexterInterface $texter,
        private ParserInterface  $parser,
    ) {
    }

    /**
     * @param OrderEvent $orderEvent
     * @return void
     * @throws PropelException
     * @throws TransportExceptionInterface
     */
    public function orderStatusChange(OrderEvent $orderEvent): void
    {
        $order = $orderEvent->getOrder();
        if (!$this->isEligibleForLocalPickupNotification($order)) {
            return;
        }

        try {
            $this->sendLocalPickupEmail($order);

            if ($this->texter && LocalPickup::getConfigValue(LocalPickup::SMS_VAR_NAME)) {
                $this->sendSmsIfNeeded($order);
            }
        } catch (\Exception $ex) {
            Tlog::getInstance()->error("Failed to send notification : " . $ex->getMessage());
        }
    }

    /**
     * @param Order $order
     * @return bool
     * @throws PropelException
     */
    private function isEligibleForLocalPickupNotification(Order $order): bool
    {
        return $order->getDeliveryModuleId() === LocalPickup::getModuleId()
            && $order->getOrderStatus()->getCode() === OrderStatus::CODE_SENT;
    }

    /**
     * @param Order $order
     * @return void
     * @throws PropelException
     */
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
     * @param Order $order
     * @return void
     * @throws NumberParseException
     * @throws PropelException
     * @throws SmartyException
     * @throws TransportExceptionInterface
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
            throw new TheliaProcessException('Message ' . LocalPickup::SMS_CUSTOM_LOCAL_PICKUP . ' not found.');
        }

        $message->setLocale($order->getLang()->getLocale());

        $this->parser->assign('order_id', $order->getId());

        $sms = new SmsMessage(
            $internationalNumber,
            $message->getTextMessageBody($this->parser)
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
        return [
            TheliaEvents::ORDER_UPDATE_STATUS => ['orderStatusChange', 99]
        ];
    }
}
