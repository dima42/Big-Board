<?php

namespace Base;

use \Note as ChildNote;
use \NoteQuery as ChildNoteQuery;
use \Exception;
use \PDO;
use Map\NoteTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'note' table.
 *
 *
 *
 * @method     ChildNoteQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildNoteQuery orderByBody($order = Criteria::ASC) Order by the body column
 * @method     ChildNoteQuery orderByPuzzleId($order = Criteria::ASC) Order by the puzzle_id column
 * @method     ChildNoteQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildNoteQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     ChildNoteQuery groupById() Group by the id column
 * @method     ChildNoteQuery groupByBody() Group by the body column
 * @method     ChildNoteQuery groupByPuzzleId() Group by the puzzle_id column
 * @method     ChildNoteQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildNoteQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     ChildNoteQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildNoteQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildNoteQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildNoteQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildNoteQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildNoteQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildNoteQuery leftJoinPuzzle($relationAlias = null) Adds a LEFT JOIN clause to the query using the Puzzle relation
 * @method     ChildNoteQuery rightJoinPuzzle($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Puzzle relation
 * @method     ChildNoteQuery innerJoinPuzzle($relationAlias = null) Adds a INNER JOIN clause to the query using the Puzzle relation
 *
 * @method     ChildNoteQuery joinWithPuzzle($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Puzzle relation
 *
 * @method     ChildNoteQuery leftJoinWithPuzzle() Adds a LEFT JOIN clause and with to the query using the Puzzle relation
 * @method     ChildNoteQuery rightJoinWithPuzzle() Adds a RIGHT JOIN clause and with to the query using the Puzzle relation
 * @method     ChildNoteQuery innerJoinWithPuzzle() Adds a INNER JOIN clause and with to the query using the Puzzle relation
 *
 * @method     \PuzzleQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildNote findOne(ConnectionInterface $con = null) Return the first ChildNote matching the query
 * @method     ChildNote findOneOrCreate(ConnectionInterface $con = null) Return the first ChildNote matching the query, or a new ChildNote object populated from the query conditions when no match is found
 *
 * @method     ChildNote findOneById(int $id) Return the first ChildNote filtered by the id column
 * @method     ChildNote findOneByBody(string $body) Return the first ChildNote filtered by the body column
 * @method     ChildNote findOneByPuzzleId(int $puzzle_id) Return the first ChildNote filtered by the puzzle_id column
 * @method     ChildNote findOneByCreatedAt(string $created_at) Return the first ChildNote filtered by the created_at column
 * @method     ChildNote findOneByUpdatedAt(string $updated_at) Return the first ChildNote filtered by the updated_at column *

 * @method     ChildNote requirePk($key, ConnectionInterface $con = null) Return the ChildNote by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildNote requireOne(ConnectionInterface $con = null) Return the first ChildNote matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildNote requireOneById(int $id) Return the first ChildNote filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildNote requireOneByBody(string $body) Return the first ChildNote filtered by the body column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildNote requireOneByPuzzleId(int $puzzle_id) Return the first ChildNote filtered by the puzzle_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildNote requireOneByCreatedAt(string $created_at) Return the first ChildNote filtered by the created_at column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildNote requireOneByUpdatedAt(string $updated_at) Return the first ChildNote filtered by the updated_at column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildNote[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildNote objects based on current ModelCriteria
 * @method     ChildNote[]|ObjectCollection findById(int $id) Return ChildNote objects filtered by the id column
 * @method     ChildNote[]|ObjectCollection findByBody(string $body) Return ChildNote objects filtered by the body column
 * @method     ChildNote[]|ObjectCollection findByPuzzleId(int $puzzle_id) Return ChildNote objects filtered by the puzzle_id column
 * @method     ChildNote[]|ObjectCollection findByCreatedAt(string $created_at) Return ChildNote objects filtered by the created_at column
 * @method     ChildNote[]|ObjectCollection findByUpdatedAt(string $updated_at) Return ChildNote objects filtered by the updated_at column
 * @method     ChildNote[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class NoteQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \Base\NoteQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'palindrome', $modelName = '\\Note', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildNoteQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildNoteQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildNoteQuery) {
            return $criteria;
        }
        $query = new ChildNoteQuery();
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
     * @param mixed $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildNote|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(NoteTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = NoteTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key)))) {
            // the object is already in the instance pool
            return $obj;
        }

        return $this->findPkSimple($key, $con);
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildNote A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, body, puzzle_id, created_at, updated_at FROM note WHERE id = :p0';
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
            /** @var ChildNote $obj */
            $obj = new ChildNote();
            $obj->hydrate($row);
            NoteTableMap::addInstanceToPool($obj, null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return ChildNote|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, ConnectionInterface $con)
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
     * @param     array $keys Primary keys to use for the query
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, ConnectionInterface $con = null)
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
     * @param     mixed $key Primary key to use for the query
     *
     * @return $this|ChildNoteQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(NoteTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildNoteQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(NoteTableMap::COL_ID, $keys, Criteria::IN);
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
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildNoteQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(NoteTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(NoteTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(NoteTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the body column
     *
     * Example usage:
     * <code>
     * $query->filterByBody('fooValue');   // WHERE body = 'fooValue'
     * $query->filterByBody('%fooValue%', Criteria::LIKE); // WHERE body LIKE '%fooValue%'
     * </code>
     *
     * @param     string $body The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildNoteQuery The current query, for fluid interface
     */
    public function filterByBody($body = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($body)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(NoteTableMap::COL_BODY, $body, $comparison);
    }

    /**
     * Filter the query on the puzzle_id column
     *
     * Example usage:
     * <code>
     * $query->filterByPuzzleId(1234); // WHERE puzzle_id = 1234
     * $query->filterByPuzzleId(array(12, 34)); // WHERE puzzle_id IN (12, 34)
     * $query->filterByPuzzleId(array('min' => 12)); // WHERE puzzle_id > 12
     * </code>
     *
     * @see       filterByPuzzle()
     *
     * @param     mixed $puzzleId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildNoteQuery The current query, for fluid interface
     */
    public function filterByPuzzleId($puzzleId = null, $comparison = null)
    {
        if (is_array($puzzleId)) {
            $useMinMax = false;
            if (isset($puzzleId['min'])) {
                $this->addUsingAlias(NoteTableMap::COL_PUZZLE_ID, $puzzleId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($puzzleId['max'])) {
                $this->addUsingAlias(NoteTableMap::COL_PUZZLE_ID, $puzzleId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(NoteTableMap::COL_PUZZLE_ID, $puzzleId, $comparison);
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
     * @param     mixed $createdAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildNoteQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(NoteTableMap::COL_CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(NoteTableMap::COL_CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(NoteTableMap::COL_CREATED_AT, $createdAt, $comparison);
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
     * @param     mixed $updatedAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildNoteQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(NoteTableMap::COL_UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(NoteTableMap::COL_UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(NoteTableMap::COL_UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related \Puzzle object
     *
     * @param \Puzzle|ObjectCollection $puzzle The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildNoteQuery The current query, for fluid interface
     */
    public function filterByPuzzle($puzzle, $comparison = null)
    {
        if ($puzzle instanceof \Puzzle) {
            return $this
                ->addUsingAlias(NoteTableMap::COL_PUZZLE_ID, $puzzle->getId(), $comparison);
        } elseif ($puzzle instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(NoteTableMap::COL_PUZZLE_ID, $puzzle->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByPuzzle() only accepts arguments of type \Puzzle or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Puzzle relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildNoteQuery The current query, for fluid interface
     */
    public function joinPuzzle($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Puzzle');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Puzzle');
        }

        return $this;
    }

    /**
     * Use the Puzzle relation Puzzle object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \PuzzleQuery A secondary query class using the current class as primary query
     */
    public function usePuzzleQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinPuzzle($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Puzzle', '\PuzzleQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildNote $note Object to remove from the list of results
     *
     * @return $this|ChildNoteQuery The current query, for fluid interface
     */
    public function prune($note = null)
    {
        if ($note) {
            $this->addUsingAlias(NoteTableMap::COL_ID, $note->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the note table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(NoteTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            NoteTableMap::clearInstancePool();
            NoteTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    /**
     * Performs a DELETE on the database based on the current ModelCriteria
     *
     * @param ConnectionInterface $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public function delete(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(NoteTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(NoteTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            NoteTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            NoteTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     $this|ChildNoteQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(NoteTableMap::COL_UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     $this|ChildNoteQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(NoteTableMap::COL_UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return     $this|ChildNoteQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(NoteTableMap::COL_UPDATED_AT);
    }

    /**
     * Order by create date desc
     *
     * @return     $this|ChildNoteQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(NoteTableMap::COL_CREATED_AT);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     $this|ChildNoteQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(NoteTableMap::COL_CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by create date asc
     *
     * @return     $this|ChildNoteQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(NoteTableMap::COL_CREATED_AT);
    }

} // NoteQuery
