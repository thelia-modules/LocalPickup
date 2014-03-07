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
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Translation\Translator;
use Thelia\Model\Resource;

/**
 * Class SetDeliveryPrice
 * @package LocalPickup\Controller
 * @author Thelia <info@thelia.net>
 */
class SetDeliveryPrice extends BaseAdminController
{
    public function configure()
    {
        if (null !== $response = $this->checkAuth(array(AdminResources::MODULE), array('LocalPickup'), AccessManager::UPDATE)) {
            return $response;
        }

        $form = new \LocalPickup\Form\SetDeliveryPrice($this->getRequest());
        $errmes="";
        try {
            $vform = $this->validateForm($form);

            $price = $vform->get('price')->getData();

            if (preg_match("#^\d\.?\d*$#",$price)) {
                $newprice = new LocalPickupShipping();
                $newprice->setPrice((float) $price)
                    ->save();
            } else {
                $errmes = Translator::getInstance()->trans("price must be a number !");
            }
        } catch (\Exception $e) {
            $errmes =  $e->getMessage();
        }

        $this->redirectToRoute("admin.module.configure",array("errmes"=>$errmes),
            array ( 'module_code'=>"LocalPickup",
                '_controller' => 'Thelia\\Controller\\Admin\\ModuleController::configureAction'));
    }
}
