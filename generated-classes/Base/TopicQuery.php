<?php

namespace Base;

use \Topic as ChildTopic;
use \TopicQuery as ChildTopicQuery;
use \Exception;
use \PDO;
use Map\TopicTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\TableMap;

/**
 * Base class that represents a query for the 'topic' table.
 *
 *
 *
 * @method     ChildTopicQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildTopicQuery orderByTitle($order = Criteria::ASC) Order by the title column
 * @method     ChildTopicQuery orderBySlackChannel($order = Criteria::ASC) Order by the slack_channel column
 * @method     ChildTopicQuery orderBySlackChannelId($order = Criteria::ASC) Order by the slack_channel_id column
 * @method     ChildTopicQuery orderByTreeLeft($order = Criteria::ASC) Order by the tree_left column
 * @method     ChildTopicQuery orderByTreeRight($order = Criteria::ASC) Order by the tree_right column
 * @method     ChildTopicQuery orderByTreeLevel($order = Criteria::ASC) Order by the tree_level column
 * @method     ChildTopicQuery orderByTreeScope($order = Criteria::ASC) Order by the tree_scope column
 *
 * @method     ChildTopicQuery groupById() Group by the id column
 * @method     ChildTopicQuery groupByTitle() Group by the title column
 * @method     ChildTopicQuery groupBySlackChannel() Group by the slack_channel column
 * @method     ChildTopicQuery groupBySlackChannelId() Group by the slack_channel_id column
 * @method     ChildTopicQuery groupByTreeLeft() Group by the tree_left column
 * @method     ChildTopicQuery groupByTreeRight() Group by the tree_right column
 * @method     ChildTopicQuery groupByTreeLevel() Group by the tree_level column
 * @method     ChildTopicQuery groupByTreeScope() Group by the tree_scope column
 *
 * @method     ChildTopicQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildTopicQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildTopicQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildTopicQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildTopicQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildTopicQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildTopicQuery leftJoinPuzzleTopic($relationAlias = null) Adds a LEFT JOIN clause to the query using the PuzzleTopic relation
 * @method     ChildTopicQuery rightJoinPuzzleTopic($relationAlias = null) Adds a RIGHT JOIN clause to the query using the PuzzleTopic relation
 * @method     ChildTopicQuery innerJoinPuzzleTopic($relationAlias = null) Adds a INNER JOIN clause to the query using the PuzzleTopic relation
 *
 * @method     ChildTopicQuery joinWithPuzzleTopic($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the PuzzleTopic relation
 *
 * @method     ChildTopicQuery leftJoinWithPuzzleTopic() Adds a LEFT JOIN clause and with to the query using the PuzzleTopic relation
 * @method     ChildTopicQuery rightJoinWithPuzzleTopic() Adds a RIGHT JOIN clause and with to the query using the PuzzleTopic relation
 * @method     ChildTopicQuery innerJoinWithPuzzleTopic() Adds a INNER JOIN clause and with to the query using the PuzzleTopic relation
 *
 * @method     \PuzzleTopicQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildTopic findOne(ConnectionInterface $con = null) Return the first ChildTopic matching the query
 * @method     ChildTopic findOneOrCreate(ConnectionInterface $con = null) Return the first ChildTopic matching the query, or a new ChildTopic object populated from the query conditions when no match is found
 *
 * @method     ChildTopic findOneById(int $id) Return the first ChildTopic filtered by the id column
 * @method     ChildTopic findOneByTitle(string $title) Return the first ChildTopic filtered by the title column
 * @method     ChildTopic findOneBySlackChannel(string $slack_channel) Return the first ChildTopic filtered by the slack_channel column
 * @method     ChildTopic findOneBySlackChannelId(string $slack_channel_id) Return the first ChildTopic filtered by the slack_channel_id column
 * @method     ChildTopic findOneByTreeLeft(int $tree_left) Return the first ChildTopic filtered by the tree_left column
 * @method     ChildTopic findOneByTreeRight(int $tree_right) Return the first ChildTopic filtered by the tree_right column
 * @method     ChildTopic findOneByTreeLevel(int $tree_level) Return the first ChildTopic filtered by the tree_level column
 * @method     ChildTopic findOneByTreeScope(int $tree_scope) Return the first ChildTopic filtered by the tree_scope column *

 * @method     ChildTopic requirePk($key, ConnectionInterface $con = null) Return the ChildTopic by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildTopic requireOne(ConnectionInterface $con = null) Return the first ChildTopic matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildTopic requireOneById(int $id) Return the first ChildTopic filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildTopic requireOneByTitle(string $title) Return the first ChildTopic filtered by the title column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildTopic requireOneBySlackChannel(string $slack_channel) Return the first ChildTopic filtered by the slack_channel column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildTopic requireOneBySlackChannelId(string $slack_channel_id) Return the first ChildTopic filtered by the slack_channel_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildTopic requireOneByTreeLeft(int $tree_left) Return the first ChildTopic filtered by the tree_left column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildTopic requireOneByTreeRight(int $tree_right) Return the first ChildTopic filtered by the tree_right column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildTopic requireOneByTreeLevel(int $tree_level) Return the first ChildTopic filtered by the tree_level column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildTopic requireOneByTreeScope(int $tree_scope) Return the first ChildTopic filtered by the tree_scope column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildTopic[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildTopic objects based on current ModelCriteria
 * @method     ChildTopic[]|ObjectCollection findById(int $id) Return ChildTopic objects filtered by the id column
 * @method     ChildTopic[]|ObjectCollection findByTitle(string $title) Return ChildTopic objects filtered by the title column
 * @method     ChildTopic[]|ObjectCollection findBySlackChannel(string $slack_channel) Return ChildTopic objects filtered by the slack_channel column
 * @method     ChildTopic[]|ObjectCollection findBySlackChannelId(string $slack_channel_id) Return ChildTopic objects filtered by the slack_channel_id column
 * @method     ChildTopic[]|ObjectCollection findByTreeLeft(int $tree_left) Return ChildTopic objects filtered by the tree_left column
 * @method     ChildTopic[]|ObjectCollection findByTreeRight(int $tree_right) Return ChildTopic objects filtered by the tree_right column
 * @method     ChildTopic[]|ObjectCollection findByTreeLevel(int $tree_level) Return ChildTopic objects filtered by the tree_level column
 * @method     ChildTopic[]|ObjectCollection findByTreeScope(int $tree_scope) Return ChildTopic objects filtered by the tree_scope column
 * @method     ChildTopic[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class TopicQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \Base\TopicQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'palindrome', $modelName = '\\Topic', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildTopicQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildTopicQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildTopicQuery) {
            return $criteria;
        }
        $query = new ChildTopicQuery();
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
     * @return ChildTopic|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(TopicTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = TopicTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key)))) {
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
     * @return ChildTopic A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, title, slack_channel, slack_channel_id, tree_left, tree_right, tree_level, tree_scope FROM topic WHERE id = :p0';
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
            /** @var ChildTopic $obj */
            $obj = new ChildTopic();
            $obj->hydrate($row);
            TopicTableMap::addInstanceToPool($obj, null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key);
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
     * @return ChildTopic|array|mixed the result, formatted by the current formatter
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
     * @return $this|ChildTopicQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(TopicTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildTopicQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(TopicTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return $this|ChildTopicQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(TopicTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(TopicTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TopicTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the title column
     *
     * Example usage:
     * <code>
     * $query->filterByTitle('fooValue');   // WHERE title = 'fooValue'
     * $query->filterByTitle('%fooValue%', Criteria::LIKE); // WHERE title LIKE '%fooValue%'
     * </code>
     *
     * @param     string $title The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildTopicQuery The current query, for fluid interface
     */
    public function filterByTitle($title = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($title)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TopicTableMap::COL_TITLE, $title, $comparison);
    }

    /**
     * Filter the query on the slack_channel column
     *
     * Example usage:
     * <code>
     * $query->filterBySlackChannel('fooValue');   // WHERE slack_channel = 'fooValue'
     * $query->filterBySlackChannel('%fooValue%', Criteria::LIKE); // WHERE slack_channel LIKE '%fooValue%'
     * </code>
     *
     * @param     string $slackChannel The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildTopicQuery The current query, for fluid interface
     */
    public function filterBySlackChannel($slackChannel = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($slackChannel)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TopicTableMap::COL_SLACK_CHANNEL, $slackChannel, $comparison);
    }

    /**
     * Filter the query on the slack_channel_id column
     *
     * Example usage:
     * <code>
     * $query->filterBySlackChannelId('fooValue');   // WHERE slack_channel_id = 'fooValue'
     * $query->filterBySlackChannelId('%fooValue%', Criteria::LIKE); // WHERE slack_channel_id LIKE '%fooValue%'
     * </code>
     *
     * @param     string $slackChannelId The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildTopicQuery The current query, for fluid interface
     */
    public function filterBySlackChannelId($slackChannelId = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($slackChannelId)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TopicTableMap::COL_SLACK_CHANNEL_ID, $slackChannelId, $comparison);
    }

    /**
     * Filter the query on the tree_left column
     *
     * Example usage:
     * <code>
     * $query->filterByTreeLeft(1234); // WHERE tree_left = 1234
     * $query->filterByTreeLeft(array(12, 34)); // WHERE tree_left IN (12, 34)
     * $query->filterByTreeLeft(array('min' => 12)); // WHERE tree_left > 12
     * </code>
     *
     * @param     mixed $treeLeft The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildTopicQuery The current query, for fluid interface
     */
    public function filterByTreeLeft($treeLeft = null, $comparison = null)
    {
        if (is_array($treeLeft)) {
            $useMinMax = false;
            if (isset($treeLeft['min'])) {
                $this->addUsingAlias(TopicTableMap::COL_TREE_LEFT, $treeLeft['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($treeLeft['max'])) {
                $this->addUsingAlias(TopicTableMap::COL_TREE_LEFT, $treeLeft['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TopicTableMap::COL_TREE_LEFT, $treeLeft, $comparison);
    }

    /**
     * Filter the query on the tree_right column
     *
     * Example usage:
     * <code>
     * $query->filterByTreeRight(1234); // WHERE tree_right = 1234
     * $query->filterByTreeRight(array(12, 34)); // WHERE tree_right IN (12, 34)
     * $query->filterByTreeRight(array('min' => 12)); // WHERE tree_right > 12
     * </code>
     *
     * @param     mixed $treeRight The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildTopicQuery The current query, for fluid interface
     */
    public function filterByTreeRight($treeRight = null, $comparison = null)
    {
        if (is_array($treeRight)) {
            $useMinMax = false;
            if (isset($treeRight['min'])) {
                $this->addUsingAlias(TopicTableMap::COL_TREE_RIGHT, $treeRight['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($treeRight['max'])) {
                $this->addUsingAlias(TopicTableMap::COL_TREE_RIGHT, $treeRight['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TopicTableMap::COL_TREE_RIGHT, $treeRight, $comparison);
    }

    /**
     * Filter the query on the tree_level column
     *
     * Example usage:
     * <code>
     * $query->filterByTreeLevel(1234); // WHERE tree_level = 1234
     * $query->filterByTreeLevel(array(12, 34)); // WHERE tree_level IN (12, 34)
     * $query->filterByTreeLevel(array('min' => 12)); // WHERE tree_level > 12
     * </code>
     *
     * @param     mixed $treeLevel The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildTopicQuery The current query, for fluid interface
     */
    public function filterByTreeLevel($treeLevel = null, $comparison = null)
    {
        if (is_array($treeLevel)) {
            $useMinMax = false;
            if (isset($treeLevel['min'])) {
                $this->addUsingAlias(TopicTableMap::COL_TREE_LEVEL, $treeLevel['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($treeLevel['max'])) {
                $this->addUsingAlias(TopicTableMap::COL_TREE_LEVEL, $treeLevel['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TopicTableMap::COL_TREE_LEVEL, $treeLevel, $comparison);
    }

    /**
     * Filter the query on the tree_scope column
     *
     * Example usage:
     * <code>
     * $query->filterByTreeScope(1234); // WHERE tree_scope = 1234
     * $query->filterByTreeScope(array(12, 34)); // WHERE tree_scope IN (12, 34)
     * $query->filterByTreeScope(array('min' => 12)); // WHERE tree_scope > 12
     * </code>
     *
     * @param     mixed $treeScope The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildTopicQuery The current query, for fluid interface
     */
    public function filterByTreeScope($treeScope = null, $comparison = null)
    {
        if (is_array($treeScope)) {
            $useMinMax = false;
            if (isset($treeScope['min'])) {
                $this->addUsingAlias(TopicTableMap::COL_TREE_SCOPE, $treeScope['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($treeScope['max'])) {
                $this->addUsingAlias(TopicTableMap::COL_TREE_SCOPE, $treeScope['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TopicTableMap::COL_TREE_SCOPE, $treeScope, $comparison);
    }

    /**
     * Filter the query by a related \PuzzleTopic object
     *
     * @param \PuzzleTopic|ObjectCollection $puzzleTopic the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildTopicQuery The current query, for fluid interface
     */
    public function filterByPuzzleTopic($puzzleTopic, $comparison = null)
    {
        if ($puzzleTopic instanceof \PuzzleTopic) {
            return $this
                ->addUsingAlias(TopicTableMap::COL_ID, $puzzleTopic->getTopicId(), $comparison);
        } elseif ($puzzleTopic instanceof ObjectCollection) {
            return $this
                ->usePuzzleTopicQuery()
                ->filterByPrimaryKeys($puzzleTopic->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByPuzzleTopic() only accepts arguments of type \PuzzleTopic or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the PuzzleTopic relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildTopicQuery The current query, for fluid interface
     */
    public function joinPuzzleTopic($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('PuzzleTopic');

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
            $this->addJoinObject($join, 'PuzzleTopic');
        }

        return $this;
    }

    /**
     * Use the PuzzleTopic relation PuzzleTopic object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \PuzzleTopicQuery A secondary query class using the current class as primary query
     */
    public function usePuzzleTopicQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinPuzzleTopic($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'PuzzleTopic', '\PuzzleTopicQuery');
    }

    /**
     * Filter the query by a related Puzzle object
     * using the puzzleTopic table as cross reference
     *
     * @param Puzzle $puzzle the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildTopicQuery The current query, for fluid interface
     */
    public function filterByPuzzle($puzzle, $comparison = Criteria::EQUAL)
    {
        return $this
            ->usePuzzleTopicQuery()
            ->filterByPuzzle($puzzle, $comparison)
            ->endUse();
    }

    /**
     * Exclude object from result
     *
     * @param   ChildTopic $topic Object to remove from the list of results
     *
     * @return $this|ChildTopicQuery The current query, for fluid interface
     */
    public function prune($topic = null)
    {
        if ($topic) {
            $this->addUsingAlias(TopicTableMap::COL_ID, $topic->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the topic table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(TopicTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            TopicTableMap::clearInstancePool();
            TopicTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(TopicTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(TopicTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            TopicTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            TopicTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    // nested_set behavior

    /**
     * Filter the query to restrict the result to root objects
     *
     * @return    $this|ChildTopicQuery The current query, for fluid interface
     */
    public function treeRoots()
    {
        return $this->addUsingAlias(ChildTopic::LEFT_COL, 1, Criteria::EQUAL);
    }

    /**
     * Returns the objects in a certain tree, from the tree scope
     *
     * @param     int $scope        Scope to determine which objects node to return
     *
     * @return    $this|ChildTopicQuery The current query, for fluid interface
     */
    public function inTree($scope = null)
    {
        return $this->addUsingAlias(ChildTopic::SCOPE_COL, $scope, Criteria::EQUAL);
    }

    /**
     * Filter the query to restrict the result to descendants of an object
     *
     * @param     ChildTopic $topic The object to use for descendant search
     *
     * @return    $this|ChildTopicQuery The current query, for fluid interface
     */
    public function descendantsOf(ChildTopic $topic)
    {
        return $this
            ->inTree($topic->getScopeValue())
            ->addUsingAlias(ChildTopic::LEFT_COL, $topic->getLeftValue(), Criteria::GREATER_THAN)
            ->addUsingAlias(ChildTopic::LEFT_COL, $topic->getRightValue(), Criteria::LESS_THAN);
    }

    /**
     * Filter the query to restrict the result to the branch of an object.
     * Same as descendantsOf(), except that it includes the object passed as parameter in the result
     *
     * @param     ChildTopic $topic The object to use for branch search
     *
     * @return    $this|ChildTopicQuery The current query, for fluid interface
     */
    public function branchOf(ChildTopic $topic)
    {
        return $this
            ->inTree($topic->getScopeValue())
            ->addUsingAlias(ChildTopic::LEFT_COL, $topic->getLeftValue(), Criteria::GREATER_EQUAL)
            ->addUsingAlias(ChildTopic::LEFT_COL, $topic->getRightValue(), Criteria::LESS_EQUAL);
    }

    /**
     * Filter the query to restrict the result to children of an object
     *
     * @param     ChildTopic $topic The object to use for child search
     *
     * @return    $this|ChildTopicQuery The current query, for fluid interface
     */
    public function childrenOf(ChildTopic $topic)
    {
        return $this
            ->descendantsOf($topic)
            ->addUsingAlias(ChildTopic::LEVEL_COL, $topic->getLevel() + 1, Criteria::EQUAL);
    }

    /**
     * Filter the query to restrict the result to siblings of an object.
     * The result does not include the object passed as parameter.
     *
     * @param     ChildTopic $topic The object to use for sibling search
     * @param      ConnectionInterface $con Connection to use.
     *
     * @return    $this|ChildTopicQuery The current query, for fluid interface
     */
    public function siblingsOf(ChildTopic $topic, ConnectionInterface $con = null)
    {
        if ($topic->isRoot()) {
            return $this->
                add(ChildTopic::LEVEL_COL, '1<>1', Criteria::CUSTOM);
        } else {
            return $this
                ->childrenOf($topic->getParent($con))
                ->prune($topic);
        }
    }

    /**
     * Filter the query to restrict the result to ancestors of an object
     *
     * @param     ChildTopic $topic The object to use for ancestors search
     *
     * @return    $this|ChildTopicQuery The current query, for fluid interface
     */
    public function ancestorsOf(ChildTopic $topic)
    {
        return $this
            ->inTree($topic->getScopeValue())
            ->addUsingAlias(ChildTopic::LEFT_COL, $topic->getLeftValue(), Criteria::LESS_THAN)
            ->addUsingAlias(ChildTopic::RIGHT_COL, $topic->getRightValue(), Criteria::GREATER_THAN);
    }

    /**
     * Filter the query to restrict the result to roots of an object.
     * Same as ancestorsOf(), except that it includes the object passed as parameter in the result
     *
     * @param     ChildTopic $topic The object to use for roots search
     *
     * @return    $this|ChildTopicQuery The current query, for fluid interface
     */
    public function rootsOf(ChildTopic $topic)
    {
        return $this
            ->inTree($topic->getScopeValue())
            ->addUsingAlias(ChildTopic::LEFT_COL, $topic->getLeftValue(), Criteria::LESS_EQUAL)
            ->addUsingAlias(ChildTopic::RIGHT_COL, $topic->getRightValue(), Criteria::GREATER_EQUAL);
    }

    /**
     * Order the result by branch, i.e. natural tree order
     *
     * @param     bool $reverse if true, reverses the order
     *
     * @return    $this|ChildTopicQuery The current query, for fluid interface
     */
    public function orderByBranch($reverse = false)
    {
        if ($reverse) {
            return $this
                ->addDescendingOrderByColumn(ChildTopic::LEFT_COL);
        } else {
            return $this
                ->addAscendingOrderByColumn(ChildTopic::LEFT_COL);
        }
    }

    /**
     * Order the result by level, the closer to the root first
     *
     * @param     bool $reverse if true, reverses the order
     *
     * @return    $this|ChildTopicQuery The current query, for fluid interface
     */
    public function orderByLevel($reverse = false)
    {
        if ($reverse) {
            return $this
                ->addDescendingOrderByColumn(ChildTopic::LEVEL_COL)
                ->addDescendingOrderByColumn(ChildTopic::LEFT_COL);
        } else {
            return $this
                ->addAscendingOrderByColumn(ChildTopic::LEVEL_COL)
                ->addAscendingOrderByColumn(ChildTopic::LEFT_COL);
        }
    }

    /**
     * Returns a root node for the tree
     *
     * @param      int $scope        Scope to determine which root node to return
     * @param      ConnectionInterface $con    Connection to use.
     *
     * @return     ChildTopic The tree root object
     */
    public function findRoot($scope = null, ConnectionInterface $con = null)
    {
        return $this
            ->addUsingAlias(ChildTopic::LEFT_COL, 1, Criteria::EQUAL)
            ->inTree($scope)
            ->findOne($con);
    }

    /**
     * Returns the root objects for all trees.
     *
     * @param      ConnectionInterface $con    Connection to use.
     *
     * @return    ChildTopic[]|ObjectCollection|mixed the list of results, formatted by the current formatter
     */
    public function findRoots(ConnectionInterface $con = null)
    {
        return $this
            ->treeRoots()
            ->find($con);
    }

    /**
     * Returns a tree of objects
     *
     * @param      int $scope        Scope to determine which tree node to return
     * @param      ConnectionInterface $con    Connection to use.
     *
     * @return     ChildTopic[]|ObjectCollection|mixed the list of results, formatted by the current formatter
     */
    public function findTree($scope = null, ConnectionInterface $con = null)
    {
        return $this
            ->inTree($scope)
            ->orderByBranch()
            ->find($con);
    }

    /**
     * Returns the root nodes for the tree
     *
     * @param      Criteria $criteria    Optional Criteria to filter the query
     * @param      ConnectionInterface $con    Connection to use.
     * @return     ChildTopic[]|ObjectCollection|mixed the list of results, formatted by the current formatter
     */
    static public function retrieveRoots(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        if (null === $criteria) {
            $criteria = new Criteria(TopicTableMap::DATABASE_NAME);
        }
        $criteria->add(ChildTopic::LEFT_COL, 1, Criteria::EQUAL);

        return ChildTopicQuery::create(null, $criteria)->find($con);
    }

    /**
     * Returns the root node for a given scope
     *
     * @param      int $scope        Scope to determine which root node to return
     * @param      ConnectionInterface $con    Connection to use.
     * @return     ChildTopic            Propel object for root node
     */
    static public function retrieveRoot($scope = null, ConnectionInterface $con = null)
    {
        $c = new Criteria(TopicTableMap::DATABASE_NAME);
        $c->add(ChildTopic::LEFT_COL, 1, Criteria::EQUAL);
        $c->add(ChildTopic::SCOPE_COL, $scope, Criteria::EQUAL);

        return ChildTopicQuery::create(null, $c)->findOne($con);
    }

    /**
     * Returns the whole tree node for a given scope
     *
     * @param      int $scope        Scope to determine which root node to return
     * @param      Criteria $criteria    Optional Criteria to filter the query
     * @param      ConnectionInterface $con    Connection to use.
     * @return     ChildTopic[]|ObjectCollection|mixed the list of results, formatted by the current formatter
     */
    static public function retrieveTree($scope = null, Criteria $criteria = null, ConnectionInterface $con = null)
    {
        if (null === $criteria) {
            $criteria = new Criteria(TopicTableMap::DATABASE_NAME);
        }
        $criteria->addAscendingOrderByColumn(ChildTopic::LEFT_COL);
        $criteria->add(ChildTopic::SCOPE_COL, $scope, Criteria::EQUAL);

        return ChildTopicQuery::create(null, $criteria)->find($con);
    }

    /**
     * Tests if node is valid
     *
     * @param      ChildTopic $node    Propel object for src node
     * @return     bool
     */
    static public function isValid(ChildTopic $node = null)
    {
        if (is_object($node) && $node->getRightValue() > $node->getLeftValue()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Delete an entire tree
     *
     * @param      int $scope        Scope to determine which tree to delete
     * @param      ConnectionInterface $con    Connection to use.
     *
     * @return     int  The number of deleted nodes
     */
    static public function deleteTree($scope = null, ConnectionInterface $con = null)
    {
        $c = new Criteria(TopicTableMap::DATABASE_NAME);
        $c->add(ChildTopic::SCOPE_COL, $scope, Criteria::EQUAL);

        return TopicTableMap::doDelete($c, $con);
    }

    /**
     * Adds $delta to all L and R values that are >= $first and <= $last.
     * '$delta' can also be negative.
     *
     * @param int $delta               Value to be shifted by, can be negative
     * @param int $first               First node to be shifted
     * @param int $last                Last node to be shifted (optional)
     * @param int $scope               Scope to use for the shift
     * @param ConnectionInterface $con Connection to use.
     */
    static public function shiftRLValues($delta, $first, $last = null, $scope = null, ConnectionInterface $con = null)
    {
        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(TopicTableMap::DATABASE_NAME);
        }

        // Shift left column values
        $whereCriteria = new Criteria(TopicTableMap::DATABASE_NAME);
        $criterion = $whereCriteria->getNewCriterion(ChildTopic::LEFT_COL, $first, Criteria::GREATER_EQUAL);
        if (null !== $last) {
            $criterion->addAnd($whereCriteria->getNewCriterion(ChildTopic::LEFT_COL, $last, Criteria::LESS_EQUAL));
        }
        $whereCriteria->add($criterion);
        $whereCriteria->add(ChildTopic::SCOPE_COL, $scope, Criteria::EQUAL);

        $valuesCriteria = new Criteria(TopicTableMap::DATABASE_NAME);
        $valuesCriteria->add(ChildTopic::LEFT_COL, array('raw' => ChildTopic::LEFT_COL . ' + ?', 'value' => $delta), Criteria::CUSTOM_EQUAL);

        $whereCriteria->doUpdate($valuesCriteria, $con);

        // Shift right column values
        $whereCriteria = new Criteria(TopicTableMap::DATABASE_NAME);
        $criterion = $whereCriteria->getNewCriterion(ChildTopic::RIGHT_COL, $first, Criteria::GREATER_EQUAL);
        if (null !== $last) {
            $criterion->addAnd($whereCriteria->getNewCriterion(ChildTopic::RIGHT_COL, $last, Criteria::LESS_EQUAL));
        }
        $whereCriteria->add($criterion);
        $whereCriteria->add(ChildTopic::SCOPE_COL, $scope, Criteria::EQUAL);

        $valuesCriteria = new Criteria(TopicTableMap::DATABASE_NAME);
        $valuesCriteria->add(ChildTopic::RIGHT_COL, array('raw' => ChildTopic::RIGHT_COL . ' + ?', 'value' => $delta), Criteria::CUSTOM_EQUAL);

        $whereCriteria->doUpdate($valuesCriteria, $con);
    }

    /**
     * Adds $delta to level for nodes having left value >= $first and right value <= $last.
     * '$delta' can also be negative.
     *
     * @param      int $delta        Value to be shifted by, can be negative
     * @param      int $first        First node to be shifted
     * @param      int $last            Last node to be shifted
     * @param      int $scope        Scope to use for the shift
     * @param      ConnectionInterface $con        Connection to use.
     */
    static public function shiftLevel($delta, $first, $last, $scope = null, ConnectionInterface $con = null)
    {
        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(TopicTableMap::DATABASE_NAME);
        }

        $whereCriteria = new Criteria(TopicTableMap::DATABASE_NAME);
        $whereCriteria->add(ChildTopic::LEFT_COL, $first, Criteria::GREATER_EQUAL);
        $whereCriteria->add(ChildTopic::RIGHT_COL, $last, Criteria::LESS_EQUAL);
        $whereCriteria->add(ChildTopic::SCOPE_COL, $scope, Criteria::EQUAL);

        $valuesCriteria = new Criteria(TopicTableMap::DATABASE_NAME);
        $valuesCriteria->add(ChildTopic::LEVEL_COL, array('raw' => ChildTopic::LEVEL_COL . ' + ?', 'value' => $delta), Criteria::CUSTOM_EQUAL);

        $whereCriteria->doUpdate($valuesCriteria, $con);
    }

    /**
     * Reload all already loaded nodes to sync them with updated db
     *
     * @param      ChildTopic $prune        Object to prune from the update
     * @param      ConnectionInterface $con        Connection to use.
     */
    static public function updateLoadedNodes($prune = null, ConnectionInterface $con = null)
    {
        if (Propel::isInstancePoolingEnabled()) {
            $keys = array();
            /** @var $obj ChildTopic */
            foreach (TopicTableMap::$instances as $obj) {
                if (!$prune || !$prune->equals($obj)) {
                    $keys[] = $obj->getPrimaryKey();
                }
            }

            if (!empty($keys)) {
                // We don't need to alter the object instance pool; we're just modifying these ones
                // already in the pool.
                $criteria = new Criteria(TopicTableMap::DATABASE_NAME);
                $criteria->add(TopicTableMap::COL_ID, $keys, Criteria::IN);
                $dataFetcher = ChildTopicQuery::create(null, $criteria)->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
                while ($row = $dataFetcher->fetch()) {
                    $key = TopicTableMap::getPrimaryKeyHashFromRow($row, 0);
                    /** @var $object ChildTopic */
                    if (null !== ($object = TopicTableMap::getInstanceFromPool($key))) {
                        $object->setLeftValue($row[4]);
                        $object->setRightValue($row[5]);
                        $object->setLevel($row[6]);
                        $object->clearNestedSetChildren();
                        $object->setScopeValue($row[7]);
                    }
                }
                $dataFetcher->close();
            }
        }
    }

    /**
     * Update the tree to allow insertion of a leaf at the specified position
     *
     * @param      int $left    left column value
     * @param      integer $scope    scope column value
     * @param      mixed $prune    Object to prune from the shift
     * @param      ConnectionInterface $con    Connection to use.
     */
    static public function makeRoomForLeaf($left, $scope, $prune = null, ConnectionInterface $con = null)
    {
        // Update database nodes
        ChildTopicQuery::shiftRLValues(2, $left, null, $scope, $con);

        // Update all loaded nodes
        ChildTopicQuery::updateLoadedNodes($prune, $con);
    }

    /**
     * Update the tree to allow insertion of a leaf at the specified position
     *
     * @param      integer $scope    scope column value
     * @param      ConnectionInterface $con    Connection to use.
     */
    static public function fixLevels($scope, ConnectionInterface $con = null)
    {
        $c = new Criteria();
        $c->add(ChildTopic::SCOPE_COL, $scope, Criteria::EQUAL);
        $c->addAscendingOrderByColumn(ChildTopic::LEFT_COL);
        $dataFetcher = ChildTopicQuery::create(null, $c)->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);

        // set the class once to avoid overhead in the loop
        $cls = TopicTableMap::getOMClass(false);
        $level = null;
        // iterate over the statement
        while ($row = $dataFetcher->fetch()) {

            // hydrate object
            $key = TopicTableMap::getPrimaryKeyHashFromRow($row, 0);
            /** @var $obj ChildTopic */
            if (null === ($obj = TopicTableMap::getInstanceFromPool($key))) {
                $obj = new $cls();
                $obj->hydrate($row);
                TopicTableMap::addInstanceToPool($obj, $key);
            }

            // compute level
            // Algorithm shamelessly stolen from sfPropelActAsNestedSetBehaviorPlugin
            // Probably authored by Tristan Rivoallan
            if ($level === null) {
                $level = 0;
                $i = 0;
                $prev = array($obj->getRightValue());
            } else {
                while ($obj->getRightValue() > $prev[$i]) {
                    $i--;
                }
                $level = ++$i;
                $prev[$i] = $obj->getRightValue();
            }

            // update level in node if necessary
            if ($obj->getLevel() !== $level) {
                $obj->setLevel($level);
                $obj->save($con);
            }
        }
        $dataFetcher->close();
    }

    /**
     * Updates all scope values for items that has negative left (<=0) values.
     *
     * @param      mixed     $scope
     * @param      ConnectionInterface $con  Connection to use.
     */
    public static function setNegativeScope($scope, ConnectionInterface $con = null)
    {
        //adjust scope value to $scope
        $whereCriteria = new Criteria(TopicTableMap::DATABASE_NAME);
        $whereCriteria->add(ChildTopic::LEFT_COL, 0, Criteria::LESS_EQUAL);

        $valuesCriteria = new Criteria(TopicTableMap::DATABASE_NAME);
        $valuesCriteria->add(ChildTopic::SCOPE_COL, $scope, Criteria::EQUAL);

        $whereCriteria->doUpdate($valuesCriteria, $con);
    }

} // TopicQuery
