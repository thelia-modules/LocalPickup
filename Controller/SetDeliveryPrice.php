<?php
/**
 * Created by PhpStorm.
 * User: benjamin
 * Date: 17/02/14
 * Time: 10:32
 */

namespace LocalPickup\Controller;


use LocalPickup\Model\LocalPickupShipping;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Tools\URL;

/**
 * Class SetDeliveryPrice
 * @package LocalPickup\Controller
 * @author Thelia <info@thelia.net>
 */
class SetDeliveryPrice extends BaseAdminController {

    public function configure() {
        $form = new \LocalPickup\Form\SetDeliveryPrice($this->getRequest());

        try {
            $vform = $this->validateForm($form);

            $price = $vform->get('price')->getData();

            if(preg_match("#^\d\.?\d*$#",$price)) {
                $newprice = new LocalPickupShipping();
                $newprice->setPrice((float)$price)
                    ->save();
            }
        } catch(\Exception $e) {}


        $this->redirectToRoute("admin.module.configure",array(),
            array ( 'module_code'=>"LocalPickup",
                '_controller' => 'Thelia\\Controller\\Admin\\ModuleController::configureAction'));
    }
} 