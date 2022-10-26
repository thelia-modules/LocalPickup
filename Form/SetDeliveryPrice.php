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
use LocalPickup\Model\LocalPickupShippingQuery;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;

/**
 * Class SetDeliveryPrice
 * @package LocalPickup\Form
 */
class SetDeliveryPrice extends BaseForm
{
    protected function buildForm()
    {
        $this->formBuilder
            ->add(
                "price",
                NumberType::class,
                [
                    "label"=>Translator::getInstance()->trans("Price", [], LocalPickup::DOMAIN_NAME),
                    "label_attr"=> [
                        "for"=>"pricefield"
                    ],
                    "constraints"=> [ new NotBlank(), new GreaterThanOrEqual([ 'value' => 0 ]) ]
                ]
            )
        ;
    }

    /**
     * @return string the name of you form. This name must be unique
     */
    public static function getName()
    {
        return "set-delivery-price-localpickup";
    }

}
