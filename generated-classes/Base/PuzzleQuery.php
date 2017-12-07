<?php

namespace Base;

use \Puzzle as ChildPuzzle;
use \PuzzleQuery as ChildPuzzleQuery;
use \Exception;
use \PDO;
use Map\PuzzleTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'puzzle' table.
 *
 *
 *
 * @method     ChildPuzzleQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildPuzzleQuery orderByTitle($order = Criteria::ASC) Order by the title column
 * @method     ChildPuzzleQuery orderByUrl($order = Criteria::ASC) Order by the url column
 * @method     ChildPuzzleQuery orderBySpreadsheetId($order = Criteria::ASC) Order by the spreadsheet_id column
 * @method     ChildPuzzleQuery orderBySolution($order = Criteria::ASC) Order by the solution column
 * @method     ChildPuzzleQuery orderByStatus($order = Criteria::ASC) Order by the status column
 * @method     ChildPuzzleQuery orderBySlackChannel($order = Criteria::ASC) Order by the slack_channel column
 * @method     ChildPuzzleQuery orderBySlackChannelId($order = Criteria::ASC) Order by the slack_channel_id column
 * @method     ChildPuzzleQuery orderByPostCount($order = Criteria::ASC) Order by the post_count column
 * @method     ChildPuzzleQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildPuzzleQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     ChildPuzzleQuery groupById() Group by the id column
 * @method     ChildPuzzleQuery groupByTitle() Group by the title column
 * @method     ChildPuzzleQuery groupByUrl() Group by the url column
 * @method     ChildPuzzleQuery groupBySpreadsheetId() Group by the spreadsheet_id column
 * @method     ChildPuzzleQuery groupBySolution() Group by the solution column
 * @method     ChildPuzzleQuery groupByStatus() Group by the status column
 * @method     ChildPuzzleQuery groupBySlackChannel() Group by the slack_channel column
 * @method     ChildPuzzleQuery groupBySlackChannelId() Group by the slack_channel_id column
 * @method     ChildPuzzleQuery groupByPostCount() Group by the post_count column
 * @method     ChildPuzzleQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildPuzzleQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     ChildPuzzleQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildPuzzleQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildPuzzleQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildPuzzleQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildPuzzleQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildPuzzleQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildPuzzleQuery leftJoinNote($relationAlias = null) Adds a LEFT JOIN clause to the query using the Note relation
 * @method     ChildPuzzleQuery rightJoinNote($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Note relation
 * @method     ChildPuzzleQuery innerJoinNote($relationAlias = null) Adds a INNER JOIN clause to the query using the Note relation
 *
 * @method     ChildPuzzleQuery joinWithNote($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Note relation
 *
 * @method     ChildPuzzleQuery leftJoinWithNote() Adds a LEFT JOIN clause and with to the query using the Note relation
 * @method     ChildPuzzleQuery rightJoinWithNote() Adds a RIGHT JOIN clause and with to the query using the Note relation
 * @method     ChildPuzzleQuery innerJoinWithNote() Adds a INNER JOIN clause and with to the query using the Note relation
 *
 * @method     ChildPuzzleQuery leftJoinPuzzleMember($relationAlias = null) Adds a LEFT JOIN clause to the query using the PuzzleMember relation
 * @method     ChildPuzzleQuery rightJoinPuzzleMember($relationAlias = null) Adds a RIGHT JOIN clause to the query using the PuzzleMember relation
 * @method     ChildPuzzleQuery innerJoinPuzzleMember($relationAlias = null) Adds a INNER JOIN clause to the query using the PuzzleMember relation
 *
 * @method     ChildPuzzleQuery joinWithPuzzleMember($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the PuzzleMember relation
 *
 * @method     ChildPuzzleQuery leftJoinWithPuzzleMember() Adds a LEFT JOIN clause and with to the query using the PuzzleMember relation
 * @method     ChildPuzzleQuery rightJoinWithPuzzleMember() Adds a RIGHT JOIN clause and with to the query using the PuzzleMember relation
 * @method     ChildPuzzleQuery innerJoinWithPuzzleMember() Adds a INNER JOIN clause and with to the query using the PuzzleMember relation
 *
 * @method     ChildPuzzleQuery leftJoinPuzzleParent($relationAlias = null) Adds a LEFT JOIN clause to the query using the PuzzleParent relation
 * @method     ChildPuzzleQuery rightJoinPuzzleParent($relationAlias = null) Adds a RIGHT JOIN clause to the query using the PuzzleParent relation
 * @method     ChildPuzzleQuery innerJoinPuzzleParent($relationAlias = null) Adds a INNER JOIN clause to the query using the PuzzleParent relation
 *
 * @method     ChildPuzzleQuery joinWithPuzzleParent($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the PuzzleParent relation
 *
 * @method     ChildPuzzleQuery leftJoinWithPuzzleParent() Adds a LEFT JOIN clause and with to the query using the PuzzleParent relation
 * @method     ChildPuzzleQuery rightJoinWithPuzzleParent() Adds a RIGHT JOIN clause and with to the query using the PuzzleParent relation
 * @method     ChildPuzzleQuery innerJoinWithPuzzleParent() Adds a INNER JOIN clause and with to the query using the PuzzleParent relation
 *
 * @method     ChildPuzzleQuery leftJoinPuzzleChild($relationAlias = null) Adds a LEFT JOIN clause to the query using the PuzzleChild relation
 * @method     ChildPuzzleQuery rightJoinPuzzleChild($relationAlias = null) Adds a RIGHT JOIN clause to the query using the PuzzleChild relation
 * @method     ChildPuzzleQuery innerJoinPuzzleChild($relationAlias = null) Adds a INNER JOIN clause to the query using the PuzzleChild relation
 *
 * @method     ChildPuzzleQuery joinWithPuzzleChild($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the PuzzleChild relation
 *
 * @method     ChildPuzzleQuery leftJoinWithPuzzleChild() Adds a LEFT JOIN clause and with to the query using the PuzzleChild relation
 * @method     ChildPuzzleQuery rightJoinWithPuzzleChild() Adds a RIGHT JOIN clause and with to the query using the PuzzleChild relation
 * @method     ChildPuzzleQuery innerJoinWithPuzzleChild() Adds a INNER JOIN clause and with to the query using the PuzzleChild relation
 *
 * @method     ChildPuzzleQuery leftJoinNews($relationAlias = null) Adds a LEFT JOIN clause to the query using the News relation
 * @method     ChildPuzzleQuery rightJoinNews($relationAlias = null) Adds a RIGHT JOIN clause to the query using the News relation
 * @method     ChildPuzzleQuery innerJoinNews($relationAlias = null) Adds a INNER JOIN clause to the query using the News relation
 *
 * @method     ChildPuzzleQuery joinWithNews($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the News relation
 *
 * @method     ChildPuzzleQuery leftJoinWithNews() Adds a LEFT JOIN clause and with to the query using the News relation
 * @method     ChildPuzzleQuery rightJoinWithNews() Adds a RIGHT JOIN clause and with to the query using the News relation
 * @method     ChildPuzzleQuery innerJoinWithNews() Adds a INNER JOIN clause and with to the query using the News relation
 *
 * @method     \NoteQuery|\PuzzleMemberQuery|\PuzzlePuzzleQuery|\NewsQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildPuzzle findOne(ConnectionInterface $con = null) Return the first ChildPuzzle matching the query
 * @method     ChildPuzzle findOneOrCreate(ConnectionInterface $con = null) Return the first ChildPuzzle matching the query, or a new ChildPuzzle object populated from the query conditions when no match is found
 *
 * @method     ChildPuzzle findOneById(int $id) Return the first ChildPuzzle filtered by the id column
 * @method     ChildPuzzle findOneByTitle(string $title) Return the first ChildPuzzle filtered by the title column
 * @method     ChildPuzzle findOneByUrl(string $url) Return the first ChildPuzzle filtered by the url column
 * @method     ChildPuzzle findOneBySpreadsheetId(string $spreadsheet_id) Return the first ChildPuzzle filtered by the spreadsheet_id column
 * @method     ChildPuzzle findOneBySolution(string $solution) Return the first ChildPuzzle filtered by the solution column
 * @method     ChildPuzzle findOneByStatus(string $status) Return the first ChildPuzzle filtered by the status column
 * @method     ChildPuzzle findOneBySlackChannel(string $slack_channel) Return the first ChildPuzzle filtered by the slack_channel column
 * @method     ChildPuzzle findOneBySlackChannelId(string $slack_channel_id) Return the first ChildPuzzle filtered by the slack_channel_id column
 * @method     ChildPuzzle findOneByPostCount(int $post_count) Return the first ChildPuzzle filtered by the post_count column
 * @method     ChildPuzzle findOneByCreatedAt(string $created_at) Return the first ChildPuzzle filtered by the created_at column
 * @method     ChildPuzzle findOneByUpdatedAt(string $updated_at) Return the first ChildPuzzle filtered by the updated_at column *

 * @method     ChildPuzzle requirePk($key, ConnectionInterface $con = null) Return the ChildPuzzle by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildPuzzle requireOne(ConnectionInterface $con = null) Return the first ChildPuzzle matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildPuzzle requireOneById(int $id) Return the first ChildPuzzle filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildPuzzle requireOneByTitle(string $title) Return the first ChildPuzzle filtered by the title column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildPuzzle requireOneByUrl(string $url) Return the first ChildPuzzle filtered by the url column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildPuzzle requireOneBySpreadsheetId(string $spreadsheet_id) Return the first ChildPuzzle filtered by the spreadsheet_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildPuzzle requireOneBySolution(string $solution) Return the first ChildPuzzle filtered by the solution column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildPuzzle requireOneByStatus(string $status) Return the first ChildPuzzle filtered by the status column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildPuzzle requireOneBySlackChannel(string $slack_channel) Return the first ChildPuzzle filtered by the slack_channel column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildPuzzle requireOneBySlackChannelId(string $slack_channel_id) Return the first ChildPuzzle filtered by the slack_channel_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildPuzzle requireOneByPostCount(int $post_count) Return the first ChildPuzzle filtered by the post_count column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildPuzzle requireOneByCreatedAt(string $created_at) Return the first ChildPuzzle filtered by the created_at column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildPuzzle requireOneByUpdatedAt(string $updated_at) Return the first ChildPuzzle filtered by the updated_at column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildPuzzle[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildPuzzle objects based on current ModelCriteria
 * @method     ChildPuzzle[]|ObjectCollection findById(int $id) Return ChildPuzzle objects filtered by the id column
 * @method     ChildPuzzle[]|ObjectCollection findByTitle(string $title) Return ChildPuzzle objects filtered by the title column
 * @method     ChildPuzzle[]|ObjectCollection findByUrl(string $url) Return ChildPuzzle objects filtered by the url column
 * @method     ChildPuzzle[]|ObjectCollection findBySpreadsheetId(string $spreadsheet_id) Return ChildPuzzle objects filtered by the spreadsheet_id column
 * @method     ChildPuzzle[]|ObjectCollection findBySolution(string $solution) Return ChildPuzzle objects filtered by the solution column
 * @method     ChildPuzzle[]|ObjectCollection findByStatus(string $status) Return ChildPuzzle objects filtered by the status column
 * @method     ChildPuzzle[]|ObjectCollection findBySlackChannel(string $slack_channel) Return ChildPuzzle objects filtered by the slack_channel column
 * @method     ChildPuzzle[]|ObjectCollection findBySlackChannelId(string $slack_channel_id) Return ChildPuzzle objects filtered by the slack_channel_id column
 * @method     ChildPuzzle[]|ObjectCollection findByPostCount(int $post_count) Return ChildPuzzle objects filtered by the post_count column
 * @method     ChildPuzzle[]|ObjectCollection findByCreatedAt(string $created_at) Return ChildPuzzle objects filtered by the created_at column
 * @method     ChildPuzzle[]|ObjectCollection findByUpdatedAt(string $updated_at) Return ChildPuzzle objects filtered by the updated_at column
 * @method     ChildPuzzle[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class PuzzleQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \Base\PuzzleQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'palindrome', $modelName = '\\Puzzle', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildPuzzleQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildPuzzleQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildPuzzleQuery) {
            return $criteria;
        }
        $query = new ChildPuzzleQuery();
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
     * @return ChildPuzzle|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(PuzzleTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = PuzzleTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key)))) {
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
     * @return ChildPuzzle A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, title, url, spreadsheet_id, solution, status, slack_channel, slack_channel_id, post_count, created_at, updated_at FROM puzzle WHERE id = :p0';
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
            /** @var ChildPuzzle $obj */
            $obj = new ChildPuzzle();
            $obj->hydrate($row);
            PuzzleTableMap::addInstanceToPool($obj, null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key);
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
     * @return ChildPuzzle|array|mixed the result, formatted by the current formatter
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
     * @return $this|ChildPuzzleQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(PuzzleTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildPuzzleQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(PuzzleTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return $this|ChildPuzzleQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(PuzzleTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(PuzzleTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PuzzleTableMap::COL_ID, $id, $comparison);
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
     * @return $this|ChildPuzzleQuery The current query, for fluid interface
     */
    public function filterByTitle($title = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($title)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PuzzleTableMap::COL_TITLE, $title, $comparison);
    }

    /**
     * Filter the query on the url column
     *
     * Example usage:
     * <code>
     * $query->filterByUrl('fooValue');   // WHERE url = 'fooValue'
     * $query->filterByUrl('%fooValue%', Criteria::LIKE); // WHERE url LIKE '%fooValue%'
     * </code>
     *
     * @param     string $url The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildPuzzleQuery The current query, for fluid interface
     */
    public function filterByUrl($url = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($url)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PuzzleTableMap::COL_URL, $url, $comparison);
    }

    /**
     * Filter the query on the spreadsheet_id column
     *
     * Example usage:
     * <code>
     * $query->filterBySpreadsheetId('fooValue');   // WHERE spreadsheet_id = 'fooValue'
     * $query->filterBySpreadsheetId('%fooValue%', Criteria::LIKE); // WHERE spreadsheet_id LIKE '%fooValue%'
     * </code>
     *
     * @param     string $spreadsheetId The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildPuzzleQuery The current query, for fluid interface
     */
    public function filterBySpreadsheetId($spreadsheetId = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($spreadsheetId)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PuzzleTableMap::COL_SPREADSHEET_ID, $spreadsheetId, $comparison);
    }

    /**
     * Filter the query on the solution column
     *
     * Example usage:
     * <code>
     * $query->filterBySolution('fooValue');   // WHERE solution = 'fooValue'
     * $query->filterBySolution('%fooValue%', Criteria::LIKE); // WHERE solution LIKE '%fooValue%'
     * </code>
     *
     * @param     string $solution The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildPuzzleQuery The current query, for fluid interface
     */
    public function filterBySolution($solution = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($solution)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PuzzleTableMap::COL_SOLUTION, $solution, $comparison);
    }

    /**
     * Filter the query on the status column
     *
     * Example usage:
     * <code>
     * $query->filterByStatus('fooValue');   // WHERE status = 'fooValue'
     * $query->filterByStatus('%fooValue%', Criteria::LIKE); // WHERE status LIKE '%fooValue%'
     * </code>
     *
     * @param     string $status The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildPuzzleQuery The current query, for fluid interface
     */
    public function filterByStatus($status = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($status)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PuzzleTableMap::COL_STATUS, $status, $comparison);
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
     * @return $this|ChildPuzzleQuery The current query, for fluid interface
     */
    public function filterBySlackChannel($slackChannel = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($slackChannel)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PuzzleTableMap::COL_SLACK_CHANNEL, $slackChannel, $comparison);
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
     * @return $this|ChildPuzzleQuery The current query, for fluid interface
     */
    public function filterBySlackChannelId($slackChannelId = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($slackChannelId)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PuzzleTableMap::COL_SLACK_CHANNEL_ID, $slackChannelId, $comparison);
    }

    /**
     * Filter the query on the post_count column
     *
     * Example usage:
     * <code>
     * $query->filterByPostCount(1234); // WHERE post_count = 1234
     * $query->filterByPostCount(array(12, 34)); // WHERE post_count IN (12, 34)
     * $query->filterByPostCount(array('min' => 12)); // WHERE post_count > 12
     * </code>
     *
     * @param     mixed $postCount The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildPuzzleQuery The current query, for fluid interface
     */
    public function filterByPostCount($postCount = null, $comparison = null)
    {
        if (is_array($postCount)) {
            $useMinMax = false;
            if (isset($postCount['min'])) {
                $this->addUsingAlias(PuzzleTableMap::COL_POST_COUNT, $postCount['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($postCount['max'])) {
                $this->addUsingAlias(PuzzleTableMap::COL_POST_COUNT, $postCount['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PuzzleTableMap::COL_POST_COUNT, $postCount, $comparison);
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
     * @return $this|ChildPuzzleQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(PuzzleTableMap::COL_CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(PuzzleTableMap::COL_CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PuzzleTableMap::COL_CREATED_AT, $createdAt, $comparison);
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
     * @return $this|ChildPuzzleQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(PuzzleTableMap::COL_UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(PuzzleTableMap::COL_UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PuzzleTableMap::COL_UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related \Note object
     *
     * @param \Note|ObjectCollection $note the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildPuzzleQuery The current query, for fluid interface
     */
    public function filterByNote($note, $comparison = null)
    {
        if ($note instanceof \Note) {
            return $this
                ->addUsingAlias(PuzzleTableMap::COL_ID, $note->getPuzzleId(), $comparison);
        } elseif ($note instanceof ObjectCollection) {
            return $this
                ->useNoteQuery()
                ->filterByPrimaryKeys($note->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByNote() only accepts arguments of type \Note or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Note relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildPuzzleQuery The current query, for fluid interface
     */
    public function joinNote($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Note');

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
            $this->addJoinObject($join, 'Note');
        }

        return $this;
    }

    /**
     * Use the Note relation Note object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \NoteQuery A secondary query class using the current class as primary query
     */
    public function useNoteQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinNote($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Note', '\NoteQuery');
    }

    /**
     * Filter the query by a related \PuzzleMember object
     *
     * @param \PuzzleMember|ObjectCollection $puzzleMember the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildPuzzleQuery The current query, for fluid interface
     */
    public function filterByPuzzleMember($puzzleMember, $comparison = null)
    {
        if ($puzzleMember instanceof \PuzzleMember) {
            return $this
                ->addUsingAlias(PuzzleTableMap::COL_ID, $puzzleMember->getPuzzleId(), $comparison);
        } elseif ($puzzleMember instanceof ObjectCollection) {
            return $this
                ->usePuzzleMemberQuery()
                ->filterByPrimaryKeys($puzzleMember->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByPuzzleMember() only accepts arguments of type \PuzzleMember or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the PuzzleMember relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildPuzzleQuery The current query, for fluid interface
     */
    public function joinPuzzleMember($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('PuzzleMember');

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
            $this->addJoinObject($join, 'PuzzleMember');
        }

        return $this;
    }

    /**
     * Use the PuzzleMember relation PuzzleMember object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \PuzzleMemberQuery A secondary query class using the current class as primary query
     */
    public function usePuzzleMemberQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinPuzzleMember($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'PuzzleMember', '\PuzzleMemberQuery');
    }

    /**
     * Filter the query by a related \PuzzlePuzzle object
     *
     * @param \PuzzlePuzzle|ObjectCollection $puzzlePuzzle the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildPuzzleQuery The current query, for fluid interface
     */
    public function filterByPuzzleParent($puzzlePuzzle, $comparison = null)
    {
        if ($puzzlePuzzle instanceof \PuzzlePuzzle) {
            return $this
                ->addUsingAlias(PuzzleTableMap::COL_ID, $puzzlePuzzle->getPuzzleId(), $comparison);
        } elseif ($puzzlePuzzle instanceof ObjectCollection) {
            return $this
                ->usePuzzleParentQuery()
                ->filterByPrimaryKeys($puzzlePuzzle->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByPuzzleParent() only accepts arguments of type \PuzzlePuzzle or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the PuzzleParent relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildPuzzleQuery The current query, for fluid interface
     */
    public function joinPuzzleParent($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('PuzzleParent');

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
            $this->addJoinObject($join, 'PuzzleParent');
        }

        return $this;
    }

    /**
     * Use the PuzzleParent relation PuzzlePuzzle object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \PuzzlePuzzleQuery A secondary query class using the current class as primary query
     */
    public function usePuzzleParentQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinPuzzleParent($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'PuzzleParent', '\PuzzlePuzzleQuery');
    }

    /**
     * Filter the query by a related \PuzzlePuzzle object
     *
     * @param \PuzzlePuzzle|ObjectCollection $puzzlePuzzle the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildPuzzleQuery The current query, for fluid interface
     */
    public function filterByPuzzleChild($puzzlePuzzle, $comparison = null)
    {
        if ($puzzlePuzzle instanceof \PuzzlePuzzle) {
            return $this
                ->addUsingAlias(PuzzleTableMap::COL_ID, $puzzlePuzzle->getParentId(), $comparison);
        } elseif ($puzzlePuzzle instanceof ObjectCollection) {
            return $this
                ->usePuzzleChildQuery()
                ->filterByPrimaryKeys($puzzlePuzzle->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByPuzzleChild() only accepts arguments of type \PuzzlePuzzle or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the PuzzleChild relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildPuzzleQuery The current query, for fluid interface
     */
    public function joinPuzzleChild($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('PuzzleChild');

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
            $this->addJoinObject($join, 'PuzzleChild');
        }

        return $this;
    }

    /**
     * Use the PuzzleChild relation PuzzlePuzzle object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \PuzzlePuzzleQuery A secondary query class using the current class as primary query
     */
    public function usePuzzleChildQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinPuzzleChild($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'PuzzleChild', '\PuzzlePuzzleQuery');
    }

    /**
     * Filter the query by a related \News object
     *
     * @param \News|ObjectCollection $news the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildPuzzleQuery The current query, for fluid interface
     */
    public function filterByNews($news, $comparison = null)
    {
        if ($news instanceof \News) {
            return $this
                ->addUsingAlias(PuzzleTableMap::COL_ID, $news->getPuzzleId(), $comparison);
        } elseif ($news instanceof ObjectCollection) {
            return $this
                ->useNewsQuery()
                ->filterByPrimaryKeys($news->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByNews() only accepts arguments of type \News or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the News relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildPuzzleQuery The current query, for fluid interface
     */
    public function joinNews($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('News');

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
            $this->addJoinObject($join, 'News');
        }

        return $this;
    }

    /**
     * Use the News relation News object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \NewsQuery A secondary query class using the current class as primary query
     */
    public function useNewsQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinNews($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'News', '\NewsQuery');
    }

    /**
     * Filter the query by a related Member object
     * using the solver table as cross reference
     *
     * @param Member $member the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildPuzzleQuery The current query, for fluid interface
     */
    public function filterByMember($member, $comparison = Criteria::EQUAL)
    {
        return $this
            ->usePuzzleMemberQuery()
            ->filterByMember($member, $comparison)
            ->endUse();
    }

    /**
     * Filter the query by a related Puzzle object
     * using the relationship table as cross reference
     *
     * @param Puzzle $puzzle the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildPuzzleQuery The current query, for fluid interface
     */
    public function filterByParent($puzzle, $comparison = Criteria::EQUAL)
    {
        return $this
            ->usePuzzleParentQuery()
            ->filterByParent($puzzle, $comparison)
            ->endUse();
    }

    /**
     * Filter the query by a related Puzzle object
     * using the relationship table as cross reference
     *
     * @param Puzzle $puzzle the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildPuzzleQuery The current query, for fluid interface
     */
    public function filterByChild($puzzle, $comparison = Criteria::EQUAL)
    {
        return $this
            ->usePuzzleChildQuery()
            ->filterByChild($puzzle, $comparison)
            ->endUse();
    }

    /**
     * Exclude object from result
     *
     * @param   ChildPuzzle $puzzle Object to remove from the list of results
     *
     * @return $this|ChildPuzzleQuery The current query, for fluid interface
     */
    public function prune($puzzle = null)
    {
        if ($puzzle) {
            $this->addUsingAlias(PuzzleTableMap::COL_ID, $puzzle->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the puzzle table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(PuzzleTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            PuzzleTableMap::clearInstancePool();
            PuzzleTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(PuzzleTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(PuzzleTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            PuzzleTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            PuzzleTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     $this|ChildPuzzleQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(PuzzleTableMap::COL_UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     $this|ChildPuzzleQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(PuzzleTableMap::COL_UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return     $this|ChildPuzzleQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(PuzzleTableMap::COL_UPDATED_AT);
    }

    /**
     * Order by create date desc
     *
     * @return     $this|ChildPuzzleQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(PuzzleTableMap::COL_CREATED_AT);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     $this|ChildPuzzleQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(PuzzleTableMap::COL_CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by create date asc
     *
     * @return     $this|ChildPuzzleQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(PuzzleTableMap::COL_CREATED_AT);
    }

} // PuzzleQuery
