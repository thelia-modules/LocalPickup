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
            ->add("price","text", array(
                "label"=>Translator::getInstance()->trans("Price", [], LocalPickup::DOMAIN_NAME),
                "label_attr"=>array(
                    "for"=>"pricefield"
                ),
                "constraints"=>array(new NotBlank()),
                "data"=> LocalPickupShippingQuery::create()->getPrice()
            ))
        ;
    }

    /**
     * @return string the name of you form. This name must be unique
     */
    public function getName()
    {
        return "set-delivery-price-localpickup";
    }

}
