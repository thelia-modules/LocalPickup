<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace LocalPickup\Listener;

use LocalPickup\LocalPickup;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Action\BaseAction;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Mailer\MailerFactory;
use Thelia\Core\Template\ParserInterface;
use Thelia\Model\ConfigQuery;
use Thelia\Model\CountryI18nQuery;
use Thelia\Model\CountryQuery;
use Thelia\Model\MessageQuery;
/**
 * Class SendEmailNotification
 * @package IciRelais\Listener
 * @author Thelia <info@thelia.net>
 */
class SendEmailNotification extends BaseAction implements EventSubscriberInterface
{

    /**
     * @var MailerFactory
     */
    protected $mailer;

    public function __construct(MailerFactory $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @return \Thelia\Mailer\MailerFactory
     */
    public function getMailer()
    {
        return $this->mailer;
    }

    public function update_status(OrderEvent $event)
    {
        if ($event->getOrder()->getDeliveryModuleId() === LocalPickup::getModuleId()) {

            if ($event->getOrder()->isSent()) {

                $order = $event->getOrder();
                $customer = $order->getCustomer();
                $store = ConfigQuery::create();

                $country = CountryQuery::create()->findPk($store->read("store_country"));

                $country = CountryI18nQuery::create()
                    ->filterById($country->getId())
                    ->findOneByLocale($order->getLang()->getLocale())
                    ->getTitle();

                $this->mailer->sendEmailToCustomer(
                    'order_confirmation_localpickup',
                    $customer,
                    [
                        'order_id'    => $order->getId(),
                        'order_ref'   => $order->getRef(),
                        'order_date'  => $order->getCreatedAt(),
                        'update_date' => $order->getUpdatedAt(),
                        'package'     => $order->getDeliveryRef(),
                        'customer_id' => $customer->getId(),
                        'store_name' => $store->read("store_name"),
                        'store_address1' => $store->read("store_address1"),
                        'store_address2' => $store->read("store_address2"),
                        'store_address3' => $store->read("store_address3"),
                        'store_zipcode' => $store->read("store_zipcode"),
                        'store_city' => $store->read("store_city"),
                        'store_country' => $country
                    ]
                );
            }
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
            TheliaEvents::ORDER_UPDATE_STATUS => array("update_status", 128)
        );
    }

}
