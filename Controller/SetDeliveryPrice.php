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

namespace LocalPickup\Controller;

use LocalPickup\Model\LocalPickupShipping;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Translation\Translator;
use Thelia\Tools\URL;

/**
 * Class SetDeliveryPrice
 * @package LocalPickup\Controller
 * @author Thelia <info@thelia.net>
 * @contributor Thomas Arnaud <tarnaud@openstudio.fr>
 */
class SetDeliveryPrice extends BaseAdminController
{
    public function configure()
    {
        if (null !== $response = $this->checkAuth(array(AdminResources::MODULE), array('LocalPickup'), AccessManager::UPDATE)) {
            return $response;
        }

        $form = $this->createForm('localpickup.form');
        $errmes=null;

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

            return $this->redirectToConfigurationPage();

        } catch (\Exception $e) {
            $errmes = $this->createStandardFormValidationErrorMessage($e);
        }

        if (null !== $errmes) {
            $this->setupFormErrorContext(
                'configuration',
                $errmes,
                $form
            );

            $response = $this->render("module-configure", ['module_code' => 'LocalPickup']);
        }

        return $response;
    }

    /**
     * Redirect to the configuration page
     */
    protected function redirectToConfigurationPage()
    {
        return RedirectResponse::create(URL::getInstance()->absoluteUrl('/admin/module/LocalPickup'));
    }
}
