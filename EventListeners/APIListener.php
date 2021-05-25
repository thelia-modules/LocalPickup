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
use Thelia\Model\ModuleQuery;

class APIListener implements EventSubscriberInterface
{
    /** @var ModelFactory  */
    protected $modelFactory;

    /** @var RequestStack  */
    protected $requestStack;

    /**
     * APIListener constructor.
     * @param ContainerInterface $container We need the container because we use a service from another module
     * which is not mandatory, and using its service without it being installed will crash
     */
    public function __construct(ModelFactory $modelFactory, RequestStack $requestStack)
    {
        $this->modelFactory = $modelFactory;
        $this->requestStack = $requestStack;
    }

    public function getDeliveryModuleOptions(DeliveryModuleOptionEvent $deliveryModuleOptionEvent)
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

        $title = $module->setLocale($this->requestStack->getCurrentRequest()->getSession()->getLang()->getLocale())->getTitle();

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