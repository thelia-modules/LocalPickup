<?php

namespace LocalPickup\Model\Base;

use \Exception;
use \PDO;
use LocalPickup\Model\LocalPickupShipping as ChildLocalPickupShipping;
use LocalPickup\Model\LocalPickupShippingQuery as ChildLocalPickupShippingQuery;
use LocalPickup\Model\Map\LocalPickupShippingTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'local_pickup_shipping' table.
 *
 *
 *
 * @method     ChildLocalPickupShippingQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildLocalPickupShippingQuery orderByPrice($order = Criteria::ASC) Order by the price column
 * @method     ChildLocalPickupShippingQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildLocalPickupShippingQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     ChildLocalPickupShippingQuery groupById() Group by the id column
 * @method     ChildLocalPickupShippingQuery groupByPrice() Group by the price column
 * @method     ChildLocalPickupShippingQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildLocalPickupShippingQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     ChildLocalPickupShippingQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildLocalPickupShippingQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildLocalPickupShippingQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildLocalPickupShipping findOne(ConnectionInterface $con = null) Return the first ChildLocalPickupShipping matching the query
 * @method     ChildLocalPickupShipping findOneOrCreate(ConnectionInterface $con = null) Return the first ChildLocalPickupShipping matching the query, or a new ChildLocalPickupShipping object populated from the query conditions when no match is found
 *
 * @method     ChildLocalPickupShipping findOneById(int $id) Return the first ChildLocalPickupShipping filtered by the id column
 * @method     ChildLocalPickupShipping findOneByPrice(double $price) Return the first ChildLocalPickupShipping filtered by the price column
 * @method     ChildLocalPickupShipping findOneByCreatedAt(string $created_at) Return the first ChildLocalPickupShipping filtered by the created_at column
 * @method     ChildLocalPickupShipping findOneByUpdatedAt(string $updated_at) Return the first ChildLocalPickupShipping filtered by the updated_at column
 *
 * @method     array findById(int $id) Return ChildLocalPickupShipping objects filtered by the id column
 * @method     array findByPrice(double $price) Return ChildLocalPickupShipping objects filtered by the price column
 * @method     array findByCreatedAt(string $created_at) Return ChildLocalPickupShipping objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return ChildLocalPickupShipping objects filtered by the updated_at column
 *
 */
