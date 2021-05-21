<?php

namespace LocalPickup\EventListeners;

use LocalPickup\LocalPickup;
use OpenApi\Events\DeliveryModuleOptionEvent;
use OpenApi\Events\OpenApiEvents;
use OpenApi\Model\Api\DeliveryModuleOption;
use OpenApi\Model\Api\ModelFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Core\HttpFoundation\Session\Session;
use Thelia\Model\ModuleQuery;

class APIListener implements EventSubscriberInterface
{
    public function getDeliveryModuleOptions(DeliveryModuleOptionEvent $deliveryModuleOptionEvent, Session $session, ModelFactory $modelFactory)
    {
        $module = ModuleQuery::create()->findOneByCode(LocalPickup::getModuleCode());
        if ($deliveryModuleOptionEvent->getModule()->getId() !== $module->getId()) {
            return ;
        }

        $isValid = true;
        $postage = 0;
        $postageTax = 0;

        $minimumDeliveryDate = '';
        $maximumDeliveryDate = '';

        $images = $module->getModuleImages();
        $imageId = 0;

        $title = $module->setLocale($session->getLang()->getLocale())->getTitle();

        if ($images->count() > 0) {
            $imageId = $images->getFirst()->getId();
        }

        /** @var DeliveryModuleOption $deliveryModuleOption */
        $deliveryModuleOption = $modelFactory->buildModel('DeliveryModuleOption');
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

        $deliveryModuleOptionEvent->appendDeliveryModuleOptions($deliveryModuleOption);
    }

    public static function getSubscribedEvents()
    {
        $listenedEvents = [];

        /** Check for old versions of Thelia where the events used by the API didn't exists */
        if (class_exists(DeliveryModuleOptionEvent::class)) {
            $listenedEvents[OpenApiEvents::MODULE_DELIVERY_GET_OPTIONS] = array("getDeliveryModuleOptions", 129);
        }

        return $listenedEvents;
    }
}