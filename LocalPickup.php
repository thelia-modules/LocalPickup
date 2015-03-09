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

namespace LocalPickup;

use Propel\Runtime\Connection\ConnectionInterface;
use Thelia\Install\Database;
use Thelia\Model\Country;
use Thelia\Module\AbstractDeliveryModule;

/**
 * Class LocalPickup
 * @package LocalPickup
 * @author Thelia <info@thelia.net>
 */
class LocalPickup extends AbstractDeliveryModule
{
    const MODULE_DOMAIN = 'localpickup';

    /**
     * calculate and return delivery price
     *
     * @param Country $country
     *
     * @return double
     */
    public function getPostage(Country $country)
    {
        return $this->getConfigValue('price', 0);
    }

    public function isValidDelivery(Country $country)
    {
        return true;
    }

    public function postActivation(ConnectionInterface $con = null)
    {
        $database = new Database($con->getWrappedConnection());

        $database->insertSql(null, array(__DIR__."/Config/thelia.sql"));
    }
}