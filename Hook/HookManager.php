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

use LocalPickup\Form\ConfigurationForm;
use LocalPickup\LocalPickup;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Form\TheliaFormFactory;
use Thelia\Core\Hook\BaseHook;
use Thelia\Core\Template\Parser\ParserResolver;

/**
 * Class HookManager.
 *
 * @author Thomas Arnaud <tarnaud@openstudio.fr>
 */
class HookManager extends BaseHook
{
    public function __construct(
        private readonly TheliaFormFactory $formFactory,
        ?EventDispatcherInterface $dispatcher = null,
        ?ParserResolver $parserResolver = null,
    ) {
        parent::__construct($dispatcher, $parserResolver);
    }

    public static function getSubscribedHooks(): array
    {
        return [
            'module.configuration' => [
                ['type' => 'back', 'method' => 'onModuleConfiguration'],
            ],
            'order-invoice.delivery-address' => [
                ['type' => 'front', 'method' => 'onOrderInvoiceDeliveryAddress'],
            ],
            'order-delivery.extra' => [
                ['type' => 'front', 'method' => 'onOrderDeliveryExtra'],
            ],
        ];
    }

    public function onModuleConfiguration(HookRenderEvent $event): void
    {
        $request = $this->getRequest();
        $locale = (null !== $request && $request->hasSession())
            ? $request->getSession()->getAdminEditionLang()->getLocale()
            : (\Thelia\Model\LangQuery::create()->findOneByByDefault(true)?->getLocale() ?? 'en_US');

        $form = $this->formFactory->createForm(ConfigurationForm::getName(), data: [
            'price' => (float) LocalPickup::getConfigValue(LocalPickup::PRICE_VAR_NAME, 0),
            'description' => LocalPickup::getConfigValue(LocalPickup::DESCRIPTION_VAR_NAME, '', $locale),
            'email' => LocalPickup::getConfigValue(LocalPickup::EMAIL_VAR_NAME, '', $locale),
            'sms' => (bool) LocalPickup::getConfigValue(LocalPickup::SMS_VAR_NAME, false),
        ]);

        $form->createView();

        $event->add(
            $this->render(
                'LocalPickup/module_configuration.html.twig',
                ['form' => $form->getView()]
            )
        );
    }

    public function onOrderInvoiceDeliveryAddress(HookRenderEvent $event): void
    {
        // Show the local delivery template if we're the current delivery module.
        $request = $this->getRequest();
        $order = (null !== $request && $request->hasSession()) ? $request->getSession()->getOrder() : null;
        if (null !== $order && $order->getDeliveryModuleId() == LocalPickup::getModuleId()) {
            $event->add(
                $this->render('localpickup/order-invoice-delivery-address.html', [
                    'order_id' => $event->getArgument('order_id'),
                ])
            );
        }
    }

    public function onOrderDeliveryExtra(HookRenderEvent $event): void
    {
        $request = $this->getRequest();
        $locale = (null !== $request && $request->hasSession())
            ? $request->getSession()->getLang()->getLocale()
            : (\Thelia\Model\LangQuery::create()->findOneByByDefault(true)?->getLocale() ?? 'en_US');

        $event->add(
            $this->render(
                'localpickup/delivery-address.html',
                [
                    'description' => LocalPickup::getConfigValue(
                        LocalPickup::DESCRIPTION_VAR_NAME, '',
                        $locale
                    ),
                ]
            )
        );
    }
}
