<?php

namespace Map;

use \Puzzle;
use \PuzzleQuery;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\InstancePoolTrait;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\DataFetcher\DataFetcherInterface;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\RelationMap;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Map\TableMapTrait;


/**
 * This class defines the structure of the 'puzzle' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class PuzzleTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = '.Map.PuzzleTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'palindrome';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'puzzle';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Puzzle';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Puzzle';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 14;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 14;

    /**
     * the column name for the id field
     */
    const COL_ID = 'puzzle.id';

    /**
     * the column name for the title field
     */
    const COL_TITLE = 'puzzle.title';

    /**
     * the column name for the url field
     */
    const COL_URL = 'puzzle.url';

    /**
     * the column name for the spreadsheet_id field
     */
    const COL_SPREADSHEET_ID = 'puzzle.spreadsheet_id';

    /**
     * the column name for the solution field
     */
    const COL_SOLUTION = 'puzzle.solution';

    /**
     * the column name for the status field
     */
    const COL_STATUS = 'puzzle.status';

    /**
     * the column name for the slack_channel field
     */
    const COL_SLACK_CHANNEL = 'puzzle.slack_channel';

    /**
     * the column name for the slack_channel_id field
     */
    const COL_SLACK_CHANNEL_ID = 'puzzle.slack_channel_id';

    /**
     * the column name for the wrangler_id field
     */
    const COL_WRANGLER_ID = 'puzzle.wrangler_id';

    /**
     * the column name for the sheet_mod_date field
     */
    const COL_SHEET_MOD_DATE = 'puzzle.sheet_mod_date';

    /**
     * the column name for the post_count field
     */
    const COL_POST_COUNT = 'puzzle.post_count';

    /**
     * the column name for the solver_count field
     */
    const COL_SOLVER_COUNT = 'puzzle.solver_count';

    /**
     * the column name for the created_at field
     */
    const COL_CREATED_AT = 'puzzle.created_at';

    /**
     * the column name for the updated_at field
     */
    const COL_UPDATED_AT = 'puzzle.updated_at';

    /**
     * The default string format for model objects of the related table
     */
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        self::TYPE_PHPNAME       => array('Id', 'Title', 'Url', 'SpreadsheetId', 'Solution', 'Status', 'SlackChannel', 'SlackChannelId', 'WranglerId', 'SheetModDate', 'PostCount', 'SolverCount', 'CreatedAt', 'UpdatedAt', ),
        self::TYPE_CAMELNAME     => array('id', 'title', 'url', 'spreadsheetId', 'solution', 'status', 'slackChannel', 'slackChannelId', 'wranglerId', 'sheetModDate', 'postCount', 'solverCount', 'createdAt', 'updatedAt', ),
        self::TYPE_COLNAME       => array(PuzzleTableMap::COL_ID, PuzzleTableMap::COL_TITLE, PuzzleTableMap::COL_URL, PuzzleTableMap::COL_SPREADSHEET_ID, PuzzleTableMap::COL_SOLUTION, PuzzleTableMap::COL_STATUS, PuzzleTableMap::COL_SLACK_CHANNEL, PuzzleTableMap::COL_SLACK_CHANNEL_ID, PuzzleTableMap::COL_WRANGLER_ID, PuzzleTableMap::COL_SHEET_MOD_DATE, PuzzleTableMap::COL_POST_COUNT, PuzzleTableMap::COL_SOLVER_COUNT, PuzzleTableMap::COL_CREATED_AT, PuzzleTableMap::COL_UPDATED_AT, ),
        self::TYPE_FIELDNAME     => array('id', 'title', 'url', 'spreadsheet_id', 'solution', 'status', 'slack_channel', 'slack_channel_id', 'wrangler_id', 'sheet_mod_date', 'post_count', 'solver_count', 'created_at', 'updated_at', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'Title' => 1, 'Url' => 2, 'SpreadsheetId' => 3, 'Solution' => 4, 'Status' => 5, 'SlackChannel' => 6, 'SlackChannelId' => 7, 'WranglerId' => 8, 'SheetModDate' => 9, 'PostCount' => 10, 'SolverCount' => 11, 'CreatedAt' => 12, 'UpdatedAt' => 13, ),
        self::TYPE_CAMELNAME     => array('id' => 0, 'title' => 1, 'url' => 2, 'spreadsheetId' => 3, 'solution' => 4, 'status' => 5, 'slackChannel' => 6, 'slackChannelId' => 7, 'wranglerId' => 8, 'sheetModDate' => 9, 'postCount' => 10, 'solverCount' => 11, 'createdAt' => 12, 'updatedAt' => 13, ),
        self::TYPE_COLNAME       => array(PuzzleTableMap::COL_ID => 0, PuzzleTableMap::COL_TITLE => 1, PuzzleTableMap::COL_URL => 2, PuzzleTableMap::COL_SPREADSHEET_ID => 3, PuzzleTableMap::COL_SOLUTION => 4, PuzzleTableMap::COL_STATUS => 5, PuzzleTableMap::COL_SLACK_CHANNEL => 6, PuzzleTableMap::COL_SLACK_CHANNEL_ID => 7, PuzzleTableMap::COL_WRANGLER_ID => 8, PuzzleTableMap::COL_SHEET_MOD_DATE => 9, PuzzleTableMap::COL_POST_COUNT => 10, PuzzleTableMap::COL_SOLVER_COUNT => 11, PuzzleTableMap::COL_CREATED_AT => 12, PuzzleTableMap::COL_UPDATED_AT => 13, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'title' => 1, 'url' => 2, 'spreadsheet_id' => 3, 'solution' => 4, 'status' => 5, 'slack_channel' => 6, 'slack_channel_id' => 7, 'wrangler_id' => 8, 'sheet_mod_date' => 9, 'post_count' => 10, 'solver_count' => 11, 'created_at' => 12, 'updated_at' => 13, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, )
    );

    /**
     * Initialize the table attributes and columns
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('puzzle');
        $this->setPhpName('Puzzle');
        $this->setIdentifierQuoting(false);
        $this->setClassName('\\Puzzle');
        $this->setPackage('');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('title', 'Title', 'VARCHAR', false, 128, null);
        $this->addColumn('url', 'Url', 'VARCHAR', false, 128, null);
        $this->addColumn('spreadsheet_id', 'SpreadsheetId', 'VARCHAR', false, 128, null);
        $this->addColumn('solution', 'Solution', 'VARCHAR', false, 128, null);
        $this->addColumn('status', 'Status', 'VARCHAR', false, 24, null);
        $this->addColumn('slack_channel', 'SlackChannel', 'VARCHAR', false, 48, null);
        $this->addColumn('slack_channel_id', 'SlackChannelId', 'VARCHAR', false, 24, null);
        $this->addForeignKey('wrangler_id', 'WranglerId', 'INTEGER', 'member', 'id', false, null, null);
        $this->addColumn('sheet_mod_date', 'SheetModDate', 'TIMESTAMP', false, null, null);
        $this->addColumn('post_count', 'PostCount', 'INTEGER', false, null, null);
        $this->addColumn('solver_count', 'SolverCount', 'INTEGER', false, null, null);
        $this->addColumn('created_at', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('updated_at', 'UpdatedAt', 'TIMESTAMP', false, null, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Wrangler', '\\Member', RelationMap::MANY_TO_ONE, array (
  0 =>
  array (
    0 => ':wrangler_id',
    1 => ':id',
  ),
), 'SET NULL', null, null, false);
        $this->addRelation('TagAlert', '\\TagAlert', RelationMap::ONE_TO_MANY, array (
  0 =>
  array (
    0 => ':puzzle_id',
    1 => ':id',
  ),
), 'CASCADE', null, 'TagAlerts', false);
        $this->addRelation('Note', '\\Note', RelationMap::ONE_TO_MANY, array (
  0 =>
  array (
    0 => ':puzzle_id',
    1 => ':id',
  ),
), 'SET NULL', null, 'Notes', false);
        $this->addRelation('PuzzleMember', '\\PuzzleMember', RelationMap::ONE_TO_MANY, array (
  0 =>
  array (
    0 => ':puzzle_id',
    1 => ':id',
  ),
), 'CASCADE', null, 'PuzzleMembers', false);
        $this->addRelation('PuzzleParent', '\\PuzzlePuzzle', RelationMap::ONE_TO_MANY, array (
  0 =>
  array (
    0 => ':puzzle_id',
    1 => ':id',
  ),
), 'CASCADE', null, 'PuzzleParents', false);
        $this->addRelation('PuzzleChild', '\\PuzzlePuzzle', RelationMap::ONE_TO_MANY, array (
  0 =>
  array (
    0 => ':parent_id',
    1 => ':id',
  ),
), 'CASCADE', null, 'Puzzlechildren', false);
        $this->addRelation('News', '\\News', RelationMap::ONE_TO_MANY, array (
  0 =>
  array (
    0 => ':puzzle_id',
    1 => ':id',
  ),
), 'CASCADE', null, 'News', false);
        $this->addRelation('Tag', '\\Tag', RelationMap::MANY_TO_MANY, array(), 'CASCADE', null, 'Tags');
        $this->addRelation('Member', '\\Member', RelationMap::MANY_TO_MANY, array(), 'CASCADE', null, 'Members');
        $this->addRelation('Parent', '\\Puzzle', RelationMap::MANY_TO_MANY, array(), 'CASCADE', null, 'Parents');
        $this->addRelation('Child', '\\Puzzle', RelationMap::MANY_TO_MANY, array(), 'CASCADE', null, 'Children');
    } // buildRelations()

    /**
     *
     * Gets the list of behaviors registered for this table
     *
     * @return array Associative array (name => parameters) of behaviors
     */
    public function getBehaviors()
    {
        return array(
            'aggregate_column' => array('name' => 'post_count', 'expression' => 'COUNT(id)', 'condition' => '', 'foreign_table' => 'note', 'foreign_schema' => '', ),
            'solver_count' => array('name' => 'solver_count', 'expression' => 'COUNT(member_id)', 'condition' => '', 'foreign_table' => 'solver', 'foreign_schema' => '', ),
            'timestampable' => array('create_column' => 'created_at', 'update_column' => 'updated_at', 'disable_created_at' => 'false', 'disable_updated_at' => 'false', ),
            'archivable' => array('archive_table' => '', 'archive_phpname' => '', 'archive_class' => '', 'log_archived_at' => 'true', 'archived_at_column' => 'archived_at', 'archive_on_insert' => 'false', 'archive_on_update' => 'false', 'archive_on_delete' => 'true', ),
        );
    } // getBehaviors()
    /**
     * Method to invalidate the instance pool of all tables related to puzzle     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in related instance pools,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        TagAlertTableMap::clearInstancePool();
        NoteTableMap::clearInstancePool();
        PuzzleMemberTableMap::clearInstancePool();
        PuzzlePuzzleTableMap::clearInstancePool();
        NewsTableMap::clearInstancePool();
    }

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     *
     * @return string The primary key hash of the row
     */
    public static function getPrimaryKeyHashFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        // If the PK cannot be derived from the row, return NULL.
        if ($row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)] === null) {
            return null;
        }

        return null === $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)] || is_scalar($row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)]) || is_callable([$row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)], '__toString']) ? (string) $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)] : $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
    }

    /**
     * Retrieves the primary key from the DB resultset row
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, an array of the primary key columns will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     *
     * @return mixed The primary key of the row
     */
    public static function getPrimaryKeyFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        return (int) $row[
            $indexType == TableMap::TYPE_NUM
                ? 0 + $offset
                : self::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)
        ];
    }

    /**
     * The class that the tableMap will make instances of.
     *
     * If $withPrefix is true, the returned path
     * uses a dot-path notation which is translated into a path
     * relative to a location on the PHP include_path.
     * (e.g. path.to.MyClass -> 'path/to/MyClass.php')
     *
     * @param boolean $withPrefix Whether or not to return the path with the class name
     * @return string path.to.ClassName
     */
    public static function getOMClass($withPrefix = true)
    {
        return $withPrefix ? PuzzleTableMap::CLASS_DEFAULT : PuzzleTableMap::OM_CLASS;
    }

    /**
     * Populates an object of the default type or an object that inherit from the default.
     *
     * @param array  $row       row returned by DataFetcher->fetch().
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                 One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     * @return array           (Puzzle object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = PuzzleTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = PuzzleTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + PuzzleTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = PuzzleTableMap::OM_CLASS;
            /** @var Puzzle $obj */
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            PuzzleTableMap::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }

    /**
     * The returned array will contain objects of the default type or
     * objects that inherit from the default.
     *
     * @param DataFetcherInterface $dataFetcher
     * @return array
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function populateObjects(DataFetcherInterface $dataFetcher)
    {
        $results = array();

        // set the class once to avoid overhead in the loop
        $cls = static::getOMClass(false);
        // populate the object(s)
        while ($row = $dataFetcher->fetch()) {
            $key = PuzzleTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = PuzzleTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                /** @var Puzzle $obj */
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                PuzzleTableMap::addInstanceToPool($obj, $key);
            } // if key exists
        }

        return $results;
    }
    /**
     * Add all the columns needed to create a new object.
     *
     * Note: any columns that were marked with lazyLoad="true" in the
     * XML schema will not be added to the select list and only loaded
     * on demand.
     *
     * @param Criteria $criteria object containing the columns to add.
     * @param string   $alias    optional table alias
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria, $alias = null)
    {
        if (null === $alias) {
            $criteria->addSelectColumn(PuzzleTableMap::COL_ID);
            $criteria->addSelectColumn(PuzzleTableMap::COL_TITLE);
            $criteria->addSelectColumn(PuzzleTableMap::COL_URL);
            $criteria->addSelectColumn(PuzzleTableMap::COL_SPREADSHEET_ID);
            $criteria->addSelectColumn(PuzzleTableMap::COL_SOLUTION);
            $criteria->addSelectColumn(PuzzleTableMap::COL_STATUS);
            $criteria->addSelectColumn(PuzzleTableMap::COL_SLACK_CHANNEL);
            $criteria->addSelectColumn(PuzzleTableMap::COL_SLACK_CHANNEL_ID);
            $criteria->addSelectColumn(PuzzleTableMap::COL_WRANGLER_ID);
            $criteria->addSelectColumn(PuzzleTableMap::COL_SHEET_MOD_DATE);
            $criteria->addSelectColumn(PuzzleTableMap::COL_POST_COUNT);
            $criteria->addSelectColumn(PuzzleTableMap::COL_SOLVER_COUNT);
            $criteria->addSelectColumn(PuzzleTableMap::COL_CREATED_AT);
            $criteria->addSelectColumn(PuzzleTableMap::COL_UPDATED_AT);
        } else {
            $criteria->addSelectColumn($alias . '.id');
            $criteria->addSelectColumn($alias . '.title');
            $criteria->addSelectColumn($alias . '.url');
            $criteria->addSelectColumn($alias . '.spreadsheet_id');
            $criteria->addSelectColumn($alias . '.solution');
            $criteria->addSelectColumn($alias . '.status');
            $criteria->addSelectColumn($alias . '.slack_channel');
            $criteria->addSelectColumn($alias . '.slack_channel_id');
            $criteria->addSelectColumn($alias . '.wrangler_id');
            $criteria->addSelectColumn($alias . '.sheet_mod_date');
            $criteria->addSelectColumn($alias . '.post_count');
            $criteria->addSelectColumn($alias . '.solver_count');
            $criteria->addSelectColumn($alias . '.created_at');
            $criteria->addSelectColumn($alias . '.updated_at');
        }
    }

    /**
     * Returns the TableMap related to this object.
     * This method is not needed for general use but a specific application could have a need.
     * @return TableMap
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function getTableMap()
    {
        return Propel::getServiceContainer()->getDatabaseMap(PuzzleTableMap::DATABASE_NAME)->getTable(PuzzleTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
        $dbMap = Propel::getServiceContainer()->getDatabaseMap(PuzzleTableMap::DATABASE_NAME);
        if (!$dbMap->hasTable(PuzzleTableMap::TABLE_NAME)) {
            $dbMap->addTableObject(new PuzzleTableMap());
        }
    }

    /**
     * Performs a DELETE on the database, given a Puzzle or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or Puzzle object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param  ConnectionInterface $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
     public static function doDelete($values, ConnectionInterface $con = null)
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(PuzzleTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Puzzle) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(PuzzleTableMap::DATABASE_NAME);
            $criteria->add(PuzzleTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = PuzzleQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) {
            PuzzleTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) {
                PuzzleTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the puzzle table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return PuzzleQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a Puzzle or Criteria object.
     *
     * @param mixed               $criteria Criteria or Puzzle object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(PuzzleTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from Puzzle object
        }

        if ($criteria->containsKey(PuzzleTableMap::COL_ID) && $criteria->keyContainsValue(PuzzleTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.PuzzleTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = PuzzleQuery::create()->mergeWith($criteria);

        // use transaction because $criteria could contain info
        // for more than one table (I guess, conceivably)
        return $con->transaction(function () use ($con, $query) {
            return $query->doInsert($con);
        });
    }

} // PuzzleTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
PuzzleTableMap::buildTableMap();
