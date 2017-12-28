<?php

namespace Map;

use \Topic;
use \TopicQuery;
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
 * This class defines the structure of the 'topic' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class TopicTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = '.Map.TopicTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'palindrome';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'topic';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Topic';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Topic';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 8;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 8;

    /**
     * the column name for the id field
     */
    const COL_ID = 'topic.id';

    /**
     * the column name for the title field
     */
    const COL_TITLE = 'topic.title';

    /**
     * the column name for the slack_channel field
     */
    const COL_SLACK_CHANNEL = 'topic.slack_channel';

    /**
     * the column name for the slack_channel_id field
     */
    const COL_SLACK_CHANNEL_ID = 'topic.slack_channel_id';

    /**
     * the column name for the tree_left field
     */
    const COL_TREE_LEFT = 'topic.tree_left';

    /**
     * the column name for the tree_right field
     */
    const COL_TREE_RIGHT = 'topic.tree_right';

    /**
     * the column name for the tree_level field
     */
    const COL_TREE_LEVEL = 'topic.tree_level';

    /**
     * the column name for the tree_scope field
     */
    const COL_TREE_SCOPE = 'topic.tree_scope';

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
        self::TYPE_PHPNAME       => array('Id', 'Title', 'SlackChannel', 'SlackChannelId', 'TreeLeft', 'TreeRight', 'TreeLevel', 'TreeScope', ),
        self::TYPE_CAMELNAME     => array('id', 'title', 'slackChannel', 'slackChannelId', 'treeLeft', 'treeRight', 'treeLevel', 'treeScope', ),
        self::TYPE_COLNAME       => array(TopicTableMap::COL_ID, TopicTableMap::COL_TITLE, TopicTableMap::COL_SLACK_CHANNEL, TopicTableMap::COL_SLACK_CHANNEL_ID, TopicTableMap::COL_TREE_LEFT, TopicTableMap::COL_TREE_RIGHT, TopicTableMap::COL_TREE_LEVEL, TopicTableMap::COL_TREE_SCOPE, ),
        self::TYPE_FIELDNAME     => array('id', 'title', 'slack_channel', 'slack_channel_id', 'tree_left', 'tree_right', 'tree_level', 'tree_scope', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'Title' => 1, 'SlackChannel' => 2, 'SlackChannelId' => 3, 'TreeLeft' => 4, 'TreeRight' => 5, 'TreeLevel' => 6, 'TreeScope' => 7, ),
        self::TYPE_CAMELNAME     => array('id' => 0, 'title' => 1, 'slackChannel' => 2, 'slackChannelId' => 3, 'treeLeft' => 4, 'treeRight' => 5, 'treeLevel' => 6, 'treeScope' => 7, ),
        self::TYPE_COLNAME       => array(TopicTableMap::COL_ID => 0, TopicTableMap::COL_TITLE => 1, TopicTableMap::COL_SLACK_CHANNEL => 2, TopicTableMap::COL_SLACK_CHANNEL_ID => 3, TopicTableMap::COL_TREE_LEFT => 4, TopicTableMap::COL_TREE_RIGHT => 5, TopicTableMap::COL_TREE_LEVEL => 6, TopicTableMap::COL_TREE_SCOPE => 7, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'title' => 1, 'slack_channel' => 2, 'slack_channel_id' => 3, 'tree_left' => 4, 'tree_right' => 5, 'tree_level' => 6, 'tree_scope' => 7, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, )
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
        $this->setName('topic');
        $this->setPhpName('Topic');
        $this->setIdentifierQuoting(false);
        $this->setClassName('\\Topic');
        $this->setPackage('');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('title', 'Title', 'VARCHAR', true, 255, null);
        $this->getColumn('title')->setPrimaryString(true);
        $this->addColumn('slack_channel', 'SlackChannel', 'VARCHAR', false, 48, null);
        $this->addColumn('slack_channel_id', 'SlackChannelId', 'VARCHAR', false, 24, null);
        $this->addColumn('tree_left', 'TreeLeft', 'INTEGER', false, null, null);
        $this->addColumn('tree_right', 'TreeRight', 'INTEGER', false, null, null);
        $this->addColumn('tree_level', 'TreeLevel', 'INTEGER', false, null, null);
        $this->addColumn('tree_scope', 'TreeScope', 'INTEGER', false, null, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('TopicAlert', '\\TopicAlert', RelationMap::ONE_TO_MANY, array (
  0 =>
  array (
    0 => ':topic_id',
    1 => ':id',
  ),
), 'CASCADE', null, 'TopicAlerts', false);
        $this->addRelation('Puzzle', '\\Puzzle', RelationMap::MANY_TO_MANY, array(), 'CASCADE', null, 'Puzzles');
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
            'nested_set' => array('left_column' => 'tree_left', 'right_column' => 'tree_right', 'level_column' => 'tree_level', 'use_scope' => 'true', 'scope_column' => 'tree_scope', 'method_proxies' => 'false', ),
        );
    } // getBehaviors()
    /**
     * Method to invalidate the instance pool of all tables related to topic     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in related instance pools,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        TopicAlertTableMap::clearInstancePool();
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
        return $withPrefix ? TopicTableMap::CLASS_DEFAULT : TopicTableMap::OM_CLASS;
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
     * @return array           (Topic object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = TopicTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = TopicTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + TopicTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = TopicTableMap::OM_CLASS;
            /** @var Topic $obj */
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            TopicTableMap::addInstanceToPool($obj, $key);
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
            $key = TopicTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = TopicTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                /** @var Topic $obj */
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                TopicTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(TopicTableMap::COL_ID);
            $criteria->addSelectColumn(TopicTableMap::COL_TITLE);
            $criteria->addSelectColumn(TopicTableMap::COL_SLACK_CHANNEL);
            $criteria->addSelectColumn(TopicTableMap::COL_SLACK_CHANNEL_ID);
            $criteria->addSelectColumn(TopicTableMap::COL_TREE_LEFT);
            $criteria->addSelectColumn(TopicTableMap::COL_TREE_RIGHT);
            $criteria->addSelectColumn(TopicTableMap::COL_TREE_LEVEL);
            $criteria->addSelectColumn(TopicTableMap::COL_TREE_SCOPE);
        } else {
            $criteria->addSelectColumn($alias . '.id');
            $criteria->addSelectColumn($alias . '.title');
            $criteria->addSelectColumn($alias . '.slack_channel');
            $criteria->addSelectColumn($alias . '.slack_channel_id');
            $criteria->addSelectColumn($alias . '.tree_left');
            $criteria->addSelectColumn($alias . '.tree_right');
            $criteria->addSelectColumn($alias . '.tree_level');
            $criteria->addSelectColumn($alias . '.tree_scope');
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
        return Propel::getServiceContainer()->getDatabaseMap(TopicTableMap::DATABASE_NAME)->getTable(TopicTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
        $dbMap = Propel::getServiceContainer()->getDatabaseMap(TopicTableMap::DATABASE_NAME);
        if (!$dbMap->hasTable(TopicTableMap::TABLE_NAME)) {
            $dbMap->addTableObject(new TopicTableMap());
        }
    }

    /**
     * Performs a DELETE on the database, given a Topic or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or Topic object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(TopicTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Topic) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(TopicTableMap::DATABASE_NAME);
            $criteria->add(TopicTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = TopicQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) {
            TopicTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) {
                TopicTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the topic table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return TopicQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a Topic or Criteria object.
     *
     * @param mixed               $criteria Criteria or Topic object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(TopicTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from Topic object
        }

        if ($criteria->containsKey(TopicTableMap::COL_ID) && $criteria->keyContainsValue(TopicTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.TopicTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = TopicQuery::create()->mergeWith($criteria);

        // use transaction because $criteria could contain info
        // for more than one table (I guess, conceivably)
        return $con->transaction(function () use ($con, $query) {
            return $query->doInsert($con);
        });
    }

} // TopicTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
TopicTableMap::buildTableMap();
