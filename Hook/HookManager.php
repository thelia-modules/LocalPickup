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

namespace LocalPickup\Hook;

use LocalPickup\LocalPickup;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;

/**
 * Class HookManager
 * @package LocalPickup\Hook
 * @author Thomas Arnaud <tarnaud@openstudio.fr>
 */
class HookManager extends BaseHook
{
    public function onModuleConfiguration(HookRenderEvent $event)
    {
        $locale = $this->getSession()->getAdminEditionLang()->getLocale();

        $event->add(
            $this->render(
                "module_configuration.html",
                [
                    'price' => (float)LocalPickup::getConfigValue(LocalPickup::PRICE_VAR_NAME, 0),
                    'description' => LocalPickup::getConfigValue(LocalPickup::DESCRIPTION_VAR_NAME, '', $locale)
                ]
            )
        );
    }

    public function onOrderInvoiceDeliveryAddress(HookRenderEvent $event)
    {
        // Show the local delivery template if we're the current delivery module.
        if ((null !== $order = $this->getSession()->getOrder()) && $order->getDeliveryModuleId() == LocalPickup::getModuleId()) {
            $event->add(
                $this->render("localpickup/order-invoice-delivery-address.html", [
                    'order_id' => $event->getArgument('order_id'),
                ])
            );
        }
    }
}
