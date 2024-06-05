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
/*      email : info@thelia.net */
/*      web : http://www.thelia.net */

/*      This program is free software; you can redistribute it and/or modify */
/*      it under the terms of the GNU General Public License as published by */
/*      the Free Software Foundation; either version 3 of the License */

/*      This program is distributed in the hope that it will be useful, */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the */
/*      GNU General Public License for more details. */

/*      You should have received a copy of the GNU General Public License */
/*	    along with this program. If not, see <http://www.gnu.org/licenses/>. */

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
 * Class LocalAddress.
 *
 * @author Thelia <info@thelia.net>
 *
 * @method int getId()
 */
class LocalAddress extends BaseLoop implements ArraySearchLoopInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildArray()
    {
        $id = $this->getId();

        /** @var \Thelia\Core\HttpFoundation\Session\Session $session */
        $session = $this->requestStack->getCurrentRequest()->getSession();

        $address = AddressQuery::create()
            ->filterByCustomerId($session->getCustomerUser()->getId())
            ->findPk($id);

        if ($address === null) {
            throw new Exception("The requested address doesn't exist");
        }

        /** @var \Thelia\Model\Customer $customer */
        $customer = $session->getCustomerUser();

        return [
            'Id' => 0,
            'Label' => $address->getLabel(),
            'CustomerId' => $address->getCustomerId(),
            'TitleId' => $address->getTitleId(),
            'Company' => ConfigQuery::read('store_name'),
            'Firstname' => $customer->getFirstname(),
            'Lastname' => $customer->getLastname(),
            'Address1' => ConfigQuery::read('store_address1'),
            'Address2' => ConfigQuery::read('store_address2'),
            'Address3' => ConfigQuery::read('store_address3'),
            'Zipcode' => ConfigQuery::read('store_zipcode'),
            'City' => ConfigQuery::read('store_city'),
            'CountryId' => ConfigQuery::read('store_country'),
            'Phone' => $address->getPhone(),
            'Cellphone' => $address->getCellphone(),
            'IsDefault' => 0,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function parseResults(LoopResult $loopResult)
    {
        $address = $loopResult->getResultDataCollection();
        $loopResultRow = new LoopResultRow($address);

        $loopResultRow
            ->set('ID', $address['Id'])
            ->set('LABEL', $address['Label'])
            ->set('CUSTOMER', $address['CustomerId'])
            ->set('TITLE', $address['TitleId'])
            ->set('COMPANY', $address['Company'])
            ->set('FIRSTNAME', $address['Firstname'])
            ->set('LASTNAME', $address['Lastname'])
            ->set('ADDRESS1', $address['Address1'])
            ->set('ADDRESS2', $address['Address2'])
            ->set('ADDRESS3', $address['Address3'])
            ->set('ZIPCODE', $address['Zipcode'])
            ->set('CITY', $address['City'])
            ->set('COUNTRY', $address['CountryId'])
            ->set('PHONE', $address['Phone'])
            ->set('CELLPHONE', $address['Cellphone'])
            ->set('DEFAULT', $address['IsDefault'])
        ;

        $loopResult->addRow($loopResultRow);

        return $loopResult;
    }

    /**
     * {@inheritdoc}
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntTypeArgument('id', null, true)
        );
    }
}
