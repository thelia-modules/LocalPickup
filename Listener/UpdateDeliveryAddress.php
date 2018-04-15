<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : info@thelia.net                                                      */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 3 of the License                */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*	    along with this program. If not, see <http://www.gnu.org/licenses/>.         */
/*                                                                                   */
/*************************************************************************************/

namespace LocalPickup\Listener;

use LocalPickup\LocalPickup;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Action\BaseAction;
use Thelia\Core\Event\Order\OrderAddressEvent;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Model\ConfigQuery;
use Thelia\Model\OrderAddressQuery;

/**
 * Class UpdateDeliveryAddress
 * @package LocalPickup\Listener
 * @contributor Thomas Arnaud <tarnaud@openstudio.fr>
 */
class UpdateDeliveryAddress extends BaseAction implements EventSubscriberInterface
{
    /**
     * @param OrderEvent $event
     * @param $eventName
     * @param EventDispatcherInterface $dispatcher
     * @throws \Exception
     */
    public function updateAddress(OrderEvent $event, $eventName, EventDispatcherInterface $dispatcher)
    {
        if ($event->getOrder()->getDeliveryModuleId() === LocalPickup::getModuleId()) {
            $address_id = $event->getOrder()->getDeliveryOrderAddressId();
            $address = OrderAddressQuery::create()->findPk($address_id);

            if ($address !== null) {
                $address1 = ConfigQuery::read("store_address1");
                $address2 = ConfigQuery::read("store_address2");
                $address3 = ConfigQuery::read("store_address3");
                $zipcode  = ConfigQuery::read("store_zipcode");
                $city     = ConfigQuery::read("store_city");
                $country  = ConfigQuery::read("store_country");
                $name     = ConfigQuery::read("store_name");

                if ($address1 !== null && $zipcode !== null && $city !== null && $country !== null) {
                    $address_event = new OrderAddressEvent(
                        $address->getCustomerTitleId(),
                        $address->getFirstname(),
                        $address->getLastname(),
                        $address1,
                        $address2,
                        $address3,
                        $zipcode,
                        $city,
                        $country,
                        $address->getPhone(),
                        $name,
                        $address->getCellphone()
                    );

                    $address_event->setOrderAddress($address);

                    $dispatcher->dispatch(TheliaEvents::ORDER_UPDATE_ADDRESS, $address_event);
                }
            } else {
                throw new \Exception("Error: order deliery address doesn't exists");
            }
        }
    }

    public function setAddress(OrderEvent $event)
    {
        if ($event->getOrder()->getDeliveryModuleId() === LocalPickup::getModuleId()) {
            $event->setDeliveryAddress(null);
        }
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return array(
            TheliaEvents::ORDER_BEFORE_PAYMENT=> ["updateAddress", 130],
            TheliaEvents::ORDER_SET_DELIVERY_MODULE=> ["setAddress", 128]
        );
    }
}
