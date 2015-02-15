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

namespace LocalPickup\Loop;

use Symfony\Component\Config\Definition\Exception\Exception;
use Thelia\Core\Template\Element\ArraySearchLoopInterface;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Model\AddressQuery;
use Thelia\Model\ConfigQuery;

/**
 * Class LocalAddress
 * @package LocalPickup\Loop
 * @author Thelia <info@thelia.net>
 */
class LocalAddress extends BaseLoop implements ArraySearchLoopInterface
{
    /**
     * this method returns an array
     *
     * @return array
     */
    public function buildArray()
    {
        return [ 1 ];
    }

    /**
     * @param LoopResult $loopResult
     *
     * @return LoopResult
     */
    public function parseResults(LoopResult $loopResult)
    {
        $id = $this->getId();

        /** @var \Thelia\Model\Customer $customer */
        $customer = $this->request->getSession()->getCustomerUser();

        /** @var \Thelia\Core\HttpFoundation\Session\Session $session */
        $address = AddressQuery::create()
            ->filterByCustomerId($customer->getId())
            ->findPk($id);

        if ($address === null) {
            throw new Exception("The requested address doesn't exist");
        }

        $loopResultRow = new LoopResultRow($address);

        $loopResultRow
            ->set("ID", 0) // This address is synthetic, and  not stored in the DB
            ->set("LABEL", $address->getLabel())
            ->set("CUSTOMER", $address->getLabel())
            ->set("TITLE", $address->getTitleId())
            ->set("COMPANY", ConfigQuery::read('store_name'))
            ->set("FIRSTNAME", $customer->getFirstname())
            ->set("LASTNAME", $customer->getLastname())
            ->set("ADDRESS1", ConfigQuery::read('store_address1'))
            ->set("ADDRESS2", ConfigQuery::read('store_address2'))
            ->set("ADDRESS3", ConfigQuery::read('store_address3'))
            ->set("ZIPCODE", ConfigQuery::read('store_zipcode'))
            ->set("CITY", ConfigQuery::read('store_city'))
            ->set("COUNTRY", ConfigQuery::read('store_country'))
            ->set("PHONE", $address->getPhone())
            ->set("CELLPHONE", $address->getCellphone())
            ->set("DEFAULT", false)
        ;

        $loopResult->addRow($loopResultRow);

        return $loopResult;
    }

    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntTypeArgument('id',null,true)
        );
    }
}
