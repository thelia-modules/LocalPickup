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

/*      Copyright (c) OpenStudio */
/*      email : dev@thelia.net */
/*      web : http://www.thelia.net */

/*      For the full copyright and license information, please view the LICENSE.txt */
/*      file that was distributed with this source code. */

namespace LocalPickup\Hook;

use LocalPickup\LocalPickup;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;

/**
 * Class HookManager.
 *
 * @author Thomas Arnaud <tarnaud@openstudio.fr>
 */
class HookManager extends BaseHook
{
    public function onModuleConfiguration(HookRenderEvent $event): void
    {
        $locale = $this->getSession()->getAdminEditionLang()->getLocale();

        $event->add(
            $this->render(
                'module_configuration.html',
                [
                    'price' => (float) LocalPickup::getConfigValue(LocalPickup::PRICE_VAR_NAME, 0),
                    'description' => LocalPickup::getConfigValue(LocalPickup::DESCRIPTION_VAR_NAME, '', $locale),
                ]
            )
        );
    }

    public function onOrderInvoiceDeliveryAddress(HookRenderEvent $event): void
    {
        // Show the local delivery template if we're the current delivery module.
        if ((null !== $order = $this->getSession()->getOrder()) && $order->getDeliveryModuleId() == LocalPickup::getModuleId()) {
            $event->add(
                $this->render('localpickup/order-invoice-delivery-address.html', [
                    'order_id' => $event->getArgument('order_id'),
                ])
            );
        }
    }

    public function onOrderDeliveryExtra(HookRenderEvent $event): void
    {
        $event->add(
            $this->render(
                'localpickup/delivery-address.html',
                [
                    'description' => LocalPickup::getConfigValue(
                        LocalPickup::DESCRIPTION_VAR_NAME, '',
                        $this->getSession()->getLang()->getLocale()
                    ),
                ]
            )
        );
    }
}
