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

use LocalPickup\Form\ConfigurationForm;
use LocalPickup\LocalPickup;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Tools\URL;

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

        $form = new ConfigurationForm($this->getRequest());

        $errmes = "";

        try {
            $vform = $this->validateForm($form);

            $price = $vform->get('price')->getData();

            LocalPickup::setConfigValue('price', $price);

        } catch (\Exception $e) {
            $errmes =  $e->getMessage();
        }

        return RedirectResponse::create(
            URL::getInstance()->absoluteUrl(
                '/admin/module/LocalPickup',
                [ "errmes" => $errmes ]
            )
        );
    }
}
