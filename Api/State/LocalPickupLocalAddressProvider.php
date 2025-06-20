<?php

namespace LocalPickup\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use LocalPickup\Api\Resource\LocalPickupLocalAddressResource;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Model\AddressQuery;
use Thelia\Model\ConfigQuery;
use Symfony\Component\Config\Definition\Exception\Exception;

class LocalPickupLocalAddressProvider implements ProviderInterface
{
    /**
     * @param RequestStack $requestStack
     */
    public function __construct(private RequestStack $requestStack) {}

    /**
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     * @return LocalPickupLocalAddressResource|null
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?LocalPickupLocalAddressResource
    {
        $request = $this->requestStack->getCurrentRequest();
        $session = $request?->getSession();

        if (!isset($uriVariables['id'])) {
            return null;
        }

        $customer = $session?->getCustomerUser();
        if (!$customer) {
            throw new Exception("No customer in session");
        }

        $address = AddressQuery::create()
            ->filterByCustomerId($customer->getId())
            ->findPk($uriVariables['id']);

        if (null === $address) {
            throw new Exception("The requested address doesn't exist");
        }

        $resource = new LocalPickupLocalAddressResource();
        $resource->id        = 0;
        $resource->label     = $address->getLabel();
        $resource->customer  = $address->getCustomerId();
        $resource->title     = $address->getTitleId();
        $resource->company   = ConfigQuery::read('store_name');
        $resource->firstname = $customer->getFirstname();
        $resource->lastname  = $customer->getLastname();
        $resource->address1  = ConfigQuery::read('store_address1');
        $resource->address2  = ConfigQuery::read('store_address2');
        $resource->address3  = ConfigQuery::read('store_address3');
        $resource->zipcode   = ConfigQuery::read('store_zipcode');
        $resource->city      = ConfigQuery::read('store_city');
        $resource->country   = ConfigQuery::read('store_country');
        $resource->phone     = $address->getPhone();
        $resource->cellphone = $address->getCellphone();
        $resource->default   = 0;

        return $resource;
    }
}
