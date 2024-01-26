<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : info@thelia.net                                                      */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 3 of the License                */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*	    along with this program. If not, see <http://www.gnu.org/licenses/>.         */
/*                                                                                   */
/*************************************************************************************/

namespace LocalPickup;

use Propel\Runtime\Connection\ConnectionInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator;
use Thelia\Install\Database;
use Thelia\Model\Country;
use Thelia\Model\State;
use Thelia\Module\AbstractDeliveryModuleWithState;

/**
 * Class LocalPickup
 * @package LocalPickup
 * @author Thelia <info@thelia.net>
 */
class LocalPickup extends AbstractDeliveryModuleWithState
{
    const DOMAIN_NAME = 'localpickup';

    const PRICE_VAR_NAME = 'price';
    const DESCRIPTION_VAR_NAME = 'description';

    /**
     * @inheritdoc
     */
    public function getPostage(Country $country, State $state = null)
    {
        return $this->buildOrderPostage(self::getConfigValue(self::PRICE_VAR_NAME, 0), $country, $this->getRequest()->getSession()->getLang()->getLocale());
    }

    public function update($currentVersion, $newVersion, ConnectionInterface $con = null): void
    {
        if ($newVersion === '1.2') {
            $db = new Database($con);

            // Migrate previous price from database to module config
            try {
                $statement = $db->execute("select price from local_pickup_shipping order by id desc limit 1");

                $price = (float)$statement->fetchColumn(0);

                self::setConfigValue(self::PRICE_VAR_NAME, $price);
            } catch (\Exception $ex) {
                // Nothing special
            }
        }
    }


    /**
     * @inheritdoc
     */
    public function isValidDelivery(Country $country, State $state = null)
    {
        return true;
    }
    
    public function getDeliveryMode()
    {
        return "localPickup";
    }

    public static function configureServices(ServicesConfigurator $servicesConfigurator): void
    {
        $servicesConfigurator->load(self::getModuleCode().'\\', __DIR__)
            ->exclude([THELIA_MODULE_DIR . ucfirst(self::getModuleCode()). "/I18n/*"])
            ->autowire(true)
            ->autoconfigure(true);
    }
}
