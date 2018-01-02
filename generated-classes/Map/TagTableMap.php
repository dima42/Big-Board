<?php

namespace Map;

use \Tag;
use \TagQuery;
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
 * This class defines the structure of the 'tag' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class TagTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = '.Map.TagTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'palindrome';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'tag';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Tag';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Tag';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 10;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 10;

    /**
     * the column name for the id field
     */
    const COL_ID = 'tag.id';

    /**
     * the column name for the title field
     */
    const COL_TITLE = 'tag.title';

    /**
     * the column name for the alerted field
     */
    const COL_ALERTED = 'tag.alerted';

    /**
     * the column name for the slack_channel field
     */
    const COL_SLACK_CHANNEL = 'tag.slack_channel';

    /**
     * the column name for the slack_channel_id field
     */
    const COL_SLACK_CHANNEL_ID = 'tag.slack_channel_id';

    /**
     * the column name for the description field
     */
    const COL_DESCRIPTION = 'tag.description';

    /**
     * the column name for the tree_left field
     */
    const COL_TREE_LEFT = 'tag.tree_left';

    /**
     * the column name for the tree_right field
     */
    const COL_TREE_RIGHT = 'tag.tree_right';

    /**
     * the column name for the tree_level field
     */
    const COL_TREE_LEVEL = 'tag.tree_level';

    /**
     * the column name for the tree_scope field
     */
    const COL_TREE_SCOPE = 'tag.tree_scope';

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
        self::TYPE_PHPNAME       => array('Id', 'Title', 'Alerted', 'SlackChannel', 'SlackChannelId', 'Description', 'TreeLeft', 'TreeRight', 'TreeLevel', 'TreeScope', ),
        self::TYPE_CAMELNAME     => array('id', 'title', 'alerted', 'slackChannel', 'slackChannelId', 'description', 'treeLeft', 'treeRight', 'treeLevel', 'treeScope', ),
        self::TYPE_COLNAME       => array(TagTableMap::COL_ID, TagTableMap::COL_TITLE, TagTableMap::COL_ALERTED, TagTableMap::COL_SLACK_CHANNEL, TagTableMap::COL_SLACK_CHANNEL_ID, TagTableMap::COL_DESCRIPTION, TagTableMap::COL_TREE_LEFT, TagTableMap::COL_TREE_RIGHT, TagTableMap::COL_TREE_LEVEL, TagTableMap::COL_TREE_SCOPE, ),
        self::TYPE_FIELDNAME     => array('id', 'title', 'alerted', 'slack_channel', 'slack_channel_id', 'description', 'tree_left', 'tree_right', 'tree_level', 'tree_scope', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'Title' => 1, 'Alerted' => 2, 'SlackChannel' => 3, 'SlackChannelId' => 4, 'Description' => 5, 'TreeLeft' => 6, 'TreeRight' => 7, 'TreeLevel' => 8, 'TreeScope' => 9, ),
        self::TYPE_CAMELNAME     => array('id' => 0, 'title' => 1, 'alerted' => 2, 'slackChannel' => 3, 'slackChannelId' => 4, 'description' => 5, 'treeLeft' => 6, 'treeRight' => 7, 'treeLevel' => 8, 'treeScope' => 9, ),
        self::TYPE_COLNAME       => array(TagTableMap::COL_ID => 0, TagTableMap::COL_TITLE => 1, TagTableMap::COL_ALERTED => 2, TagTableMap::COL_SLACK_CHANNEL => 3, TagTableMap::COL_SLACK_CHANNEL_ID => 4, TagTableMap::COL_DESCRIPTION => 5, TagTableMap::COL_TREE_LEFT => 6, TagTableMap::COL_TREE_RIGHT => 7, TagTableMap::COL_TREE_LEVEL => 8, TagTableMap::COL_TREE_SCOPE => 9, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'title' => 1, 'alerted' => 2, 'slack_channel' => 3, 'slack_channel_id' => 4, 'description' => 5, 'tree_left' => 6, 'tree_right' => 7, 'tree_level' => 8, 'tree_scope' => 9, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, )
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
        $this->setName('tag');
        $this->setPhpName('Tag');
        $this->setIdentifierQuoting(false);
        $this->setClassName('\\Tag');
        $this->setPackage('');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('title', 'Title', 'VARCHAR', true, 255, null);
        $this->getColumn('title')->setPrimaryString(true);
        $this->addColumn('alerted', 'Alerted', 'BOOLEAN', true, 1, true);
        $this->addColumn('slack_channel', 'SlackChannel', 'VARCHAR', false, 48, null);
        $this->addColumn('slack_channel_id', 'SlackChannelId', 'VARCHAR', false, 24, null);
        $this->addColumn('description', 'Description', 'VARCHAR', false, 128, null);
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
        $this->addRelation('TagAlert', '\\TagAlert', RelationMap::ONE_TO_MANY, array (
  0 =>
  array (
    0 => ':tag_id',
    1 => ':id',
  ),
), 'CASCADE', null, 'TagAlerts', false);
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
     * Method to invalidate the instance pool of all tables related to tag     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in related instance pools,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        TagAlertTableMap::clearInstancePool();
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
        return $withPrefix ? TagTableMap::CLASS_DEFAULT : TagTableMap::OM_CLASS;
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
     * @return array           (Tag object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = TagTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = TagTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + TagTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = TagTableMap::OM_CLASS;
            /** @var Tag $obj */
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            TagTableMap::addInstanceToPool($obj, $key);
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
            $key = TagTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = TagTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                /** @var Tag $obj */
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                TagTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(TagTableMap::COL_ID);
            $criteria->addSelectColumn(TagTableMap::COL_TITLE);
            $criteria->addSelectColumn(TagTableMap::COL_ALERTED);
            $criteria->addSelectColumn(TagTableMap::COL_SLACK_CHANNEL);
            $criteria->addSelectColumn(TagTableMap::COL_SLACK_CHANNEL_ID);
            $criteria->addSelectColumn(TagTableMap::COL_DESCRIPTION);
            $criteria->addSelectColumn(TagTableMap::COL_TREE_LEFT);
            $criteria->addSelectColumn(TagTableMap::COL_TREE_RIGHT);
            $criteria->addSelectColumn(TagTableMap::COL_TREE_LEVEL);
            $criteria->addSelectColumn(TagTableMap::COL_TREE_SCOPE);
        } else {
            $criteria->addSelectColumn($alias . '.id');
            $criteria->addSelectColumn($alias . '.title');
            $criteria->addSelectColumn($alias . '.alerted');
            $criteria->addSelectColumn($alias . '.slack_channel');
            $criteria->addSelectColumn($alias . '.slack_channel_id');
            $criteria->addSelectColumn($alias . '.description');
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
        return Propel::getServiceContainer()->getDatabaseMap(TagTableMap::DATABASE_NAME)->getTable(TagTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
        $dbMap = Propel::getServiceContainer()->getDatabaseMap(TagTableMap::DATABASE_NAME);
        if (!$dbMap->hasTable(TagTableMap::TABLE_NAME)) {
            $dbMap->addTableObject(new TagTableMap());
        }
    }

    /**
     * Performs a DELETE on the database, given a Tag or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or Tag object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(TagTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Tag) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(TagTableMap::DATABASE_NAME);
            $criteria->add(TagTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = TagQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) {
            TagTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) {
                TagTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the tag table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return TagQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a Tag or Criteria object.
     *
     * @param mixed               $criteria Criteria or Tag object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(TagTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from Tag object
        }

        if ($criteria->containsKey(TagTableMap::COL_ID) && $criteria->keyContainsValue(TagTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.TagTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = TagQuery::create()->mergeWith($criteria);

        // use transaction because $criteria could contain info
        // for more than one table (I guess, conceivably)
        return $con->transaction(function () use ($con, $query) {
            return $query->doInsert($con);
        });
    }

} // TagTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
TagTableMap::buildTableMap();
