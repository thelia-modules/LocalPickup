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
use OpenApi\Events\DeliveryModuleOptionEvent;
use OpenApi\Events\OpenApiEvents;
use OpenApi\Model\Api\DeliveryModuleOption;
use OpenApi\Model\Api\ModelFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Model\ModuleQuery;

class APIListener implements EventSubscriberInterface
{
    /**
     * APIListener constructor.
     */
    public function __construct(
        protected ModelFactory $modelFactory,
        protected RequestStack $requestStack
    ) {}

    public function getDeliveryModuleOptions(DeliveryModuleOptionEvent $deliveryModuleOptionEvent): void
    {
        $module = ModuleQuery::create()->findOneByCode(LocalPickup::getModuleCode());
        if ($deliveryModuleOptionEvent->getModule()->getId() !== $module?->getId()) {
            return;
        }

        $isValid = true;
        $locale = $this->requestStack->getCurrentRequest()?->getSession()->getLang()->getLocale();

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

    public static function getSubscribedEvents()
    {
        $listenedEvents = [];

        /* Check for old versions of Thelia where the events used by the API didn't exists */
        if (class_exists(DeliveryModuleOptionEvent::class)) {
            $listenedEvents[OpenApiEvents::MODULE_DELIVERY_GET_OPTIONS] = ['getDeliveryModuleOptions', 129];
        }

        return $listenedEvents;
    }
}
