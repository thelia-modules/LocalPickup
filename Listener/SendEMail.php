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
 * Class SendEMail
 * @package IciRelais\Listener
 * @author Thelia <info@thelia.net>
 */
class SendEMail extends BaseAction implements EventSubscriberInterface
{
    /**
     * @var MailerFactory
     */
    protected $mailer;

    public function __construct(MailerFactory $mailer)
    {
        $this->mailer = $mailer;
    }

    /*
     * Send a mail to the customer qhen the order is set to the Sent Status.
     *
     * @param OrderEvent $event
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function updateStatus(OrderEvent $event)
    {
        if ($event->getOrder()->getDeliveryModuleId() === LocalPickup::getModuleId()) {
            if ($event->getOrder()->isSent()) {
                $order = $event->getOrder();
                $customer = $order->getCustomer();
                $store = ConfigQuery::create();

                if (null !== $country = CountryQuery::create()->findPk($store->read("store_country"))) {
                    $countryName = $country->setLocale($order->getLang()->getLocale())->getTitle();
                } else {
                    $countryName = '';
                }

                $this->mailer->sendEmailToCustomer(
                    'order_confirmation_localpickup',
                    $customer,
                    [
                        'order_id'  => $order->getId(),
                        'order_ref' => $order->getRef(),
                        'store_name'     => ConfigQuery::read("store_name"),
                        'store_address1' => ConfigQuery::read("store_address1"),
                        'store_address2' => ConfigQuery::read("store_address2"),
                        'store_address3' => ConfigQuery::read("store_address3"),
                        'store_zipcode'  => ConfigQuery::read("store_zipcode"),
                        'store_city'     => ConfigQuery::read("store_city"),
                        'store_country'  => $countryName
                    ]
                );
            }
        }
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return array(
            TheliaEvents::ORDER_UPDATE_STATUS => array("updateStatus", 128)
        );
    }
}
