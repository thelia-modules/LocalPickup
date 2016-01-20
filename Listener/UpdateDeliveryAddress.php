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
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Action\BaseAction;
use Thelia\Core\Event\Order\OrderAddressEvent;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Model\OrderAddressQuery;
use Thelia\Model\ConfigQuery;

/**
 * Class UpdateDeliveryAddress
 * @package LocalPickup\Listener
 * @contributor Thomas Arnaud <tarnaud@openstudio.fr>
 */
class UpdateDeliveryAddress extends BaseAction implements EventSubscriberInterface
{
    /**
     * @param  OrderEvent $event
     * @throws \Exception
     */
    public function update_address(OrderEvent $event)
    {
        if ($event->getOrder()->getDeliveryModuleId() === LocalPickup::getModCode()) {
            $address_id = $event->getOrder()->getDeliveryOrderAddressId();
            $address = OrderAddressQuery::create()->findPk($address_id);

            if ($address !== null) {
                $config = new ConfigQuery();
                $address1 = $config->read("store_address1");
                $address2 = $config->read("store_address2");
                $address3 = $config->read("store_address3");
                $zipcode = $config->read("store_zipcode");
                $city = $config->read("store_city");
                $country = $config->read("store_country");
                $name = $config->read("store_name");

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

                    $event->getDispatcher()->dispatch(TheliaEvents::ORDER_UPDATE_ADDRESS, $address_event);
                }
            } else {
                throw new \Exception("Error: order deliery address doesn't exists");
            }
        }
    }

    public function set_address(OrderEvent $event)
    {
        if ($event->getOrder()->getDeliveryModuleId() === LocalPickup::getModCode()) {
            $event->setDeliveryAddress(null);
        }
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            TheliaEvents::ORDER_BEFORE_PAYMENT=>array("update_address", 130),
            TheliaEvents::ORDER_SET_DELIVERY_MODULE=>array("set_address", 128)
        );
    }
}
