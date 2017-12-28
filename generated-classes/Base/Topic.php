<?php

namespace Base;

use \Puzzle as ChildPuzzle;
use \PuzzleQuery as ChildPuzzleQuery;
use \PuzzleTopic as ChildPuzzleTopic;
use \PuzzleTopicQuery as ChildPuzzleTopicQuery;
use \Topic as ChildTopic;
use \TopicQuery as ChildTopicQuery;
use \Exception;
use \PDO;
use Map\PuzzleTopicTableMap;
use Map\TopicTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\ActiveRecord\NestedSetRecursiveIterator;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\BadMethodCallException;
use Propel\Runtime\Exception\LogicException;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Parser\AbstractParser;

/**
 * Base class that represents a row from the 'topic' table.
 *
 *
 *
 * @package    propel.generator..Base
 */
abstract class Topic implements ActiveRecordInterface
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Map\\TopicTableMap';


    /**
     * attribute to determine if this object has previously been saved.
     * @var boolean
     */
    protected $new = true;

    /**
     * attribute to determine whether this object has been deleted.
     * @var boolean
     */
    protected $deleted = false;

    /**
     * The columns that have been modified in current object.
     * Tracking modified columns allows us to only update modified columns.
     * @var array
     */
    protected $modifiedColumns = array();

    /**
     * The (virtual) columns that are added at runtime
     * The formatters can add supplementary columns based on a resultset
     * @var array
     */
    protected $virtualColumns = array();

    /**
     * The value for the id field.
     *
     * @var        int
     */
    protected $id;

    /**
     * The value for the title field.
     *
     * @var        string
     */
    protected $title;

    /**
     * The value for the slack_channel field.
     *
     * @var        string
     */
    protected $slack_channel;

    /**
     * The value for the slack_channel_id field.
     *
     * @var        string
     */
    protected $slack_channel_id;

    /**
     * The value for the tree_left field.
     *
     * @var        int
     */
    protected $tree_left;

    /**
     * The value for the tree_right field.
     *
     * @var        int
     */
    protected $tree_right;

    /**
     * The value for the tree_level field.
     *
     * @var        int
     */
    protected $tree_level;

    /**
     * The value for the tree_scope field.
     *
     * @var        int
     */
    protected $tree_scope;

    /**
     * @var        ObjectCollection|ChildPuzzleTopic[] Collection to store aggregation of ChildPuzzleTopic objects.
     */
    protected $collPuzzleTopics;
    protected $collPuzzleTopicsPartial;

    /**
     * @var        ObjectCollection|ChildPuzzle[] Cross Collection to store aggregation of ChildPuzzle objects.
     */
    protected $collPuzzles;

    /**
     * @var bool
     */
    protected $collPuzzlesPartial;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var boolean
     */
    protected $alreadyInSave = false;

    // nested_set behavior

    /**
     * Queries to be executed in the save transaction
     * @var        array
     */
    protected $nestedSetQueries = array();

    /**
     * Internal cache for children nodes
     * @var        null|ObjectCollection
     */
    protected $collNestedSetChildren = null;

    /**
     * Internal cache for parent node
     * @var        null|ChildTopic
     */
    protected $aNestedSetParent = null;

    /**
     * Left column for the set
     */
    const LEFT_COL = 'topic.tree_left';

    /**
     * Right column for the set
     */
    const RIGHT_COL = 'topic.tree_right';

    /**
     * Level column for the set
     */
    const LEVEL_COL = 'topic.tree_level';

    /**
     * Scope column for the set
     */
    const SCOPE_COL = 'topic.tree_scope';

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildPuzzle[]
     */
    protected $puzzlesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildPuzzleTopic[]
     */
    protected $puzzleTopicsScheduledForDeletion = null;

    /**
     * Initializes internal state of Base\Topic object.
     */
    public function __construct()
    {
    }

    /**
     * Returns whether the object has been modified.
     *
     * @return boolean True if the object has been modified.
     */
    public function isModified()
    {
        return !!$this->modifiedColumns;
    }

    /**
     * Has specified column been modified?
     *
     * @param  string  $col column fully qualified name (TableMap::TYPE_COLNAME), e.g. Book::AUTHOR_ID
     * @return boolean True if $col has been modified.
     */
    public function isColumnModified($col)
    {
        return $this->modifiedColumns && isset($this->modifiedColumns[$col]);
    }

    /**
     * Get the columns that have been modified in this object.
     * @return array A unique list of the modified column names for this object.
     */
    public function getModifiedColumns()
    {
        return $this->modifiedColumns ? array_keys($this->modifiedColumns) : [];
    }

    /**
     * Returns whether the object has ever been saved.  This will
     * be false, if the object was retrieved from storage or was created
     * and then saved.
     *
     * @return boolean true, if the object has never been persisted.
     */
    public function isNew()
    {
        return $this->new;
    }

    /**
     * Setter for the isNew attribute.  This method will be called
     * by Propel-generated children and objects.
     *
     * @param boolean $b the state of the object.
     */
    public function setNew($b)
    {
        $this->new = (boolean) $b;
    }

    /**
     * Whether this object has been deleted.
     * @return boolean The deleted state of this object.
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * Specify whether this object has been deleted.
     * @param  boolean $b The deleted state of this object.
     * @return void
     */
    public function setDeleted($b)
    {
        $this->deleted = (boolean) $b;
    }

    /**
     * Sets the modified state for the object to be false.
     * @param  string $col If supplied, only the specified column is reset.
     * @return void
     */
    public function resetModified($col = null)
    {
        if (null !== $col) {
            if (isset($this->modifiedColumns[$col])) {
                unset($this->modifiedColumns[$col]);
            }
        } else {
            $this->modifiedColumns = array();
        }
    }

    /**
     * Compares this with another <code>Topic</code> instance.  If
     * <code>obj</code> is an instance of <code>Topic</code>, delegates to
     * <code>equals(Topic)</code>.  Otherwise, returns <code>false</code>.
     *
     * @param  mixed   $obj The object to compare to.
     * @return boolean Whether equal to the object specified.
     */
    public function equals($obj)
    {
        if (!$obj instanceof static) {
            return false;
        }

        if ($this === $obj) {
            return true;
        }

        if (null === $this->getPrimaryKey() || null === $obj->getPrimaryKey()) {
            return false;
        }

        return $this->getPrimaryKey() === $obj->getPrimaryKey();
    }

    /**
     * Get the associative array of the virtual columns in this object
     *
     * @return array
     */
    public function getVirtualColumns()
    {
        return $this->virtualColumns;
    }

    /**
     * Checks the existence of a virtual column in this object
     *
     * @param  string  $name The virtual column name
     * @return boolean
     */
    public function hasVirtualColumn($name)
    {
        return array_key_exists($name, $this->virtualColumns);
    }

    /**
     * Get the value of a virtual column in this object
     *
     * @param  string $name The virtual column name
     * @return mixed
     *
     * @throws PropelException
     */
    public function getVirtualColumn($name)
    {
        if (!$this->hasVirtualColumn($name)) {
            throw new PropelException(sprintf('Cannot get value of inexistent virtual column %s.', $name));
        }

        return $this->virtualColumns[$name];
    }

    /**
     * Set the value of a virtual column in this object
     *
     * @param string $name  The virtual column name
     * @param mixed  $value The value to give to the virtual column
     *
     * @return $this|Topic The current object, for fluid interface
     */
    public function setVirtualColumn($name, $value)
    {
        $this->virtualColumns[$name] = $value;

        return $this;
    }

    /**
     * Logs a message using Propel::log().
     *
     * @param  string  $msg
     * @param  int     $priority One of the Propel::LOG_* logging levels
     * @return boolean
     */
    protected function log($msg, $priority = Propel::LOG_INFO)
    {
        return Propel::log(get_class($this) . ': ' . $msg, $priority);
    }

    /**
     * Export the current object properties to a string, using a given parser format
     * <code>
     * $book = BookQuery::create()->findPk(9012);
     * echo $book->exportTo('JSON');
     *  => {"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * @param  mixed   $parser                 A AbstractParser instance, or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param  boolean $includeLazyLoadColumns (optional) Whether to include lazy load(ed) columns. Defaults to TRUE.
     * @return string  The exported data
     */
    public function exportTo($parser, $includeLazyLoadColumns = true)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        return $parser->fromArray($this->toArray(TableMap::TYPE_PHPNAME, $includeLazyLoadColumns, array(), true));
    }

    /**
     * Clean up internal collections prior to serializing
     * Avoids recursive loops that turn into segmentation faults when serializing
     */
    public function __sleep()
    {
        $this->clearAllReferences();

        $cls = new \ReflectionClass($this);
        $propertyNames = [];
        $serializableProperties = array_diff($cls->getProperties(), $cls->getProperties(\ReflectionProperty::IS_STATIC));

        foreach($serializableProperties as $property) {
            $propertyNames[] = $property->getName();
        }

        return $propertyNames;
    }

    /**
     * Get the [id] column value.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the [title] column value.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get the [slack_channel] column value.
     *
     * @return string
     */
    public function getSlackChannel()
    {
        return $this->slack_channel;
    }

    /**
     * Get the [slack_channel_id] column value.
     *
     * @return string
     */
    public function getSlackChannelId()
    {
        return $this->slack_channel_id;
    }

    /**
     * Get the [tree_left] column value.
     *
     * @return int
     */
    public function getTreeLeft()
    {
        return $this->tree_left;
    }

    /**
     * Get the [tree_right] column value.
     *
     * @return int
     */
    public function getTreeRight()
    {
        return $this->tree_right;
    }

    /**
     * Get the [tree_level] column value.
     *
     * @return int
     */
    public function getTreeLevel()
    {
        return $this->tree_level;
    }

    /**
     * Get the [tree_scope] column value.
     *
     * @return int
     */
    public function getTreeScope()
    {
        return $this->tree_scope;
    }

    /**
     * Set the value of [id] column.
     *
     * @param int $v new value
     * @return $this|\Topic The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[TopicTableMap::COL_ID] = true;
        }

        return $this;
    } // setId()

    /**
     * Set the value of [title] column.
     *
     * @param string $v new value
     * @return $this|\Topic The current object (for fluent API support)
     */
    public function setTitle($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->title !== $v) {
            $this->title = $v;
            $this->modifiedColumns[TopicTableMap::COL_TITLE] = true;
        }

        return $this;
    } // setTitle()

    /**
     * Set the value of [slack_channel] column.
     *
     * @param string $v new value
     * @return $this|\Topic The current object (for fluent API support)
     */
    public function setSlackChannel($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->slack_channel !== $v) {
            $this->slack_channel = $v;
            $this->modifiedColumns[TopicTableMap::COL_SLACK_CHANNEL] = true;
        }

        return $this;
    } // setSlackChannel()

    /**
     * Set the value of [slack_channel_id] column.
     *
     * @param string $v new value
     * @return $this|\Topic The current object (for fluent API support)
     */
    public function setSlackChannelId($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->slack_channel_id !== $v) {
            $this->slack_channel_id = $v;
            $this->modifiedColumns[TopicTableMap::COL_SLACK_CHANNEL_ID] = true;
        }

        return $this;
    } // setSlackChannelId()

    /**
     * Set the value of [tree_left] column.
     *
     * @param int $v new value
     * @return $this|\Topic The current object (for fluent API support)
     */
    public function setTreeLeft($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->tree_left !== $v) {
            $this->tree_left = $v;
            $this->modifiedColumns[TopicTableMap::COL_TREE_LEFT] = true;
        }

        return $this;
    } // setTreeLeft()

    /**
     * Set the value of [tree_right] column.
     *
     * @param int $v new value
     * @return $this|\Topic The current object (for fluent API support)
     */
    public function setTreeRight($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->tree_right !== $v) {
            $this->tree_right = $v;
            $this->modifiedColumns[TopicTableMap::COL_TREE_RIGHT] = true;
        }

        return $this;
    } // setTreeRight()

    /**
     * Set the value of [tree_level] column.
     *
     * @param int $v new value
     * @return $this|\Topic The current object (for fluent API support)
     */
    public function setTreeLevel($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->tree_level !== $v) {
            $this->tree_level = $v;
            $this->modifiedColumns[TopicTableMap::COL_TREE_LEVEL] = true;
        }

        return $this;
    } // setTreeLevel()

    /**
     * Set the value of [tree_scope] column.
     *
     * @param int $v new value
     * @return $this|\Topic The current object (for fluent API support)
     */
    public function setTreeScope($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->tree_scope !== $v) {
            $this->tree_scope = $v;
            $this->modifiedColumns[TopicTableMap::COL_TREE_SCOPE] = true;
        }

        return $this;
    } // setTreeScope()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
        // otherwise, everything was equal, so return TRUE
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array   $row       The row returned by DataFetcher->fetch().
     * @param int     $startcol  0-based offset column which indicates which restultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @param string  $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                  One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                            TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false, $indexType = TableMap::TYPE_NUM)
    {
        try {

            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : TopicTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : TopicTableMap::translateFieldName('Title', TableMap::TYPE_PHPNAME, $indexType)];
            $this->title = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : TopicTableMap::translateFieldName('SlackChannel', TableMap::TYPE_PHPNAME, $indexType)];
            $this->slack_channel = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : TopicTableMap::translateFieldName('SlackChannelId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->slack_channel_id = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : TopicTableMap::translateFieldName('TreeLeft', TableMap::TYPE_PHPNAME, $indexType)];
            $this->tree_left = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : TopicTableMap::translateFieldName('TreeRight', TableMap::TYPE_PHPNAME, $indexType)];
            $this->tree_right = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : TopicTableMap::translateFieldName('TreeLevel', TableMap::TYPE_PHPNAME, $indexType)];
            $this->tree_level = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 7 + $startcol : TopicTableMap::translateFieldName('TreeScope', TableMap::TYPE_PHPNAME, $indexType)];
            $this->tree_scope = (null !== $col) ? (int) $col : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 8; // 8 = TopicTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException(sprintf('Error populating %s object', '\\Topic'), 0, $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {
    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param      boolean $deep (optional) Whether to also de-associated any related objects.
     * @param      ConnectionInterface $con (optional) The ConnectionInterface connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(TopicTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildTopicQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collPuzzleTopics = null;

            $this->collPuzzles = null;
        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see Topic::setDeleted()
     * @see Topic::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(TopicTableMap::DATABASE_NAME);
        }

        $con->transaction(function () use ($con) {
            $deleteQuery = ChildTopicQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            // nested_set behavior
            if ($this->isRoot()) {
                throw new PropelException('Deletion of a root node is disabled for nested sets. Use ChildTopicQuery::deleteTree($scope) instead to delete an entire tree');
            }

            if ($this->isInTree()) {
                $this->deleteDescendants($con);
            }

            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                // nested_set behavior
                if ($this->isInTree()) {
                    // fill up the room that was used by the node
                    ChildTopicQuery::shiftRLValues(-2, $this->getRightValue() + 1, null, $this->getScopeValue(), $con);
                }

                $this->setDeleted(true);
            }
        });
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see doSave()
     */
    public function save(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($this->alreadyInSave) {
            return 0;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(TopicTableMap::DATABASE_NAME);
        }

        return $con->transaction(function () use ($con) {
            $ret = $this->preSave($con);
            $isInsert = $this->isNew();
            // nested_set behavior
            if ($this->isNew() && $this->isRoot()) {
                // check if no other root exist in, the tree
                $rootExists = ChildTopicQuery::create()
                    ->addUsingAlias(ChildTopic::LEFT_COL, 1, Criteria::EQUAL)
                    ->addUsingAlias(ChildTopic::SCOPE_COL, $this->getScopeValue(), Criteria::EQUAL)
                    ->exists($con);
                if ($rootExists) {
                        throw new PropelException(sprintf('A root node already exists in this tree with scope "%s".', $this->getScopeValue()));
                }
            }
            $this->processNestedSetQueries($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
            } else {
                $ret = $ret && $this->preUpdate($con);
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                TopicTableMap::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }

            return $affectedRows;
        });
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see save()
     */
    protected function doSave(ConnectionInterface $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                    $affectedRows += 1;
                } else {
                    $affectedRows += $this->doUpdate($con);
                }
                $this->resetModified();
            }

            if ($this->puzzlesScheduledForDeletion !== null) {
                if (!$this->puzzlesScheduledForDeletion->isEmpty()) {
                    $pks = array();
                    foreach ($this->puzzlesScheduledForDeletion as $entry) {
                        $entryPk = [];

                        $entryPk[1] = $this->getId();
                        $entryPk[0] = $entry->getId();
                        $pks[] = $entryPk;
                    }

                    \PuzzleTopicQuery::create()
                        ->filterByPrimaryKeys($pks)
                        ->delete($con);

                    $this->puzzlesScheduledForDeletion = null;
                }

            }

            if ($this->collPuzzles) {
                foreach ($this->collPuzzles as $puzzle) {
                    if (!$puzzle->isDeleted() && ($puzzle->isNew() || $puzzle->isModified())) {
                        $puzzle->save($con);
                    }
                }
            }


            if ($this->puzzleTopicsScheduledForDeletion !== null) {
                if (!$this->puzzleTopicsScheduledForDeletion->isEmpty()) {
                    \PuzzleTopicQuery::create()
                        ->filterByPrimaryKeys($this->puzzleTopicsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->puzzleTopicsScheduledForDeletion = null;
                }
            }

            if ($this->collPuzzleTopics !== null) {
                foreach ($this->collPuzzleTopics as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @throws PropelException
     * @see doSave()
     */
    protected function doInsert(ConnectionInterface $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[TopicTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . TopicTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(TopicTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'id';
        }
        if ($this->isColumnModified(TopicTableMap::COL_TITLE)) {
            $modifiedColumns[':p' . $index++]  = 'title';
        }
        if ($this->isColumnModified(TopicTableMap::COL_SLACK_CHANNEL)) {
            $modifiedColumns[':p' . $index++]  = 'slack_channel';
        }
        if ($this->isColumnModified(TopicTableMap::COL_SLACK_CHANNEL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'slack_channel_id';
        }
        if ($this->isColumnModified(TopicTableMap::COL_TREE_LEFT)) {
            $modifiedColumns[':p' . $index++]  = 'tree_left';
        }
        if ($this->isColumnModified(TopicTableMap::COL_TREE_RIGHT)) {
            $modifiedColumns[':p' . $index++]  = 'tree_right';
        }
        if ($this->isColumnModified(TopicTableMap::COL_TREE_LEVEL)) {
            $modifiedColumns[':p' . $index++]  = 'tree_level';
        }
        if ($this->isColumnModified(TopicTableMap::COL_TREE_SCOPE)) {
            $modifiedColumns[':p' . $index++]  = 'tree_scope';
        }

        $sql = sprintf(
            'INSERT INTO topic (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case 'id':
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case 'title':
                        $stmt->bindValue($identifier, $this->title, PDO::PARAM_STR);
                        break;
                    case 'slack_channel':
                        $stmt->bindValue($identifier, $this->slack_channel, PDO::PARAM_STR);
                        break;
                    case 'slack_channel_id':
                        $stmt->bindValue($identifier, $this->slack_channel_id, PDO::PARAM_STR);
                        break;
                    case 'tree_left':
                        $stmt->bindValue($identifier, $this->tree_left, PDO::PARAM_INT);
                        break;
                    case 'tree_right':
                        $stmt->bindValue($identifier, $this->tree_right, PDO::PARAM_INT);
                        break;
                    case 'tree_level':
                        $stmt->bindValue($identifier, $this->tree_level, PDO::PARAM_INT);
                        break;
                    case 'tree_scope':
                        $stmt->bindValue($identifier, $this->tree_scope, PDO::PARAM_INT);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), 0, $e);
        }

        try {
            $pk = $con->lastInsertId();
        } catch (Exception $e) {
            throw new PropelException('Unable to get autoincrement id.', 0, $e);
        }
        $this->setId($pk);

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @return Integer Number of updated rows
     * @see doSave()
     */
    protected function doUpdate(ConnectionInterface $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();

        return $selectCriteria->doUpdate($valuesCriteria, $con);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param      string $name name
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                     TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                     Defaults to TableMap::TYPE_PHPNAME.
     * @return mixed Value of field.
     */
    public function getByName($name, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = TopicTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getId();
                break;
            case 1:
                return $this->getTitle();
                break;
            case 2:
                return $this->getSlackChannel();
                break;
            case 3:
                return $this->getSlackChannelId();
                break;
            case 4:
                return $this->getTreeLeft();
                break;
            case 5:
                return $this->getTreeRight();
                break;
            case 6:
                return $this->getTreeLevel();
                break;
            case 7:
                return $this->getTreeScope();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param     string  $keyType (optional) One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     *                    TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                    Defaults to TableMap::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to TRUE.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = TableMap::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {

        if (isset($alreadyDumpedObjects['Topic'][$this->hashCode()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Topic'][$this->hashCode()] = true;
        $keys = TopicTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getTitle(),
            $keys[2] => $this->getSlackChannel(),
            $keys[3] => $this->getSlackChannelId(),
            $keys[4] => $this->getTreeLeft(),
            $keys[5] => $this->getTreeRight(),
            $keys[6] => $this->getTreeLevel(),
            $keys[7] => $this->getTreeScope(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->collPuzzleTopics) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'puzzleTopics';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'puzzleTopics';
                        break;
                    default:
                        $key = 'PuzzleTopics';
                }

                $result[$key] = $this->collPuzzleTopics->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param  string $name
     * @param  mixed  $value field value
     * @param  string $type The type of fieldname the $name is of:
     *                one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                Defaults to TableMap::TYPE_PHPNAME.
     * @return $this|\Topic
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = TopicTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param  int $pos position in xml schema
     * @param  mixed $value field value
     * @return $this|\Topic
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setTitle($value);
                break;
            case 2:
                $this->setSlackChannel($value);
                break;
            case 3:
                $this->setSlackChannelId($value);
                break;
            case 4:
                $this->setTreeLeft($value);
                break;
            case 5:
                $this->setTreeRight($value);
                break;
            case 6:
                $this->setTreeLevel($value);
                break;
            case 7:
                $this->setTreeScope($value);
                break;
        } // switch()

        return $this;
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     * TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     * The default key type is the column's TableMap::TYPE_PHPNAME.
     *
     * @param      array  $arr     An array to populate the object from.
     * @param      string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = TableMap::TYPE_PHPNAME)
    {
        $keys = TopicTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setId($arr[$keys[0]]);
        }
        if (array_key_exists($keys[1], $arr)) {
            $this->setTitle($arr[$keys[1]]);
        }
        if (array_key_exists($keys[2], $arr)) {
            $this->setSlackChannel($arr[$keys[2]]);
        }
        if (array_key_exists($keys[3], $arr)) {
            $this->setSlackChannelId($arr[$keys[3]]);
        }
        if (array_key_exists($keys[4], $arr)) {
            $this->setTreeLeft($arr[$keys[4]]);
        }
        if (array_key_exists($keys[5], $arr)) {
            $this->setTreeRight($arr[$keys[5]]);
        }
        if (array_key_exists($keys[6], $arr)) {
            $this->setTreeLevel($arr[$keys[6]]);
        }
        if (array_key_exists($keys[7], $arr)) {
            $this->setTreeScope($arr[$keys[7]]);
        }
    }

     /**
     * Populate the current object from a string, using a given parser format
     * <code>
     * $book = new Book();
     * $book->importFrom('JSON', '{"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     * TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     * The default key type is the column's TableMap::TYPE_PHPNAME.
     *
     * @param mixed $parser A AbstractParser instance,
     *                       or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param string $data The source data to import from
     * @param string $keyType The type of keys the array uses.
     *
     * @return $this|\Topic The current object, for fluid interface
     */
    public function importFrom($parser, $data, $keyType = TableMap::TYPE_PHPNAME)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        $this->fromArray($parser->toArray($data), $keyType);

        return $this;
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(TopicTableMap::DATABASE_NAME);

        if ($this->isColumnModified(TopicTableMap::COL_ID)) {
            $criteria->add(TopicTableMap::COL_ID, $this->id);
        }
        if ($this->isColumnModified(TopicTableMap::COL_TITLE)) {
            $criteria->add(TopicTableMap::COL_TITLE, $this->title);
        }
        if ($this->isColumnModified(TopicTableMap::COL_SLACK_CHANNEL)) {
            $criteria->add(TopicTableMap::COL_SLACK_CHANNEL, $this->slack_channel);
        }
        if ($this->isColumnModified(TopicTableMap::COL_SLACK_CHANNEL_ID)) {
            $criteria->add(TopicTableMap::COL_SLACK_CHANNEL_ID, $this->slack_channel_id);
        }
        if ($this->isColumnModified(TopicTableMap::COL_TREE_LEFT)) {
            $criteria->add(TopicTableMap::COL_TREE_LEFT, $this->tree_left);
        }
        if ($this->isColumnModified(TopicTableMap::COL_TREE_RIGHT)) {
            $criteria->add(TopicTableMap::COL_TREE_RIGHT, $this->tree_right);
        }
        if ($this->isColumnModified(TopicTableMap::COL_TREE_LEVEL)) {
            $criteria->add(TopicTableMap::COL_TREE_LEVEL, $this->tree_level);
        }
        if ($this->isColumnModified(TopicTableMap::COL_TREE_SCOPE)) {
            $criteria->add(TopicTableMap::COL_TREE_SCOPE, $this->tree_scope);
        }

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @throws LogicException if no primary key is defined
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = ChildTopicQuery::create();
        $criteria->add(TopicTableMap::COL_ID, $this->id);

        return $criteria;
    }

    /**
     * If the primary key is not null, return the hashcode of the
     * primary key. Otherwise, return the hash code of the object.
     *
     * @return int Hashcode
     */
    public function hashCode()
    {
        $validPk = null !== $this->getId();

        $validPrimaryKeyFKs = 0;
        $primaryKeyFKs = [];

        if ($validPk) {
            return crc32(json_encode($this->getPrimaryKey(), JSON_UNESCAPED_UNICODE));
        } elseif ($validPrimaryKeyFKs) {
            return crc32(json_encode($primaryKeyFKs, JSON_UNESCAPED_UNICODE));
        }

        return spl_object_hash($this);
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param       int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {
        return null === $this->getId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of \Topic (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setTitle($this->getTitle());
        $copyObj->setSlackChannel($this->getSlackChannel());
        $copyObj->setSlackChannelId($this->getSlackChannelId());
        $copyObj->setTreeLeft($this->getTreeLeft());
        $copyObj->setTreeRight($this->getTreeRight());
        $copyObj->setTreeLevel($this->getTreeLevel());
        $copyObj->setTreeScope($this->getTreeScope());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getPuzzleTopics() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addPuzzleTopic($relObj->copy($deepCopy));
                }
            }

        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param  boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return \Topic Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }


    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param      string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('PuzzleTopic' == $relationName) {
            $this->initPuzzleTopics();
            return;
        }
    }

    /**
     * Clears out the collPuzzleTopics collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addPuzzleTopics()
     */
    public function clearPuzzleTopics()
    {
        $this->collPuzzleTopics = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collPuzzleTopics collection loaded partially.
     */
    public function resetPartialPuzzleTopics($v = true)
    {
        $this->collPuzzleTopicsPartial = $v;
    }

    /**
     * Initializes the collPuzzleTopics collection.
     *
     * By default this just sets the collPuzzleTopics collection to an empty array (like clearcollPuzzleTopics());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initPuzzleTopics($overrideExisting = true)
    {
        if (null !== $this->collPuzzleTopics && !$overrideExisting) {
            return;
        }

        $collectionClassName = PuzzleTopicTableMap::getTableMap()->getCollectionClassName();

        $this->collPuzzleTopics = new $collectionClassName;
        $this->collPuzzleTopics->setModel('\PuzzleTopic');
    }

    /**
     * Gets an array of ChildPuzzleTopic objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildTopic is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildPuzzleTopic[] List of ChildPuzzleTopic objects
     * @throws PropelException
     */
    public function getPuzzleTopics(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collPuzzleTopicsPartial && !$this->isNew();
        if (null === $this->collPuzzleTopics || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collPuzzleTopics) {
                // return empty collection
                $this->initPuzzleTopics();
            } else {
                $collPuzzleTopics = ChildPuzzleTopicQuery::create(null, $criteria)
                    ->filterByTopic($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collPuzzleTopicsPartial && count($collPuzzleTopics)) {
                        $this->initPuzzleTopics(false);

                        foreach ($collPuzzleTopics as $obj) {
                            if (false == $this->collPuzzleTopics->contains($obj)) {
                                $this->collPuzzleTopics->append($obj);
                            }
                        }

                        $this->collPuzzleTopicsPartial = true;
                    }

                    return $collPuzzleTopics;
                }

                if ($partial && $this->collPuzzleTopics) {
                    foreach ($this->collPuzzleTopics as $obj) {
                        if ($obj->isNew()) {
                            $collPuzzleTopics[] = $obj;
                        }
                    }
                }

                $this->collPuzzleTopics = $collPuzzleTopics;
                $this->collPuzzleTopicsPartial = false;
            }
        }

        return $this->collPuzzleTopics;
    }

    /**
     * Sets a collection of ChildPuzzleTopic objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $puzzleTopics A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildTopic The current object (for fluent API support)
     */
    public function setPuzzleTopics(Collection $puzzleTopics, ConnectionInterface $con = null)
    {
        /** @var ChildPuzzleTopic[] $puzzleTopicsToDelete */
        $puzzleTopicsToDelete = $this->getPuzzleTopics(new Criteria(), $con)->diff($puzzleTopics);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->puzzleTopicsScheduledForDeletion = clone $puzzleTopicsToDelete;

        foreach ($puzzleTopicsToDelete as $puzzleTopicRemoved) {
            $puzzleTopicRemoved->setTopic(null);
        }

        $this->collPuzzleTopics = null;
        foreach ($puzzleTopics as $puzzleTopic) {
            $this->addPuzzleTopic($puzzleTopic);
        }

        $this->collPuzzleTopics = $puzzleTopics;
        $this->collPuzzleTopicsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related PuzzleTopic objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related PuzzleTopic objects.
     * @throws PropelException
     */
    public function countPuzzleTopics(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collPuzzleTopicsPartial && !$this->isNew();
        if (null === $this->collPuzzleTopics || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collPuzzleTopics) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getPuzzleTopics());
            }

            $query = ChildPuzzleTopicQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByTopic($this)
                ->count($con);
        }

        return count($this->collPuzzleTopics);
    }

    /**
     * Method called to associate a ChildPuzzleTopic object to this object
     * through the ChildPuzzleTopic foreign key attribute.
     *
     * @param  ChildPuzzleTopic $l ChildPuzzleTopic
     * @return $this|\Topic The current object (for fluent API support)
     */
    public function addPuzzleTopic(ChildPuzzleTopic $l)
    {
        if ($this->collPuzzleTopics === null) {
            $this->initPuzzleTopics();
            $this->collPuzzleTopicsPartial = true;
        }

        if (!$this->collPuzzleTopics->contains($l)) {
            $this->doAddPuzzleTopic($l);

            if ($this->puzzleTopicsScheduledForDeletion and $this->puzzleTopicsScheduledForDeletion->contains($l)) {
                $this->puzzleTopicsScheduledForDeletion->remove($this->puzzleTopicsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildPuzzleTopic $puzzleTopic The ChildPuzzleTopic object to add.
     */
    protected function doAddPuzzleTopic(ChildPuzzleTopic $puzzleTopic)
    {
        $this->collPuzzleTopics[]= $puzzleTopic;
        $puzzleTopic->setTopic($this);
    }

    /**
     * @param  ChildPuzzleTopic $puzzleTopic The ChildPuzzleTopic object to remove.
     * @return $this|ChildTopic The current object (for fluent API support)
     */
    public function removePuzzleTopic(ChildPuzzleTopic $puzzleTopic)
    {
        if ($this->getPuzzleTopics()->contains($puzzleTopic)) {
            $pos = $this->collPuzzleTopics->search($puzzleTopic);
            $this->collPuzzleTopics->remove($pos);
            if (null === $this->puzzleTopicsScheduledForDeletion) {
                $this->puzzleTopicsScheduledForDeletion = clone $this->collPuzzleTopics;
                $this->puzzleTopicsScheduledForDeletion->clear();
            }
            $this->puzzleTopicsScheduledForDeletion[]= clone $puzzleTopic;
            $puzzleTopic->setTopic(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Topic is new, it will return
     * an empty collection; or if this Topic has previously
     * been saved, it will retrieve related PuzzleTopics from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Topic.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildPuzzleTopic[] List of ChildPuzzleTopic objects
     */
    public function getPuzzleTopicsJoinPuzzle(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildPuzzleTopicQuery::create(null, $criteria);
        $query->joinWith('Puzzle', $joinBehavior);

        return $this->getPuzzleTopics($query, $con);
    }

    /**
     * Clears out the collPuzzles collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addPuzzles()
     */
    public function clearPuzzles()
    {
        $this->collPuzzles = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Initializes the collPuzzles crossRef collection.
     *
     * By default this just sets the collPuzzles collection to an empty collection (like clearPuzzles());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @return void
     */
    public function initPuzzles()
    {
        $collectionClassName = PuzzleTopicTableMap::getTableMap()->getCollectionClassName();

        $this->collPuzzles = new $collectionClassName;
        $this->collPuzzlesPartial = true;
        $this->collPuzzles->setModel('\Puzzle');
    }

    /**
     * Checks if the collPuzzles collection is loaded.
     *
     * @return bool
     */
    public function isPuzzlesLoaded()
    {
        return null !== $this->collPuzzles;
    }

    /**
     * Gets a collection of ChildPuzzle objects related by a many-to-many relationship
     * to the current object by way of the puzzleTopic cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildTopic is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria Optional query object to filter the query
     * @param      ConnectionInterface $con Optional connection object
     *
     * @return ObjectCollection|ChildPuzzle[] List of ChildPuzzle objects
     */
    public function getPuzzles(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collPuzzlesPartial && !$this->isNew();
        if (null === $this->collPuzzles || null !== $criteria || $partial) {
            if ($this->isNew()) {
                // return empty collection
                if (null === $this->collPuzzles) {
                    $this->initPuzzles();
                }
            } else {

                $query = ChildPuzzleQuery::create(null, $criteria)
                    ->filterByTopic($this);
                $collPuzzles = $query->find($con);
                if (null !== $criteria) {
                    return $collPuzzles;
                }

                if ($partial && $this->collPuzzles) {
                    //make sure that already added objects gets added to the list of the database.
                    foreach ($this->collPuzzles as $obj) {
                        if (!$collPuzzles->contains($obj)) {
                            $collPuzzles[] = $obj;
                        }
                    }
                }

                $this->collPuzzles = $collPuzzles;
                $this->collPuzzlesPartial = false;
            }
        }

        return $this->collPuzzles;
    }

    /**
     * Sets a collection of Puzzle objects related by a many-to-many relationship
     * to the current object by way of the puzzleTopic cross-reference table.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param  Collection $puzzles A Propel collection.
     * @param  ConnectionInterface $con Optional connection object
     * @return $this|ChildTopic The current object (for fluent API support)
     */
    public function setPuzzles(Collection $puzzles, ConnectionInterface $con = null)
    {
        $this->clearPuzzles();
        $currentPuzzles = $this->getPuzzles();

        $puzzlesScheduledForDeletion = $currentPuzzles->diff($puzzles);

        foreach ($puzzlesScheduledForDeletion as $toDelete) {
            $this->removePuzzle($toDelete);
        }

        foreach ($puzzles as $puzzle) {
            if (!$currentPuzzles->contains($puzzle)) {
                $this->doAddPuzzle($puzzle);
            }
        }

        $this->collPuzzlesPartial = false;
        $this->collPuzzles = $puzzles;

        return $this;
    }

    /**
     * Gets the number of Puzzle objects related by a many-to-many relationship
     * to the current object by way of the puzzleTopic cross-reference table.
     *
     * @param      Criteria $criteria Optional query object to filter the query
     * @param      boolean $distinct Set to true to force count distinct
     * @param      ConnectionInterface $con Optional connection object
     *
     * @return int the number of related Puzzle objects
     */
    public function countPuzzles(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collPuzzlesPartial && !$this->isNew();
        if (null === $this->collPuzzles || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collPuzzles) {
                return 0;
            } else {

                if ($partial && !$criteria) {
                    return count($this->getPuzzles());
                }

                $query = ChildPuzzleQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByTopic($this)
                    ->count($con);
            }
        } else {
            return count($this->collPuzzles);
        }
    }

    /**
     * Associate a ChildPuzzle to this object
     * through the puzzleTopic cross reference table.
     *
     * @param ChildPuzzle $puzzle
     * @return ChildTopic The current object (for fluent API support)
     */
    public function addPuzzle(ChildPuzzle $puzzle)
    {
        if ($this->collPuzzles === null) {
            $this->initPuzzles();
        }

        if (!$this->getPuzzles()->contains($puzzle)) {
            // only add it if the **same** object is not already associated
            $this->collPuzzles->push($puzzle);
            $this->doAddPuzzle($puzzle);
        }

        return $this;
    }

    /**
     *
     * @param ChildPuzzle $puzzle
     */
    protected function doAddPuzzle(ChildPuzzle $puzzle)
    {
        $puzzleTopic = new ChildPuzzleTopic();

        $puzzleTopic->setPuzzle($puzzle);

        $puzzleTopic->setTopic($this);

        $this->addPuzzleTopic($puzzleTopic);

        // set the back reference to this object directly as using provided method either results
        // in endless loop or in multiple relations
        if (!$puzzle->isTopicsLoaded()) {
            $puzzle->initTopics();
            $puzzle->getTopics()->push($this);
        } elseif (!$puzzle->getTopics()->contains($this)) {
            $puzzle->getTopics()->push($this);
        }

    }

    /**
     * Remove puzzle of this object
     * through the puzzleTopic cross reference table.
     *
     * @param ChildPuzzle $puzzle
     * @return ChildTopic The current object (for fluent API support)
     */
    public function removePuzzle(ChildPuzzle $puzzle)
    {
        if ($this->getPuzzles()->contains($puzzle)) {
            $puzzleTopic = new ChildPuzzleTopic();
            $puzzleTopic->setPuzzle($puzzle);
            if ($puzzle->isTopicsLoaded()) {
                //remove the back reference if available
                $puzzle->getTopics()->removeObject($this);
            }

            $puzzleTopic->setTopic($this);
            $this->removePuzzleTopic(clone $puzzleTopic);
            $puzzleTopic->clear();

            $this->collPuzzles->remove($this->collPuzzles->search($puzzle));

            if (null === $this->puzzlesScheduledForDeletion) {
                $this->puzzlesScheduledForDeletion = clone $this->collPuzzles;
                $this->puzzlesScheduledForDeletion->clear();
            }

            $this->puzzlesScheduledForDeletion->push($puzzle);
        }


        return $this;
    }

    /**
     * Clears the current object, sets all attributes to their default values and removes
     * outgoing references as well as back-references (from other objects to this one. Results probably in a database
     * change of those foreign objects when you call `save` there).
     */
    public function clear()
    {
        $this->id = null;
        $this->title = null;
        $this->slack_channel = null;
        $this->slack_channel_id = null;
        $this->tree_left = null;
        $this->tree_right = null;
        $this->tree_level = null;
        $this->tree_scope = null;
        $this->alreadyInSave = false;
        $this->clearAllReferences();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references and back-references to other model objects or collections of model objects.
     *
     * This method is used to reset all php object references (not the actual reference in the database).
     * Necessary for object serialisation.
     *
     * @param      boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep) {
            if ($this->collPuzzleTopics) {
                foreach ($this->collPuzzleTopics as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collPuzzles) {
                foreach ($this->collPuzzles as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        // nested_set behavior
        $this->collNestedSetChildren = null;
        $this->aNestedSetParent = null;
        $this->collPuzzleTopics = null;
        $this->collPuzzles = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string The value of the 'title' column
     */
    public function __toString()
    {
        return (string) $this->getTitle();
    }

    // nested_set behavior

    /**
     * Execute queries that were saved to be run inside the save transaction
     *
     * @param  ConnectionInterface $con Connection to use.
     */
    protected function processNestedSetQueries(ConnectionInterface $con)
    {
        foreach ($this->nestedSetQueries as $query) {
            $query['arguments'][] = $con;
            call_user_func_array($query['callable'], $query['arguments']);
        }
        $this->nestedSetQueries = array();
    }

    /**
     * Proxy getter method for the left value of the nested set model.
     * It provides a generic way to get the value, whatever the actual column name is.
     *
     * @return     int The nested set left value
     */
    public function getLeftValue()
    {
        return $this->tree_left;
    }

    /**
     * Proxy getter method for the right value of the nested set model.
     * It provides a generic way to get the value, whatever the actual column name is.
     *
     * @return     int The nested set right value
     */
    public function getRightValue()
    {
        return $this->tree_right;
    }

    /**
     * Proxy getter method for the level value of the nested set model.
     * It provides a generic way to get the value, whatever the actual column name is.
     *
     * @return     int The nested set level value
     */
    public function getLevel()
    {
        return $this->tree_level;
    }

    /**
     * Proxy getter method for the scope value of the nested set model.
     * It provides a generic way to get the value, whatever the actual column name is.
     *
     * @return     int The nested set scope value
     */
    public function getScopeValue()
    {
        return $this->tree_scope;
    }

    /**
     * Proxy setter method for the left value of the nested set model.
     * It provides a generic way to set the value, whatever the actual column name is.
     *
     * @param  int $v The nested set left value
     * @return $this|ChildTopic The current object (for fluent API support)
     */
    public function setLeftValue($v)
    {
        return $this->setTreeLeft($v);
    }

    /**
     * Proxy setter method for the right value of the nested set model.
     * It provides a generic way to set the value, whatever the actual column name is.
     *
     * @param      int $v The nested set right value
     * @return     $this|ChildTopic The current object (for fluent API support)
     */
    public function setRightValue($v)
    {
        return $this->setTreeRight($v);
    }

    /**
     * Proxy setter method for the level value of the nested set model.
     * It provides a generic way to set the value, whatever the actual column name is.
     *
     * @param      int $v The nested set level value
     * @return     $this|ChildTopic The current object (for fluent API support)
     */
    public function setLevel($v)
    {
        return $this->setTreeLevel($v);
    }

    /**
     * Proxy setter method for the scope value of the nested set model.
     * It provides a generic way to set the value, whatever the actual column name is.
     *
     * @param      int $v The nested set scope value
     * @return     $this|ChildTopic The current object (for fluent API support)
     */
    public function setScopeValue($v)
    {
        return $this->setTreeScope($v);
    }

    /**
     * Creates the supplied node as the root node.
     *
     * @return     $this|ChildTopic The current object (for fluent API support)
     * @throws     PropelException
     */
    public function makeRoot()
    {
        if ($this->getLeftValue() || $this->getRightValue()) {
            throw new PropelException('Cannot turn an existing node into a root node.');
        }

        $this->setLeftValue(1);
        $this->setRightValue(2);
        $this->setLevel(0);

        return $this;
    }

    /**
     * Tests if object is a node, i.e. if it is inserted in the tree
     *
     * @return     bool
     */
    public function isInTree()
    {
        return $this->getLeftValue() > 0 && $this->getRightValue() > $this->getLeftValue();
    }

    /**
     * Tests if node is a root
     *
     * @return     bool
     */
    public function isRoot()
    {
        return $this->isInTree() && $this->getLeftValue() == 1;
    }

    /**
     * Tests if node is a leaf
     *
     * @return     bool
     */
    public function isLeaf()
    {
        return $this->isInTree() &&  ($this->getRightValue() - $this->getLeftValue()) == 1;
    }

    /**
     * Tests if node is a descendant of another node
     *
     * @param      ChildTopic $parent Propel node object
     * @return     bool
     */
    public function isDescendantOf(ChildTopic $parent)
    {
        if ($this->getScopeValue() !== $parent->getScopeValue()) {
            return false; //since the `this` and $parent are in different scopes, there's no way that `this` is be a descendant of $parent.
        }

        return $this->isInTree() && $this->getLeftValue() > $parent->getLeftValue() && $this->getRightValue() < $parent->getRightValue();
    }

    /**
     * Tests if node is a ancestor of another node
     *
     * @param      ChildTopic $child Propel node object
     * @return     bool
     */
    public function isAncestorOf(ChildTopic $child)
    {
        return $child->isDescendantOf($this);
    }

    /**
     * Tests if object has an ancestor
     *
     * @return boolean
     */
    public function hasParent()
    {
        return $this->getLevel() > 0;
    }

    /**
     * Sets the cache for parent node of the current object.
     * Warning: this does not move the current object in the tree.
     * Use moveTofirstChildOf() or moveToLastChildOf() for that purpose
     *
     * @param      ChildTopic $parent
     * @return     $this|ChildTopic The current object, for fluid interface
     */
    public function setParent(ChildTopic $parent = null)
    {
        $this->aNestedSetParent = $parent;

        return $this;
    }

    /**
     * Gets parent node for the current object if it exists
     * The result is cached so further calls to the same method don't issue any queries
     *
     * @param  ConnectionInterface $con Connection to use.
     * @return ChildTopic|null Propel object if exists else null
     */
    public function getParent(ConnectionInterface $con = null)
    {
        if (null === $this->aNestedSetParent && $this->hasParent()) {
            $this->aNestedSetParent = ChildTopicQuery::create()
                ->ancestorsOf($this)
                ->orderByLevel(true)
                ->findOne($con);
        }

        return $this->aNestedSetParent;
    }

    /**
     * Determines if the node has previous sibling
     *
     * @param      ConnectionInterface $con Connection to use.
     * @return     bool
     */
    public function hasPrevSibling(ConnectionInterface $con = null)
    {
        if (!ChildTopicQuery::isValid($this)) {
            return false;
        }

        return ChildTopicQuery::create()
            ->filterByTreeRight($this->getLeftValue() - 1)
            ->inTree($this->getScopeValue())
            ->exists($con);
    }

    /**
     * Gets previous sibling for the given node if it exists
     *
     * @param      ConnectionInterface $con Connection to use.
     * @return     ChildTopic|null         Propel object if exists else null
     */
    public function getPrevSibling(ConnectionInterface $con = null)
    {
        return ChildTopicQuery::create()
            ->filterByTreeRight($this->getLeftValue() - 1)
            ->inTree($this->getScopeValue())
            ->findOne($con);
    }

    /**
     * Determines if the node has next sibling
     *
     * @param      ConnectionInterface $con Connection to use.
     * @return     bool
     */
    public function hasNextSibling(ConnectionInterface $con = null)
    {
        if (!ChildTopicQuery::isValid($this)) {
            return false;
        }

        return ChildTopicQuery::create()
            ->filterByTreeLeft($this->getRightValue() + 1)
            ->inTree($this->getScopeValue())
            ->exists($con);
    }

    /**
     * Gets next sibling for the given node if it exists
     *
     * @param      ConnectionInterface $con Connection to use.
     * @return     ChildTopic|null         Propel object if exists else null
     */
    public function getNextSibling(ConnectionInterface $con = null)
    {
        return ChildTopicQuery::create()
            ->filterByTreeLeft($this->getRightValue() + 1)
            ->inTree($this->getScopeValue())
            ->findOne($con);
    }

    /**
     * Clears out the $collNestedSetChildren collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return     void
     */
    public function clearNestedSetChildren()
    {
        $this->collNestedSetChildren = null;
    }

    /**
     * Initializes the $collNestedSetChildren collection.
     *
     * @return     void
     */
    public function initNestedSetChildren()
    {
        $collectionClassName = \Map\TopicTableMap::getTableMap()->getCollectionClassName();

        $this->collNestedSetChildren = new $collectionClassName;
        $this->collNestedSetChildren->setModel('\Topic');
    }

    /**
     * Adds an element to the internal $collNestedSetChildren collection.
     * Beware that this doesn't insert a node in the tree.
     * This method is only used to facilitate children hydration.
     *
     * @param      ChildTopic $topic
     *
     * @return     void
     */
    public function addNestedSetChild(ChildTopic $topic)
    {
        if (null === $this->collNestedSetChildren) {
            $this->initNestedSetChildren();
        }
        if (!in_array($topic, $this->collNestedSetChildren->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->collNestedSetChildren[]= $topic;
            $topic->setParent($this);
        }
    }

    /**
     * Tests if node has children
     *
     * @return     bool
     */
    public function hasChildren()
    {
        return ($this->getRightValue() - $this->getLeftValue()) > 1;
    }

    /**
     * Gets the children of the given node
     *
     * @param      Criteria  $criteria Criteria to filter results.
     * @param      ConnectionInterface $con Connection to use.
     * @return     ObjectCollection|ChildTopic[] List of ChildTopic objects
     */
    public function getChildren(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        if (null === $this->collNestedSetChildren || null !== $criteria) {
            if ($this->isLeaf() || ($this->isNew() && null === $this->collNestedSetChildren)) {
                // return empty collection
                $this->initNestedSetChildren();
            } else {
                $collNestedSetChildren = ChildTopicQuery::create(null, $criteria)
                    ->childrenOf($this)
                    ->orderByBranch()
                    ->find($con);
                if (null !== $criteria) {
                    return $collNestedSetChildren;
                }
                $this->collNestedSetChildren = $collNestedSetChildren;
            }
        }

        return $this->collNestedSetChildren;
    }

    /**
     * Gets number of children for the given node
     *
     * @param      Criteria  $criteria Criteria to filter results.
     * @param      ConnectionInterface $con Connection to use.
     * @return     int       Number of children
     */
    public function countChildren(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        if (null === $this->collNestedSetChildren || null !== $criteria) {
            if ($this->isLeaf() || ($this->isNew() && null === $this->collNestedSetChildren)) {
                return 0;
            } else {
                return ChildTopicQuery::create(null, $criteria)
                    ->childrenOf($this)
                    ->count($con);
            }
        } else {
            return count($this->collNestedSetChildren);
        }
    }

    /**
     * Gets the first child of the given node
     *
     * @param      Criteria $criteria Criteria to filter results.
     * @param      ConnectionInterface $con Connection to use.
     * @return     ChildTopic|null First child or null if this is a leaf
     */
    public function getFirstChild(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        if ($this->isLeaf()) {
            return null;
        } else {
            return ChildTopicQuery::create(null, $criteria)
                ->childrenOf($this)
                ->orderByBranch()
                ->findOne($con);
        }
    }

    /**
     * Gets the last child of the given node
     *
     * @param      Criteria $criteria Criteria to filter results.
     * @param      ConnectionInterface $con Connection to use.
     * @return     ChildTopic|null Last child or null if this is a leaf
     */
    public function getLastChild(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        if ($this->isLeaf()) {
            return null;
        } else {
            return ChildTopicQuery::create(null, $criteria)
                ->childrenOf($this)
                ->orderByBranch(true)
                ->findOne($con);
        }
    }

    /**
     * Gets the siblings of the given node
     *
     * @param boolean             $includeNode Whether to include the current node or not
     * @param Criteria            $criteria Criteria to filter results.
     * @param ConnectionInterface $con Connection to use.
     *
     * @return ObjectCollection|ChildTopic[] List of ChildTopic objects
     */
    public function getSiblings($includeNode = false, Criteria $criteria = null, ConnectionInterface $con = null)
    {
        if ($this->isRoot()) {
            return array();
        } else {
            $query = ChildTopicQuery::create(null, $criteria)
                ->childrenOf($this->getParent($con))
                ->orderByBranch();
            if (!$includeNode) {
                $query->prune($this);
            }

            return $query->find($con);
        }
    }

    /**
     * Gets descendants for the given node
     *
     * @param      Criteria $criteria Criteria to filter results.
     * @param      ConnectionInterface $con Connection to use.
     * @return     ObjectCollection|ChildTopic[] List of ChildTopic objects
     */
    public function getDescendants(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        if ($this->isLeaf()) {
            return array();
        } else {
            return ChildTopicQuery::create(null, $criteria)
                ->descendantsOf($this)
                ->orderByBranch()
                ->find($con);
        }
    }

    /**
     * Gets number of descendants for the given node
     *
     * @param      Criteria $criteria Criteria to filter results.
     * @param      ConnectionInterface $con Connection to use.
     * @return     int         Number of descendants
     */
    public function countDescendants(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        if ($this->isLeaf()) {
            // save one query
            return 0;
        } else {
            return ChildTopicQuery::create(null, $criteria)
                ->descendantsOf($this)
                ->count($con);
        }
    }

    /**
     * Gets descendants for the given node, plus the current node
     *
     * @param      Criteria $criteria Criteria to filter results.
     * @param      ConnectionInterface $con Connection to use.
     * @return     ObjectCollection|ChildTopic[] List of ChildTopic objects
     */
    public function getBranch(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        return ChildTopicQuery::create(null, $criteria)
            ->branchOf($this)
            ->orderByBranch()
            ->find($con);
    }

    /**
     * Gets ancestors for the given node, starting with the root node
     * Use it for breadcrumb paths for instance
     *
     * @param      Criteria $criteria Criteria to filter results.
     * @param      ConnectionInterface $con Connection to use.
     * @return     ObjectCollection|ChildTopic[] List of ChildTopic objects
     */
    public function getAncestors(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        if ($this->isRoot()) {
            // save one query
            return array();
        } else {
            return ChildTopicQuery::create(null, $criteria)
                ->ancestorsOf($this)
                ->orderByBranch()
                ->find($con);
        }
    }

    /**
     * Inserts the given $child node as first child of current
     * The modifications in the current object and the tree
     * are not persisted until the child object is saved.
     *
     * @param      ChildTopic $child    Propel object for child node
     *
     * @return     $this|ChildTopic The current Propel object
     */
    public function addChild(ChildTopic $child)
    {
        if ($this->isNew()) {
            throw new PropelException('A ChildTopic object must not be new to accept children.');
        }
        $child->insertAsFirstChildOf($this);

        return $this;
    }

    /**
     * Inserts the current node as first child of given $parent node
     * The modifications in the current object and the tree
     * are not persisted until the current object is saved.
     *
     * @param      ChildTopic $parent    Propel object for parent node
     *
     * @return     $this|ChildTopic The current Propel object
     */
    public function insertAsFirstChildOf(ChildTopic $parent)
    {
        if ($this->isInTree()) {
            throw new PropelException('A ChildTopic object must not already be in the tree to be inserted. Use the moveToFirstChildOf() instead.');
        }
        $left = $parent->getLeftValue() + 1;
        // Update node properties
        $this->setLeftValue($left);
        $this->setRightValue($left + 1);
        $this->setLevel($parent->getLevel() + 1);
        $scope = $parent->getScopeValue();
        $this->setScopeValue($scope);
        // update the children collection of the parent
        $parent->addNestedSetChild($this);

        // Keep the tree modification query for the save() transaction
        $this->nestedSetQueries[] = array(
            'callable'  => array('\TopicQuery', 'makeRoomForLeaf'),
            'arguments' => array($left, $scope, $this->isNew() ? null : $this)
        );

        return $this;
    }

    /**
     * Inserts the current node as last child of given $parent node
     * The modifications in the current object and the tree
     * are not persisted until the current object is saved.
     *
     * @param  ChildTopic $parent Propel object for parent node
     * @return $this|ChildTopic The current Propel object
     */
    public function insertAsLastChildOf(ChildTopic $parent)
    {
        if ($this->isInTree()) {
            throw new PropelException(
                'A ChildTopic object must not already be in the tree to be inserted. Use the moveToLastChildOf() instead.'
            );
        }

        $left = $parent->getRightValue();
        // Update node properties
        $this->setLeftValue($left);
        $this->setRightValue($left + 1);
        $this->setLevel($parent->getLevel() + 1);

        $scope = $parent->getScopeValue();
        $this->setScopeValue($scope);

        // update the children collection of the parent
        $parent->addNestedSetChild($this);

        // Keep the tree modification query for the save() transaction
        $this->nestedSetQueries []= array(
            'callable'  => array('\TopicQuery', 'makeRoomForLeaf'),
            'arguments' => array($left, $scope, $this->isNew() ? null : $this)
        );

        return $this;
    }

    /**
     * Inserts the current node as prev sibling given $sibling node
     * The modifications in the current object and the tree
     * are not persisted until the current object is saved.
     *
     * @param      ChildTopic $sibling    Propel object for parent node
     *
     * @return     $this|ChildTopic The current Propel object
     */
    public function insertAsPrevSiblingOf(ChildTopic $sibling)
    {
        if ($this->isInTree()) {
            throw new PropelException('A ChildTopic object must not already be in the tree to be inserted. Use the moveToPrevSiblingOf() instead.');
        }
        $left = $sibling->getLeftValue();
        // Update node properties
        $this->setLeftValue($left);
        $this->setRightValue($left + 1);
        $this->setLevel($sibling->getLevel());
        $scope = $sibling->getScopeValue();
        $this->setScopeValue($scope);
        // Keep the tree modification query for the save() transaction
        $this->nestedSetQueries []= array(
            'callable'  => array('\TopicQuery', 'makeRoomForLeaf'),
            'arguments' => array($left, $scope, $this->isNew() ? null : $this)
        );

        return $this;
    }

    /**
     * Inserts the current node as next sibling given $sibling node
     * The modifications in the current object and the tree
     * are not persisted until the current object is saved.
     *
     * @param      ChildTopic $sibling    Propel object for parent node
     *
     * @return     $this|ChildTopic The current Propel object
     */
    public function insertAsNextSiblingOf(ChildTopic $sibling)
    {
        if ($this->isInTree()) {
            throw new PropelException('A ChildTopic object must not already be in the tree to be inserted. Use the moveToNextSiblingOf() instead.');
        }
        $left = $sibling->getRightValue() + 1;
        // Update node properties
        $this->setLeftValue($left);
        $this->setRightValue($left + 1);
        $this->setLevel($sibling->getLevel());
        $scope = $sibling->getScopeValue();
        $this->setScopeValue($scope);
        // Keep the tree modification query for the save() transaction
        $this->nestedSetQueries []= array(
            'callable'  => array('\TopicQuery', 'makeRoomForLeaf'),
            'arguments' => array($left, $scope, $this->isNew() ? null : $this)
        );

        return $this;
    }

    /**
     * Moves current node and its subtree to be the first child of $parent
     * The modifications in the current object and the tree are immediate
     *
     * @param      ChildTopic $parent    Propel object for parent node
     * @param      ConnectionInterface $con    Connection to use.
     *
     * @return     $this|ChildTopic The current Propel object
     */
    public function moveToFirstChildOf(ChildTopic $parent, ConnectionInterface $con = null)
    {
        if (!$this->isInTree()) {
            throw new PropelException('A ChildTopic object must be already in the tree to be moved. Use the insertAsFirstChildOf() instead.');
        }
        if ($parent->isDescendantOf($this)) {
            throw new PropelException('Cannot move a node as child of one of its subtree nodes.');
        }

        $this->moveSubtreeTo($parent->getLeftValue() + 1, $parent->getLevel() - $this->getLevel() + 1, $parent->getScopeValue(), $con);

        return $this;
    }

    /**
     * Moves current node and its subtree to be the last child of $parent
     * The modifications in the current object and the tree are immediate
     *
     * @param      ChildTopic $parent    Propel object for parent node
     * @param      ConnectionInterface $con    Connection to use.
     *
     * @return     $this|ChildTopic The current Propel object
     */
    public function moveToLastChildOf(ChildTopic $parent, ConnectionInterface $con = null)
    {
        if (!$this->isInTree()) {
            throw new PropelException('A ChildTopic object must be already in the tree to be moved. Use the insertAsLastChildOf() instead.');
        }
        if ($parent->isDescendantOf($this)) {
            throw new PropelException('Cannot move a node as child of one of its subtree nodes.');
        }

        $this->moveSubtreeTo($parent->getRightValue(), $parent->getLevel() - $this->getLevel() + 1, $parent->getScopeValue(), $con);

        return $this;
    }

    /**
     * Moves current node and its subtree to be the previous sibling of $sibling
     * The modifications in the current object and the tree are immediate
     *
     * @param      ChildTopic $sibling    Propel object for sibling node
     * @param      ConnectionInterface $con    Connection to use.
     *
     * @return     $this|ChildTopic The current Propel object
     */
    public function moveToPrevSiblingOf(ChildTopic $sibling, ConnectionInterface $con = null)
    {
        if (!$this->isInTree()) {
            throw new PropelException('A ChildTopic object must be already in the tree to be moved. Use the insertAsPrevSiblingOf() instead.');
        }
        if ($sibling->isRoot()) {
            throw new PropelException('Cannot move to previous sibling of a root node.');
        }
        if ($sibling->isDescendantOf($this)) {
            throw new PropelException('Cannot move a node as sibling of one of its subtree nodes.');
        }

        $this->moveSubtreeTo($sibling->getLeftValue(), $sibling->getLevel() - $this->getLevel(), $sibling->getScopeValue(), $con);

        return $this;
    }

    /**
     * Moves current node and its subtree to be the next sibling of $sibling
     * The modifications in the current object and the tree are immediate
     *
     * @param      ChildTopic $sibling    Propel object for sibling node
     * @param      ConnectionInterface $con    Connection to use.
     *
     * @return     $this|ChildTopic The current Propel object
     */
    public function moveToNextSiblingOf(ChildTopic $sibling, ConnectionInterface $con = null)
    {
        if (!$this->isInTree()) {
            throw new PropelException('A ChildTopic object must be already in the tree to be moved. Use the insertAsNextSiblingOf() instead.');
        }
        if ($sibling->isRoot()) {
            throw new PropelException('Cannot move to next sibling of a root node.');
        }
        if ($sibling->isDescendantOf($this)) {
            throw new PropelException('Cannot move a node as sibling of one of its subtree nodes.');
        }

        $this->moveSubtreeTo($sibling->getRightValue() + 1, $sibling->getLevel() - $this->getLevel(), $sibling->getScopeValue(), $con);

        return $this;
    }

    /**
     * Move current node and its children to location $destLeft and updates rest of tree
     *
     * @param      int    $destLeft Destination left value
     * @param      int    $levelDelta Delta to add to the levels
     * @param      ConnectionInterface $con        Connection to use.
     */
    protected function moveSubtreeTo($destLeft, $levelDelta, $targetScope = null, ConnectionInterface $con = null)
    {
        $left  = $this->getLeftValue();
        $right = $this->getRightValue();
        $scope = $this->getScopeValue();

        if ($targetScope === null) {
            $targetScope = $scope;
        }

        $treeSize = $right - $left +1;

        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(TopicTableMap::DATABASE_NAME);
        }

        $con->transaction(function () use ($con, $treeSize, $destLeft, $left, $right, $levelDelta, $scope, $targetScope) {
            $preventDefault = false;

            // make room next to the target for the subtree
            ChildTopicQuery::shiftRLValues($treeSize, $destLeft, null, $targetScope, $con);

            if ($targetScope != $scope) {

                //move subtree to < 0, so the items are out of scope.
                ChildTopicQuery::shiftRLValues(-$right, $left, $right, $scope, $con);

                //update scopes
                ChildTopicQuery::setNegativeScope($targetScope, $con);

                //update levels
                ChildTopicQuery::shiftLevel($levelDelta, $left - $right, 0, $targetScope, $con);

                //move the subtree to the target
                ChildTopicQuery::shiftRLValues(($right - $left) + $destLeft, $left - $right, 0, $targetScope, $con);


                $preventDefault = true;
            }

            if (!$preventDefault) {

                if ($left >= $destLeft) { // src was shifted too?
                    $left += $treeSize;
                    $right += $treeSize;
                }

                if ($levelDelta) {
                    // update the levels of the subtree
                    ChildTopicQuery::shiftLevel($levelDelta, $left, $right, $scope, $con);
                }

                // move the subtree to the target
                ChildTopicQuery::shiftRLValues($destLeft - $left, $left, $right, $scope, $con);
            }

            // remove the empty room at the previous location of the subtree
            ChildTopicQuery::shiftRLValues(-$treeSize, $right + 1, null, $scope, $con);

            // update all loaded nodes
            ChildTopicQuery::updateLoadedNodes(null, $con);
        });
    }

    /**
     * Deletes all descendants for the given node
     * Instance pooling is wiped out by this command,
     * so existing ChildTopic instances are probably invalid (except for the current one)
     *
     * @param      ConnectionInterface $con Connection to use.
     *
     * @return     int         number of deleted nodes
     */
    public function deleteDescendants(ConnectionInterface $con = null)
    {
        if ($this->isLeaf()) {
            // save one query
            return;
        }
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection(TopicTableMap::DATABASE_NAME);
        }
        $left = $this->getLeftValue();
        $right = $this->getRightValue();
        $scope = $this->getScopeValue();

        return $con->transaction(function () use ($con, $left, $right, $scope) {
            // delete descendant nodes (will empty the instance pool)
            $ret = ChildTopicQuery::create()
                ->descendantsOf($this)
                ->delete($con);

            // fill up the room that was used by descendants
            ChildTopicQuery::shiftRLValues($left - $right + 1, $right, null, $scope, $con);

            // fix the right value for the current node, which is now a leaf
            $this->setRightValue($left + 1);

            return $ret;
        });
    }

    /**
     * Returns a pre-order iterator for this node and its children.
     *
     * @return NestedSetRecursiveIterator
     */
    public function getIterator()
    {
        return new NestedSetRecursiveIterator($this);
    }

    /**
     * Code to be run before persisting the object
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preSave(ConnectionInterface $con = null)
    {
        if (is_callable('parent::preSave')) {
            return parent::preSave($con);
        }
        return true;
    }

    /**
     * Code to be run after persisting the object
     * @param ConnectionInterface $con
     */
    public function postSave(ConnectionInterface $con = null)
    {
        if (is_callable('parent::postSave')) {
            parent::postSave($con);
        }
    }

    /**
     * Code to be run before inserting to database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preInsert(ConnectionInterface $con = null)
    {
        if (is_callable('parent::preInsert')) {
            return parent::preInsert($con);
        }
        return true;
    }

    /**
     * Code to be run after inserting to database
     * @param ConnectionInterface $con
     */
    public function postInsert(ConnectionInterface $con = null)
    {
        if (is_callable('parent::postInsert')) {
            parent::postInsert($con);
        }
    }

    /**
     * Code to be run before updating the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preUpdate(ConnectionInterface $con = null)
    {
        if (is_callable('parent::preUpdate')) {
            return parent::preUpdate($con);
        }
        return true;
    }

    /**
     * Code to be run after updating the object in database
     * @param ConnectionInterface $con
     */
    public function postUpdate(ConnectionInterface $con = null)
    {
        if (is_callable('parent::postUpdate')) {
            parent::postUpdate($con);
        }
    }

    /**
     * Code to be run before deleting the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preDelete(ConnectionInterface $con = null)
    {
        if (is_callable('parent::preDelete')) {
            return parent::preDelete($con);
        }
        return true;
    }

    /**
     * Code to be run after deleting the object in database
     * @param ConnectionInterface $con
     */
    public function postDelete(ConnectionInterface $con = null)
    {
        if (is_callable('parent::postDelete')) {
            parent::postDelete($con);
        }
    }


    /**
     * Derived method to catches calls to undefined methods.
     *
     * Provides magic import/export method support (fromXML()/toXML(), fromYAML()/toYAML(), etc.).
     * Allows to define default __call() behavior if you overwrite __call()
     *
     * @param string $name
     * @param mixed  $params
     *
     * @return array|string
     */
    public function __call($name, $params)
    {
        if (0 === strpos($name, 'get')) {
            $virtualColumn = substr($name, 3);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }

            $virtualColumn = lcfirst($virtualColumn);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }
        }

        if (0 === strpos($name, 'from')) {
            $format = substr($name, 4);

            return $this->importFrom($format, reset($params));
        }

        if (0 === strpos($name, 'to')) {
            $format = substr($name, 2);
            $includeLazyLoadColumns = isset($params[0]) ? $params[0] : true;

            return $this->exportTo($format, $includeLazyLoadColumns);
        }

        throw new BadMethodCallException(sprintf('Call to undefined method: %s.', $name));
    }

}
