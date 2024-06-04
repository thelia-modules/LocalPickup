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

use Billaudot\Billaudot;
use LocalPickup\LocalPickup;
use OpenApi\Events\DeliveryModuleOptionEvent;
use OpenApi\Events\OpenApiEvents;
use OpenApi\Model\Api\DeliveryModuleOption;
use OpenApi\Model\Api\ModelFactory;
use Propel\Runtime\Exception\PropelException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Mailer\MailerFactory;
use Thelia\Model\ModuleQuery;
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
     *
     * @param ContainerInterface $container We need the container because we use a service from another module
     *                                      which is not mandatory, and using its service without it being installed will crash
     */
    public function __construct(ModelFactory $modelFactory, RequestStack $requestStack, MailerFactory $mailer)
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
            ->setPostageUntaxed($postage - $postageTax)
        ;

        // Pre-5.3.x compatibility
        if (method_exists($deliveryModuleOption, 'setDescription')) {
            $deliveryModuleOption->setDescription($commentary);
        }

        $deliveryModuleOptionEvent->appendDeliveryModuleOptions($deliveryModuleOption);
    }

    /**
     * @throws PropelException
     */
    public function getOrderStatus(OrderEvent $orderEvent)
    {
        $order = $orderEvent->getOrder();

        if ($order->getDeliveryModuleId() !== LocalPickup::getModuleId() || $order->getOrderStatus()->getCode() !== OrderStatus::CODE_SENT) {
            return;
        }

        $this->mailer->sendEmailToCustomer(
            LocalPickup::EMAIL_CUSTOM_LOCAL_PICKUP,
            $order->getCustomer(),
            [
                'order_id' => $order->getId(),
                'order_ref' => $order->getRef(),
                'comment' => LocalPickup::getConfigValue(LocalPickup::EMAIL_VAR_NAME, '', $order->getLang()->getLocale()),
            ]
        );
    }

    public static function getSubscribedEvents()
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
