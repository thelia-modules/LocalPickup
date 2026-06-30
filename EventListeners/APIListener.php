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

use LocalPickup\LocalPickup;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Api\Bridge\Propel\Event\DeliveryModuleOptionEvent;
use Thelia\Api\Resource\DeliveryModuleOption;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Model\ModuleQuery;

class APIListener implements EventSubscriberInterface
{
    /**
     * APIListener constructor.
     */
    public function __construct(
        protected RequestStack $requestStack
    ) {}

    public function getDeliveryModuleOptions(DeliveryModuleOptionEvent $deliveryModuleOptionEvent): void
    {
        $module = ModuleQuery::create()->findOneByCode(LocalPickup::getModuleCode());
        if ($deliveryModuleOptionEvent->getModule()->getId() !== $module?->getId()) {
            return;
        }

        $request = $this->requestStack->getCurrentRequest();
        $locale = (null !== $request && $request->hasSession())
            ? $request->getSession()->getLang()->getLocale()
            : (\Thelia\Model\LangQuery::create()->findOneByByDefault(true)?->getLocale() ?? 'en_US');

        $postage = LocalPickup::getConfigValue(LocalPickup::PRICE_VAR_NAME, 0);
        $commentary = LocalPickup::getConfigValue(
            LocalPickup::DESCRIPTION_VAR_NAME,
            '',
            $locale
        );

        $postageTax = 0;

        $minimumDeliveryDate = '';
        $maximumDeliveryDate = '';

        $images = $module?->getModuleImages();
        $imageId = 0;

        $title = $module?->setLocale($locale)->getTitle();

        if ($images->count() > 0) {
            $imageId = $images->getFirst()?->getId();
        }

        $deliveryModuleOption = new DeliveryModuleOption();
        $deliveryModuleOption
            ->setCode(LocalPickup::getModuleCode())
            ->setValid(true)
            ->setTitle($title)
            ->setImage($imageId)
            ->setMinimumDeliveryDate($minimumDeliveryDate)
            ->setMaximumDeliveryDate($maximumDeliveryDate)
            ->setPostage($postage + $postageTax)
            ->setPostageTax($postageTax)
            ->setPostageUntaxed($postage);

        // Pre-5.3.x compatibility
        if (method_exists($deliveryModuleOption, 'setDescription')) {
            $deliveryModuleOption->setDescription($commentary);
        }

        $deliveryModuleOptionEvent->appendDeliveryModuleOptions($deliveryModuleOption);
    }

    public static function getSubscribedEvents(): array
    {
        $listenedEvents = [];

        /* Check for old versions of Thelia where the events used by the API didn't exists */
        if (class_exists(DeliveryModuleOptionEvent::class)) {
            $listenedEvents[TheliaEvents::MODULE_DELIVERY_GET_OPTIONS] = ['getDeliveryModuleOptions', 129];
        }

        return $listenedEvents;
    }
}
