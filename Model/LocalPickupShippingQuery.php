<?php

namespace LocalPickup\Model;

use LocalPickup\Model\Base\LocalPickupShippingQuery as BaseLocalPickupShippingQuery;

/**
 * Skeleton subclass for performing query and update operations on the 'local_pickup_shipping' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class LocalPickupShippingQuery extends BaseLocalPickupShippingQuery
{
    /**
     * @return float
     */
    public function getPrice()
    {
        $price = $this->orderById('desc')
            ->findOne()
            ->getPrice();

        return (double) $price;
    }
} // LocalPickupShippingQuery
