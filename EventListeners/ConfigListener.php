<?php

namespace LocalPickup\EventListeners;

use Symfony\Component\EventDispatcher\GenericEvent;
use LocalPickup\LocalPickup;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Model\AreaDeliveryModuleQuery;

class ConfigListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            'module.config' => [
                'onModuleConfig', 128
                ],
        ];
    }

    public function onModuleConfig(GenericEvent $event): void
    {
        $subject = $event->getSubject();

        if ($subject !== "HealthStatus") {
            throw new \RuntimeException('Event subject does not match expected value');
        }

        $shippingZoneConfig = AreaDeliveryModuleQuery::create()
            ->filterByDeliveryModuleId(LocalPickup::getModuleId())
            ->find();

        $moduleConfig = [];
        $moduleConfig['module'] = LocalPickup::getModuleCode();
        $configsCompleted = true;

        if ($shippingZoneConfig->count() === 0) {
            $configsCompleted = false;
        }

        $moduleConfig['completed'] = $configsCompleted;

        $event->setArgument('local.pickup.module.config', $moduleConfig);
    }
}
