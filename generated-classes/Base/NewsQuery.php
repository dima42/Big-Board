<?php

namespace Base;

use \News as ChildNews;
use \NewsArchive as ChildNewsArchive;
use \NewsQuery as ChildNewsQuery;
use \Exception;
use \PDO;
use Map\NewsTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'news' table.
 *
 *
 *
 * @method     ChildNewsQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildNewsQuery orderByNewsType($order = Criteria::ASC) Order by the news_type column
 * @method     ChildNewsQuery orderByContent($order = Criteria::ASC) Order by the content column
 * @method     ChildNewsQuery orderByMemberId($order = Criteria::ASC) Order by the member_id column
 * @method     ChildNewsQuery orderByPuzzleId($order = Criteria::ASC) Order by the puzzle_id column
 * @method     ChildNewsQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildNewsQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     ChildNewsQuery groupById() Group by the id column
 * @method     ChildNewsQuery groupByNewsType() Group by the news_type column
 * @method     ChildNewsQuery groupByContent() Group by the content column
 * @method     ChildNewsQuery groupByMemberId() Group by the member_id column
 * @method     ChildNewsQuery groupByPuzzleId() Group by the puzzle_id column
 * @method     ChildNewsQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildNewsQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     ChildNewsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildNewsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildNewsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildNewsQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildNewsQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildNewsQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildNewsQuery leftJoinMember($relationAlias = null) Adds a LEFT JOIN clause to the query using the Member relation
 * @method     ChildNewsQuery rightJoinMember($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Member relation
 * @method     ChildNewsQuery innerJoinMember($relationAlias = null) Adds a INNER JOIN clause to the query using the Member relation
 *
 * @method     ChildNewsQuery joinWithMember($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Member relation
 *
 * @method     ChildNewsQuery leftJoinWithMember() Adds a LEFT JOIN clause and with to the query using the Member relation
 * @method     ChildNewsQuery rightJoinWithMember() Adds a RIGHT JOIN clause and with to the query using the Member relation
 * @method     ChildNewsQuery innerJoinWithMember() Adds a INNER JOIN clause and with to the query using the Member relation
 *
 * @method     ChildNewsQuery leftJoinPuzzle($relationAlias = null) Adds a LEFT JOIN clause to the query using the Puzzle relation
 * @method     ChildNewsQuery rightJoinPuzzle($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Puzzle relation
 * @method     ChildNewsQuery innerJoinPuzzle($relationAlias = null) Adds a INNER JOIN clause to the query using the Puzzle relation
 *
 * @method     ChildNewsQuery joinWithPuzzle($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Puzzle relation
 *
 * @method     ChildNewsQuery leftJoinWithPuzzle() Adds a LEFT JOIN clause and with to the query using the Puzzle relation
 * @method     ChildNewsQuery rightJoinWithPuzzle() Adds a RIGHT JOIN clause and with to the query using the Puzzle relation
 * @method     ChildNewsQuery innerJoinWithPuzzle() Adds a INNER JOIN clause and with to the query using the Puzzle relation
 *
 * @method     \MemberQuery|\PuzzleQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildNews findOne(ConnectionInterface $con = null) Return the first ChildNews matching the query
 * @method     ChildNews findOneOrCreate(ConnectionInterface $con = null) Return the first ChildNews matching the query, or a new ChildNews object populated from the query conditions when no match is found
 *
 * @method     ChildNews findOneById(int $id) Return the first ChildNews filtered by the id column
 * @method     ChildNews findOneByNewsType(string $news_type) Return the first ChildNews filtered by the news_type column
 * @method     ChildNews findOneByContent(string $content) Return the first ChildNews filtered by the content column
 * @method     ChildNews findOneByMemberId(int $member_id) Return the first ChildNews filtered by the member_id column
 * @method     ChildNews findOneByPuzzleId(int $puzzle_id) Return the first ChildNews filtered by the puzzle_id column
 * @method     ChildNews findOneByCreatedAt(string $created_at) Return the first ChildNews filtered by the created_at column
 * @method     ChildNews findOneByUpdatedAt(string $updated_at) Return the first ChildNews filtered by the updated_at column *

 * @method     ChildNews requirePk($key, ConnectionInterface $con = null) Return the ChildNews by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildNews requireOne(ConnectionInterface $con = null) Return the first ChildNews matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildNews requireOneById(int $id) Return the first ChildNews filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildNews requireOneByNewsType(string $news_type) Return the first ChildNews filtered by the news_type column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildNews requireOneByContent(string $content) Return the first ChildNews filtered by the content column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildNews requireOneByMemberId(int $member_id) Return the first ChildNews filtered by the member_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildNews requireOneByPuzzleId(int $puzzle_id) Return the first ChildNews filtered by the puzzle_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildNews requireOneByCreatedAt(string $created_at) Return the first ChildNews filtered by the created_at column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildNews requireOneByUpdatedAt(string $updated_at) Return the first ChildNews filtered by the updated_at column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildNews[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildNews objects based on current ModelCriteria
 * @method     ChildNews[]|ObjectCollection findById(int $id) Return ChildNews objects filtered by the id column
 * @method     ChildNews[]|ObjectCollection findByNewsType(string $news_type) Return ChildNews objects filtered by the news_type column
 * @method     ChildNews[]|ObjectCollection findByContent(string $content) Return ChildNews objects filtered by the content column
 * @method     ChildNews[]|ObjectCollection findByMemberId(int $member_id) Return ChildNews objects filtered by the member_id column
 * @method     ChildNews[]|ObjectCollection findByPuzzleId(int $puzzle_id) Return ChildNews objects filtered by the puzzle_id column
 * @method     ChildNews[]|ObjectCollection findByCreatedAt(string $created_at) Return ChildNews objects filtered by the created_at column
 * @method     ChildNews[]|ObjectCollection findByUpdatedAt(string $updated_at) Return ChildNews objects filtered by the updated_at column
 * @method     ChildNews[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class NewsQuery extends ModelCriteria
{

    // archivable behavior
    protected $archiveOnDelete = true;
protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \Base\NewsQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'palindrome', $modelName = '\\News', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildNewsQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildNewsQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildNewsQuery) {
            return $criteria;
        }
        $query = new ChildNewsQuery();
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
     * @return ChildNews|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(NewsTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = NewsTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key)))) {
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
     * @return ChildNews A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, news_type, content, member_id, puzzle_id, created_at, updated_at FROM news WHERE id = :p0';
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
            /** @var ChildNews $obj */
            $obj = new ChildNews();
            $obj->hydrate($row);
            NewsTableMap::addInstanceToPool($obj, null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key);
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
     * @return ChildNews|array|mixed the result, formatted by the current formatter
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
     * @return $this|ChildNewsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(NewsTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildNewsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(NewsTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return $this|ChildNewsQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(NewsTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(NewsTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(NewsTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the news_type column
     *
     * Example usage:
     * <code>
     * $query->filterByNewsType('fooValue');   // WHERE news_type = 'fooValue'
     * $query->filterByNewsType('%fooValue%', Criteria::LIKE); // WHERE news_type LIKE '%fooValue%'
     * </code>
     *
     * @param     string $newsType The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildNewsQuery The current query, for fluid interface
     */
    public function filterByNewsType($newsType = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($newsType)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(NewsTableMap::COL_NEWS_TYPE, $newsType, $comparison);
    }

    /**
     * Filter the query on the content column
     *
     * Example usage:
     * <code>
     * $query->filterByContent('fooValue');   // WHERE content = 'fooValue'
     * $query->filterByContent('%fooValue%', Criteria::LIKE); // WHERE content LIKE '%fooValue%'
     * </code>
     *
     * @param     string $content The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildNewsQuery The current query, for fluid interface
     */
    public function filterByContent($content = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($content)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(NewsTableMap::COL_CONTENT, $content, $comparison);
    }

    /**
     * Filter the query on the member_id column
     *
     * Example usage:
     * <code>
     * $query->filterByMemberId(1234); // WHERE member_id = 1234
     * $query->filterByMemberId(array(12, 34)); // WHERE member_id IN (12, 34)
     * $query->filterByMemberId(array('min' => 12)); // WHERE member_id > 12
     * </code>
     *
     * @see       filterByMember()
     *
     * @param     mixed $memberId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildNewsQuery The current query, for fluid interface
     */
    public function filterByMemberId($memberId = null, $comparison = null)
    {
        if (is_array($memberId)) {
            $useMinMax = false;
            if (isset($memberId['min'])) {
                $this->addUsingAlias(NewsTableMap::COL_MEMBER_ID, $memberId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($memberId['max'])) {
                $this->addUsingAlias(NewsTableMap::COL_MEMBER_ID, $memberId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(NewsTableMap::COL_MEMBER_ID, $memberId, $comparison);
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
     * @return $this|ChildNewsQuery The current query, for fluid interface
     */
    public function filterByPuzzleId($puzzleId = null, $comparison = null)
    {
        if (is_array($puzzleId)) {
            $useMinMax = false;
            if (isset($puzzleId['min'])) {
                $this->addUsingAlias(NewsTableMap::COL_PUZZLE_ID, $puzzleId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($puzzleId['max'])) {
                $this->addUsingAlias(NewsTableMap::COL_PUZZLE_ID, $puzzleId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(NewsTableMap::COL_PUZZLE_ID, $puzzleId, $comparison);
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
     * @return $this|ChildNewsQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(NewsTableMap::COL_CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(NewsTableMap::COL_CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(NewsTableMap::COL_CREATED_AT, $createdAt, $comparison);
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
     * @return $this|ChildNewsQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(NewsTableMap::COL_UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(NewsTableMap::COL_UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(NewsTableMap::COL_UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related \Member object
     *
     * @param \Member|ObjectCollection $member The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildNewsQuery The current query, for fluid interface
     */
    public function filterByMember($member, $comparison = null)
    {
        if ($member instanceof \Member) {
            return $this
                ->addUsingAlias(NewsTableMap::COL_MEMBER_ID, $member->getId(), $comparison);
        } elseif ($member instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(NewsTableMap::COL_MEMBER_ID, $member->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByMember() only accepts arguments of type \Member or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Member relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildNewsQuery The current query, for fluid interface
     */
    public function joinMember($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Member');

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
            $this->addJoinObject($join, 'Member');
        }

        return $this;
    }

    /**
     * Use the Member relation Member object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \MemberQuery A secondary query class using the current class as primary query
     */
    public function useMemberQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinMember($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Member', '\MemberQuery');
    }

    /**
     * Filter the query by a related \Puzzle object
     *
     * @param \Puzzle|ObjectCollection $puzzle The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildNewsQuery The current query, for fluid interface
     */
    public function filterByPuzzle($puzzle, $comparison = null)
    {
        if ($puzzle instanceof \Puzzle) {
            return $this
                ->addUsingAlias(NewsTableMap::COL_PUZZLE_ID, $puzzle->getId(), $comparison);
        } elseif ($puzzle instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(NewsTableMap::COL_PUZZLE_ID, $puzzle->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return $this|ChildNewsQuery The current query, for fluid interface
     */
    public function joinPuzzle($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
    public function usePuzzleQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinPuzzle($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Puzzle', '\PuzzleQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildNews $news Object to remove from the list of results
     *
     * @return $this|ChildNewsQuery The current query, for fluid interface
     */
    public function prune($news = null)
    {
        if ($news) {
            $this->addUsingAlias(NewsTableMap::COL_ID, $news->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Code to execute before every DELETE statement
     *
     * @param     ConnectionInterface $con The connection object used by the query
     */
    protected function basePreDelete(ConnectionInterface $con)
    {
        // archivable behavior

        if ($this->archiveOnDelete) {
            $this->archive($con);
        } else {
            $this->archiveOnDelete = true;
        }


        return $this->preDelete($con);
    }

    /**
     * Deletes all rows from the news table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(NewsTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            NewsTableMap::clearInstancePool();
            NewsTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(NewsTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(NewsTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            NewsTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            NewsTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     $this|ChildNewsQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(NewsTableMap::COL_UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     $this|ChildNewsQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(NewsTableMap::COL_UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return     $this|ChildNewsQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(NewsTableMap::COL_UPDATED_AT);
    }

    /**
     * Order by create date desc
     *
     * @return     $this|ChildNewsQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(NewsTableMap::COL_CREATED_AT);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     $this|ChildNewsQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(NewsTableMap::COL_CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by create date asc
     *
     * @return     $this|ChildNewsQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(NewsTableMap::COL_CREATED_AT);
    }

    // archivable behavior

    /**
     * Copy the data of the objects satisfying the query into ChildNewsArchive archive objects.
     * The archived objects are then saved.
     * If any of the objects has already been archived, the archived object
     * is updated and not duplicated.
     * Warning: This termination methods issues 2n+1 queries.
     *
     * @param      ConnectionInterface $con    Connection to use.
     * @param      Boolean $useLittleMemory    Whether or not to use OnDemandFormatter to retrieve objects.
     *               Set to false if the identity map matters.
     *               Set to true (default) to use less memory.
     *
     * @return     int the number of archived objects
     */
    public function archive($con = null, $useLittleMemory = true)
    {
        $criteria = clone $this;
        // prepare the query
        $criteria->setWith(array());
        if ($useLittleMemory) {
            $criteria->setFormatter(ModelCriteria::FORMAT_ON_DEMAND);
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(NewsTableMap::DATABASE_NAME);
        }

        return $con->transaction(function () use ($con, $criteria) {
            $totalArchivedObjects = 0;

            // archive all results one by one
            foreach ($criteria->find($con) as $object) {
                $object->archive($con);
                $totalArchivedObjects++;
            }

            return $totalArchivedObjects;
        });
    }

    /**
     * Enable/disable auto-archiving on delete for the next query.
     *
     * @param boolean True if the query must archive deleted objects, false otherwise.
     */
    public function setArchiveOnDelete($archiveOnDelete)
    {
        $this->archiveOnDelete = $archiveOnDelete;
    }

    /**
     * Delete records matching the current query without archiving them.
     *
     * @param      ConnectionInterface $con    Connection to use.
     *
     * @return integer the number of deleted rows
     */
    public function deleteWithoutArchive($con = null)
    {
        $this->archiveOnDelete = false;

        return $this->delete($con);
    }

    /**
     * Delete all records without archiving them.
     *
     * @param      ConnectionInterface $con    Connection to use.
     *
     * @return integer the number of deleted rows
     */
    public function deleteAllWithoutArchive($con = null)
    {
        $this->archiveOnDelete = false;

        return $this->deleteAll($con);
    }

} // NewsQuery
