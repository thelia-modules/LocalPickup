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

namespace LocalPickup\Form;

use LocalPickup\LocalPickup;
use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Form\BaseForm;

class ConfigurationForm extends BaseForm
{
    protected function buildForm()
    {
        $this->formBuilder
            ->add(
                'price',
                'number',
                [
                    'constraints' => [ new NotBlank() ],
                    'data' => LocalPickup::getConfigValue('price', 0),
                    'label' => $this->translator->trans('Delivery price', [], LocalPickup::MODULE_DOMAIN),
                    'label_attr' => [
                        'for' => 'pricefield',
                        'help' => $this->translator->trans('Enter the price for a local pick up', [], LocalPickup::MODULE_DOMAIN),
                    ],
                ]
            )
        ;
    }

    public function getName()
    {
        return "set-delivery-price-localpickup";
    }
}
