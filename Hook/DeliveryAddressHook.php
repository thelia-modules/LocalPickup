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

use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;


/**
 * Class CarouselHook
 * @package Carousel\Hook
 * @author  Franck Allimant <franck@cqfdev.fr>
 */
class DeliveryAddressHook extends BaseHook
{

    public function displayLocalPickupAddress(HookRenderEvent $event)
    {
        $event->add(
            $this->render('pickup-address.html')
        );
    }
} 