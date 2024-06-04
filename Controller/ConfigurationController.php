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

namespace LocalPickup\Controller;

use LocalPickup\Form\ConfigurationForm;
use LocalPickup\LocalPickup;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Tools\URL;

/**
 * Class ConfigurationController.
 *
 * @author Thelia <info@thelia.net>
 */
class ConfigurationController extends BaseAdminController
{
    public function configure()
    {
        if (null !== $response = $this->checkAuth([AdminResources::MODULE], ['LocalPickup'], AccessManager::UPDATE)) {
            return $response;
        }

        $form = $this->createForm(ConfigurationForm::getName());
        $errmes = $ex = null;

        try {
            $vform = $this->validateForm($form);

            $price = $vform->get('price')->getData();
            $description = $vform->get('description')->getData();
            $email = $vform->get('email')->getData();

            LocalPickup::setConfigValue(LocalPickup::PRICE_VAR_NAME, (float) $price);
            LocalPickup::setConfigValue(LocalPickup::DESCRIPTION_VAR_NAME, $description, $this->getCurrentEditionLocale());
            LocalPickup::setConfigValue(LocalPickup::EMAIL_VAR_NAME, $email, $this->getCurrentEditionLocale());
        } catch (FormValidationException $ex) {
            $errmes = $this->createStandardFormValidationErrorMessage($ex);
        } catch (\Exception $ex) {
            $errmes = $ex->getMessage();
        }

        if (null !== $errmes && null !== $ex) {
            $this->setupFormErrorContext(
                'configuration',
                $errmes,
                $form,
                $ex
            );
        }

        return new RedirectResponse(URL::getInstance()->absoluteUrl('/admin/module/LocalPickup'));
    }
}
