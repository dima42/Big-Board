<?php

namespace Base;

use \Member as ChildMember;
use \MemberQuery as ChildMemberQuery;
use \Exception;
use \PDO;
use Map\MemberTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'member' table.
 *
 *
 *
 * @method     ChildMemberQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildMemberQuery orderByFullName($order = Criteria::ASC) Order by the full_name column
 * @method     ChildMemberQuery orderByGoogleId($order = Criteria::ASC) Order by the google_id column
 * @method     ChildMemberQuery orderByGoogleRefresh($order = Criteria::ASC) Order by the google_refresh column
 * @method     ChildMemberQuery orderBySlackId($order = Criteria::ASC) Order by the slack_id column
 * @method     ChildMemberQuery orderBySlackHandle($order = Criteria::ASC) Order by the slack_handle column
 * @method     ChildMemberQuery orderByStrengths($order = Criteria::ASC) Order by the strengths column
 *
 * @method     ChildMemberQuery groupById() Group by the id column
 * @method     ChildMemberQuery groupByFullName() Group by the full_name column
 * @method     ChildMemberQuery groupByGoogleId() Group by the google_id column
 * @method     ChildMemberQuery groupByGoogleRefresh() Group by the google_refresh column
 * @method     ChildMemberQuery groupBySlackId() Group by the slack_id column
 * @method     ChildMemberQuery groupBySlackHandle() Group by the slack_handle column
 * @method     ChildMemberQuery groupByStrengths() Group by the strengths column
 *
 * @method     ChildMemberQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildMemberQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildMemberQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildMemberQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildMemberQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildMemberQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildMemberQuery leftJoinNote($relationAlias = null) Adds a LEFT JOIN clause to the query using the Note relation
 * @method     ChildMemberQuery rightJoinNote($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Note relation
 * @method     ChildMemberQuery innerJoinNote($relationAlias = null) Adds a INNER JOIN clause to the query using the Note relation
 *
 * @method     ChildMemberQuery joinWithNote($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Note relation
 *
 * @method     ChildMemberQuery leftJoinWithNote() Adds a LEFT JOIN clause and with to the query using the Note relation
 * @method     ChildMemberQuery rightJoinWithNote() Adds a RIGHT JOIN clause and with to the query using the Note relation
 * @method     ChildMemberQuery innerJoinWithNote() Adds a INNER JOIN clause and with to the query using the Note relation
 *
 * @method     ChildMemberQuery leftJoinPuzzleMember($relationAlias = null) Adds a LEFT JOIN clause to the query using the PuzzleMember relation
 * @method     ChildMemberQuery rightJoinPuzzleMember($relationAlias = null) Adds a RIGHT JOIN clause to the query using the PuzzleMember relation
 * @method     ChildMemberQuery innerJoinPuzzleMember($relationAlias = null) Adds a INNER JOIN clause to the query using the PuzzleMember relation
 *
 * @method     ChildMemberQuery joinWithPuzzleMember($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the PuzzleMember relation
 *
 * @method     ChildMemberQuery leftJoinWithPuzzleMember() Adds a LEFT JOIN clause and with to the query using the PuzzleMember relation
 * @method     ChildMemberQuery rightJoinWithPuzzleMember() Adds a RIGHT JOIN clause and with to the query using the PuzzleMember relation
 * @method     ChildMemberQuery innerJoinWithPuzzleMember() Adds a INNER JOIN clause and with to the query using the PuzzleMember relation
 *
 * @method     ChildMemberQuery leftJoinNews($relationAlias = null) Adds a LEFT JOIN clause to the query using the News relation
 * @method     ChildMemberQuery rightJoinNews($relationAlias = null) Adds a RIGHT JOIN clause to the query using the News relation
 * @method     ChildMemberQuery innerJoinNews($relationAlias = null) Adds a INNER JOIN clause to the query using the News relation
 *
 * @method     ChildMemberQuery joinWithNews($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the News relation
 *
 * @method     ChildMemberQuery leftJoinWithNews() Adds a LEFT JOIN clause and with to the query using the News relation
 * @method     ChildMemberQuery rightJoinWithNews() Adds a RIGHT JOIN clause and with to the query using the News relation
 * @method     ChildMemberQuery innerJoinWithNews() Adds a INNER JOIN clause and with to the query using the News relation
 *
 * @method     \NoteQuery|\PuzzleMemberQuery|\NewsQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildMember findOne(ConnectionInterface $con = null) Return the first ChildMember matching the query
 * @method     ChildMember findOneOrCreate(ConnectionInterface $con = null) Return the first ChildMember matching the query, or a new ChildMember object populated from the query conditions when no match is found
 *
 * @method     ChildMember findOneById(int $id) Return the first ChildMember filtered by the id column
 * @method     ChildMember findOneByFullName(string $full_name) Return the first ChildMember filtered by the full_name column
 * @method     ChildMember findOneByGoogleId(string $google_id) Return the first ChildMember filtered by the google_id column
 * @method     ChildMember findOneByGoogleRefresh(string $google_refresh) Return the first ChildMember filtered by the google_refresh column
 * @method     ChildMember findOneBySlackId(string $slack_id) Return the first ChildMember filtered by the slack_id column
 * @method     ChildMember findOneBySlackHandle(string $slack_handle) Return the first ChildMember filtered by the slack_handle column
 * @method     ChildMember findOneByStrengths(string $strengths) Return the first ChildMember filtered by the strengths column *

 * @method     ChildMember requirePk($key, ConnectionInterface $con = null) Return the ChildMember by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildMember requireOne(ConnectionInterface $con = null) Return the first ChildMember matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildMember requireOneById(int $id) Return the first ChildMember filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildMember requireOneByFullName(string $full_name) Return the first ChildMember filtered by the full_name column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildMember requireOneByGoogleId(string $google_id) Return the first ChildMember filtered by the google_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildMember requireOneByGoogleRefresh(string $google_refresh) Return the first ChildMember filtered by the google_refresh column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildMember requireOneBySlackId(string $slack_id) Return the first ChildMember filtered by the slack_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildMember requireOneBySlackHandle(string $slack_handle) Return the first ChildMember filtered by the slack_handle column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildMember requireOneByStrengths(string $strengths) Return the first ChildMember filtered by the strengths column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildMember[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildMember objects based on current ModelCriteria
 * @method     ChildMember[]|ObjectCollection findById(int $id) Return ChildMember objects filtered by the id column
 * @method     ChildMember[]|ObjectCollection findByFullName(string $full_name) Return ChildMember objects filtered by the full_name column
 * @method     ChildMember[]|ObjectCollection findByGoogleId(string $google_id) Return ChildMember objects filtered by the google_id column
 * @method     ChildMember[]|ObjectCollection findByGoogleRefresh(string $google_refresh) Return ChildMember objects filtered by the google_refresh column
 * @method     ChildMember[]|ObjectCollection findBySlackId(string $slack_id) Return ChildMember objects filtered by the slack_id column
 * @method     ChildMember[]|ObjectCollection findBySlackHandle(string $slack_handle) Return ChildMember objects filtered by the slack_handle column
 * @method     ChildMember[]|ObjectCollection findByStrengths(string $strengths) Return ChildMember objects filtered by the strengths column
 * @method     ChildMember[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class MemberQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \Base\MemberQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'palindrome', $modelName = '\\Member', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildMemberQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildMemberQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildMemberQuery) {
            return $criteria;
        }
        $query = new ChildMemberQuery();
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
     * @return ChildMember|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(MemberTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = MemberTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key)))) {
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
     * @return ChildMember A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, full_name, google_id, google_refresh, slack_id, slack_handle, strengths FROM member WHERE id = :p0';
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
            /** @var ChildMember $obj */
            $obj = new ChildMember();
            $obj->hydrate($row);
            MemberTableMap::addInstanceToPool($obj, null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key);
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
     * @return ChildMember|array|mixed the result, formatted by the current formatter
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
     * @return $this|ChildMemberQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(MemberTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildMemberQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(MemberTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return $this|ChildMemberQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(MemberTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(MemberTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MemberTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the full_name column
     *
     * Example usage:
     * <code>
     * $query->filterByFullName('fooValue');   // WHERE full_name = 'fooValue'
     * $query->filterByFullName('%fooValue%', Criteria::LIKE); // WHERE full_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $fullName The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildMemberQuery The current query, for fluid interface
     */
    public function filterByFullName($fullName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($fullName)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MemberTableMap::COL_FULL_NAME, $fullName, $comparison);
    }

    /**
     * Filter the query on the google_id column
     *
     * Example usage:
     * <code>
     * $query->filterByGoogleId('fooValue');   // WHERE google_id = 'fooValue'
     * $query->filterByGoogleId('%fooValue%', Criteria::LIKE); // WHERE google_id LIKE '%fooValue%'
     * </code>
     *
     * @param     string $googleId The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildMemberQuery The current query, for fluid interface
     */
    public function filterByGoogleId($googleId = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($googleId)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MemberTableMap::COL_GOOGLE_ID, $googleId, $comparison);
    }

    /**
     * Filter the query on the google_refresh column
     *
     * Example usage:
     * <code>
     * $query->filterByGoogleRefresh('fooValue');   // WHERE google_refresh = 'fooValue'
     * $query->filterByGoogleRefresh('%fooValue%', Criteria::LIKE); // WHERE google_refresh LIKE '%fooValue%'
     * </code>
     *
     * @param     string $googleRefresh The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildMemberQuery The current query, for fluid interface
     */
    public function filterByGoogleRefresh($googleRefresh = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($googleRefresh)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MemberTableMap::COL_GOOGLE_REFRESH, $googleRefresh, $comparison);
    }

    /**
     * Filter the query on the slack_id column
     *
     * Example usage:
     * <code>
     * $query->filterBySlackId('fooValue');   // WHERE slack_id = 'fooValue'
     * $query->filterBySlackId('%fooValue%', Criteria::LIKE); // WHERE slack_id LIKE '%fooValue%'
     * </code>
     *
     * @param     string $slackId The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildMemberQuery The current query, for fluid interface
     */
    public function filterBySlackId($slackId = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($slackId)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MemberTableMap::COL_SLACK_ID, $slackId, $comparison);
    }

    /**
     * Filter the query on the slack_handle column
     *
     * Example usage:
     * <code>
     * $query->filterBySlackHandle('fooValue');   // WHERE slack_handle = 'fooValue'
     * $query->filterBySlackHandle('%fooValue%', Criteria::LIKE); // WHERE slack_handle LIKE '%fooValue%'
     * </code>
     *
     * @param     string $slackHandle The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildMemberQuery The current query, for fluid interface
     */
    public function filterBySlackHandle($slackHandle = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($slackHandle)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MemberTableMap::COL_SLACK_HANDLE, $slackHandle, $comparison);
    }

    /**
     * Filter the query on the strengths column
     *
     * Example usage:
     * <code>
     * $query->filterByStrengths('fooValue');   // WHERE strengths = 'fooValue'
     * $query->filterByStrengths('%fooValue%', Criteria::LIKE); // WHERE strengths LIKE '%fooValue%'
     * </code>
     *
     * @param     string $strengths The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildMemberQuery The current query, for fluid interface
     */
    public function filterByStrengths($strengths = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($strengths)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MemberTableMap::COL_STRENGTHS, $strengths, $comparison);
    }

    /**
     * Filter the query by a related \Note object
     *
     * @param \Note|ObjectCollection $note the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildMemberQuery The current query, for fluid interface
     */
    public function filterByNote($note, $comparison = null)
    {
        if ($note instanceof \Note) {
            return $this
                ->addUsingAlias(MemberTableMap::COL_ID, $note->getMemberId(), $comparison);
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
     * @return $this|ChildMemberQuery The current query, for fluid interface
     */
    public function joinNote($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
    public function useNoteQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
     * @return ChildMemberQuery The current query, for fluid interface
     */
    public function filterByPuzzleMember($puzzleMember, $comparison = null)
    {
        if ($puzzleMember instanceof \PuzzleMember) {
            return $this
                ->addUsingAlias(MemberTableMap::COL_ID, $puzzleMember->getMemberId(), $comparison);
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
     * @return $this|ChildMemberQuery The current query, for fluid interface
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
     * Filter the query by a related \News object
     *
     * @param \News|ObjectCollection $news the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildMemberQuery The current query, for fluid interface
     */
    public function filterByNews($news, $comparison = null)
    {
        if ($news instanceof \News) {
            return $this
                ->addUsingAlias(MemberTableMap::COL_ID, $news->getMemberId(), $comparison);
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
     * @return $this|ChildMemberQuery The current query, for fluid interface
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
     * Filter the query by a related Puzzle object
     * using the solver table as cross reference
     *
     * @param Puzzle $puzzle the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildMemberQuery The current query, for fluid interface
     */
    public function filterByPuzzle($puzzle, $comparison = Criteria::EQUAL)
    {
        return $this
            ->usePuzzleMemberQuery()
            ->filterByPuzzle($puzzle, $comparison)
            ->endUse();
    }

    /**
     * Exclude object from result
     *
     * @param   ChildMember $member Object to remove from the list of results
     *
     * @return $this|ChildMemberQuery The current query, for fluid interface
     */
    public function prune($member = null)
    {
        if ($member) {
            $this->addUsingAlias(MemberTableMap::COL_ID, $member->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the member table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(MemberTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            MemberTableMap::clearInstancePool();
            MemberTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(MemberTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(MemberTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            MemberTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            MemberTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // MemberQuery