abstract class LocalPickupShippingQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \LocalPickup\Model\Base\LocalPickupShippingQuery object.
     *
     * @param string $dbName     The database name
     * @param string $modelName  The phpName of a model, e.g. 'Book'
     * @param string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'thelia', $modelName = '\\LocalPickup\\Model\\LocalPickupShipping', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildLocalPickupShippingQuery object.
     *
     * @param string   $modelAlias The alias of a model in the query
     * @param Criteria $criteria   Optional Criteria to build the query from
     *
     * @return ChildLocalPickupShippingQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \LocalPickup\Model\LocalPickupShippingQuery) {
            return $criteria;
        }
        $query = new \LocalPickup\Model\LocalPickupShippingQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed               $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildLocalPickupShipping|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = LocalPickupShippingTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(LocalPickupShippingTableMap::DATABASE_NAME);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param mixed               $key Primary key to use for the query
     * @param ConnectionInterface $con A connection object
     *
     * @return ChildLocalPickupShipping A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, PRICE, CREATED_AT, UPDATED_AT FROM local_pickup_shipping WHERE ID = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            $obj = new ChildLocalPickupShipping();
            $obj->hydrate($row);
            LocalPickupShippingTableMap::addInstanceToPool($obj, (string) $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param mixed               $key Primary key to use for the query
     * @param ConnectionInterface $con A connection object
     *
     * @return ChildLocalPickupShipping|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($dataFetcher);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param array               $keys Primary keys to use for the query
     * @param ConnectionInterface $con  an optional connection object
     *
     * @return ObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param mixed $key Primary key to use for the query
     *
     * @return ChildLocalPickupShippingQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        return $this->addUsingAlias(LocalPickupShippingTableMap::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param array $keys The list of primary key to use for the query
     *
     * @return ChildLocalPickupShippingQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        return $this->addUsingAlias(LocalPickupShippingTableMap::ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id > 12
     * </code>
     *
     * @param mixed  $id         The value to use as filter.
     *                           Use scalar values for equality.
     *                           Use array values for in_array() equivalent.
     *                           Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildLocalPickupShippingQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(LocalPickupShippingTableMap::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(LocalPickupShippingTableMap::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LocalPickupShippingTableMap::ID, $id, $comparison);
    }

    /**
     * Filter the query on the price column
     *
     * Example usage:
     * <code>
     * $query->filterByPrice(1234); // WHERE price = 1234
     * $query->filterByPrice(array(12, 34)); // WHERE price IN (12, 34)
     * $query->filterByPrice(array('min' => 12)); // WHERE price > 12
     * </code>
     *
     * @param mixed  $price      The value to use as filter.
     *                           Use scalar values for equality.
     *                           Use array values for in_array() equivalent.
     *                           Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildLocalPickupShippingQuery The current query, for fluid interface
     */
    public function filterByPrice($price = null, $comparison = null)
    {
        if (is_array($price)) {
            $useMinMax = false;
            if (isset($price['min'])) {
                $this->addUsingAlias(LocalPickupShippingTableMap::PRICE, $price['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($price['max'])) {
                $this->addUsingAlias(LocalPickupShippingTableMap::PRICE, $price['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LocalPickupShippingTableMap::PRICE, $price, $comparison);
    }

    /**
     * Filter the query on the created_at column
     *
     * Example usage:
     * <code>
     * $query->filterByCreatedAt('2011-03-14'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt('now'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt(array('max' => 'yesterday')); // WHERE created_at > '2011-03-13'
     * </code>
     *
     * @param mixed  $createdAt  The value to use as filter.
     *                           Values can be integers (unix timestamps), DateTime objects, or strings.
     *                           Empty strings are treated as NULL.
     *                           Use scalar values for equality.
     *                           Use array values for in_array() equivalent.
     *                           Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildLocalPickupShippingQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(LocalPickupShippingTableMap::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(LocalPickupShippingTableMap::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LocalPickupShippingTableMap::CREATED_AT, $createdAt, $comparison);
    }

    /**
     * Filter the query on the updated_at column
     *
     * Example usage:
     * <code>
     * $query->filterByUpdatedAt('2011-03-14'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt('now'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt(array('max' => 'yesterday')); // WHERE updated_at > '2011-03-13'
     * </code>
     *
     * @param mixed  $updatedAt  The value to use as filter.
     *                           Values can be integers (unix timestamps), DateTime objects, or strings.
     *                           Empty strings are treated as NULL.
     *                           Use scalar values for equality.
     *                           Use array values for in_array() equivalent.
     *                           Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildLocalPickupShippingQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(LocalPickupShippingTableMap::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(LocalPickupShippingTableMap::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LocalPickupShippingTableMap::UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param ChildLocalPickupShipping $localPickupShipping Object to remove from the list of results
     *
     * @return ChildLocalPickupShippingQuery The current query, for fluid interface
     */
    public function prune($localPickupShipping = null)
    {
        if ($localPickupShipping) {
            $this->addUsingAlias(LocalPickupShippingTableMap::ID, $localPickupShipping->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the local_pickup_shipping table.
     *
     * @param  ConnectionInterface $con the connection to use
     * @return int                 The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(LocalPickupShippingTableMap::DATABASE_NAME);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            LocalPickupShippingTableMap::clearInstancePool();
            LocalPickupShippingTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildLocalPickupShipping or Criteria object OR a primary key value.
     *
     * @param  mixed               $values Criteria or ChildLocalPickupShipping object or primary key or array of primary keys
     *                                     which is used to create the DELETE statement
     * @param  ConnectionInterface $con    the connection to use
     * @return int                 The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                                    if supported by native driver or if emulated using Propel.
     * @throws PropelException     Any exceptions caught during processing will be
     *                                    rethrown wrapped into a PropelException.
     */
     public function delete(ConnectionInterface $con = null)
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(LocalPickupShippingTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(LocalPickupShippingTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();

        LocalPickupShippingTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            LocalPickupShippingTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param int $nbDays Maximum age of the latest update in days
     *
     * @return ChildLocalPickupShippingQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(LocalPickupShippingTableMap::UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Filter by the latest created
     *
     * @param int $nbDays Maximum age of in days
     *
     * @return ChildLocalPickupShippingQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(LocalPickupShippingTableMap::CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return ChildLocalPickupShippingQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(LocalPickupShippingTableMap::UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return ChildLocalPickupShippingQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(LocalPickupShippingTableMap::UPDATED_AT);
    }

    /**
     * Order by create date desc
     *
     * @return ChildLocalPickupShippingQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(LocalPickupShippingTableMap::CREATED_AT);
    }

    /**
     * Order by create date asc
     *
     * @return ChildLocalPickupShippingQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(LocalPickupShippingTableMap::CREATED_AT);
    }

} // LocalPickupShippingQuery
