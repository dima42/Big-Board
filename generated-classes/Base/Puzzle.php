<?php

namespace Base;

use \Note as ChildNote;
use \NoteQuery as ChildNoteQuery;
use \Puzzle as ChildPuzzle;
use \PuzzleMember as ChildPuzzleMember;
use \PuzzleMemberQuery as ChildPuzzleMemberQuery;
use \PuzzleParent as ChildPuzzleParent;
use \PuzzleParentQuery as ChildPuzzleParentQuery;
use \PuzzleQuery as ChildPuzzleQuery;
use \Exception;
use \PDO;
use Map\NoteTableMap;
use Map\PuzzleMemberTableMap;
use Map\PuzzleParentTableMap;
use Map\PuzzleTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\BadMethodCallException;
use Propel\Runtime\Exception\LogicException;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Parser\AbstractParser;

/**
 * Base class that represents a row from the 'puzzle' table.
 *
 *
 *
 * @package    propel.generator..Base
 */
abstract class Puzzle implements ActiveRecordInterface
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Map\\PuzzleTableMap';


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
     * The value for the url field.
     *
     * @var        string
     */
    protected $url;

    /**
     * The value for the spreadsheet_id field.
     *
     * @var        string
     */
    protected $spreadsheet_id;

    /**
     * The value for the solution field.
     *
     * @var        string
     */
    protected $solution;

    /**
     * The value for the status field.
     *
     * @var        string
     */
    protected $status;

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
     * @var        ObjectCollection|ChildNote[] Collection to store aggregation of ChildNote objects.
     */
    protected $collNotes;
    protected $collNotesPartial;

    /**
     * @var        ObjectCollection|ChildPuzzleMember[] Collection to store aggregation of ChildPuzzleMember objects.
     */
    protected $collPuzzleMembersRelatedByPuzzleId;
    protected $collPuzzleMembersRelatedByPuzzleIdPartial;

    /**
     * @var        ObjectCollection|ChildPuzzleMember[] Collection to store aggregation of ChildPuzzleMember objects.
     */
    protected $collPuzzleMembersRelatedByMemberId;
    protected $collPuzzleMembersRelatedByMemberIdPartial;

    /**
     * @var        ObjectCollection|ChildPuzzleParent[] Collection to store aggregation of ChildPuzzleParent objects.
     */
    protected $collPuzzleParentsRelatedByPuzzleId;
    protected $collPuzzleParentsRelatedByPuzzleIdPartial;

    /**
     * @var        ObjectCollection|ChildPuzzleParent[] Collection to store aggregation of ChildPuzzleParent objects.
     */
    protected $collPuzzleParentsRelatedByParentId;
    protected $collPuzzleParentsRelatedByParentIdPartial;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var boolean
     */
    protected $alreadyInSave = false;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildNote[]
     */
    protected $notesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildPuzzleMember[]
     */
    protected $puzzleMembersRelatedByPuzzleIdScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildPuzzleMember[]
     */
    protected $puzzleMembersRelatedByMemberIdScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildPuzzleParent[]
     */
    protected $puzzleParentsRelatedByPuzzleIdScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildPuzzleParent[]
     */
    protected $puzzleParentsRelatedByParentIdScheduledForDeletion = null;

    /**
     * Initializes internal state of Base\Puzzle object.
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
     * Compares this with another <code>Puzzle</code> instance.  If
     * <code>obj</code> is an instance of <code>Puzzle</code>, delegates to
     * <code>equals(Puzzle)</code>.  Otherwise, returns <code>false</code>.
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
     * @return $this|Puzzle The current object, for fluid interface
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
     * Get the [url] column value.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Get the [spreadsheet_id] column value.
     *
     * @return string
     */
    public function getSpreadsheetId()
    {
        return $this->spreadsheet_id;
    }

    /**
     * Get the [solution] column value.
     *
     * @return string
     */
    public function getSolution()
    {
        return $this->solution;
    }

    /**
     * Get the [status] column value.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
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
     * Set the value of [id] column.
     *
     * @param int $v new value
     * @return $this|\Puzzle The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[PuzzleTableMap::COL_ID] = true;
        }

        return $this;
    } // setId()

    /**
     * Set the value of [title] column.
     *
     * @param string $v new value
     * @return $this|\Puzzle The current object (for fluent API support)
     */
    public function setTitle($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->title !== $v) {
            $this->title = $v;
            $this->modifiedColumns[PuzzleTableMap::COL_TITLE] = true;
        }

        return $this;
    } // setTitle()

    /**
     * Set the value of [url] column.
     *
     * @param string $v new value
     * @return $this|\Puzzle The current object (for fluent API support)
     */
    public function setUrl($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->url !== $v) {
            $this->url = $v;
            $this->modifiedColumns[PuzzleTableMap::COL_URL] = true;
        }

        return $this;
    } // setUrl()

    /**
     * Set the value of [spreadsheet_id] column.
     *
     * @param string $v new value
     * @return $this|\Puzzle The current object (for fluent API support)
     */
    public function setSpreadsheetId($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->spreadsheet_id !== $v) {
            $this->spreadsheet_id = $v;
            $this->modifiedColumns[PuzzleTableMap::COL_SPREADSHEET_ID] = true;
        }

        return $this;
    } // setSpreadsheetId()

    /**
     * Set the value of [solution] column.
     *
     * @param string $v new value
     * @return $this|\Puzzle The current object (for fluent API support)
     */
    public function setSolution($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->solution !== $v) {
            $this->solution = $v;
            $this->modifiedColumns[PuzzleTableMap::COL_SOLUTION] = true;
        }

        return $this;
    } // setSolution()

    /**
     * Set the value of [status] column.
     *
     * @param string $v new value
     * @return $this|\Puzzle The current object (for fluent API support)
     */
    public function setStatus($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->status !== $v) {
            $this->status = $v;
            $this->modifiedColumns[PuzzleTableMap::COL_STATUS] = true;
        }

        return $this;
    } // setStatus()

    /**
     * Set the value of [slack_channel] column.
     *
     * @param string $v new value
     * @return $this|\Puzzle The current object (for fluent API support)
     */
    public function setSlackChannel($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->slack_channel !== $v) {
            $this->slack_channel = $v;
            $this->modifiedColumns[PuzzleTableMap::COL_SLACK_CHANNEL] = true;
        }

        return $this;
    } // setSlackChannel()

    /**
     * Set the value of [slack_channel_id] column.
     *
     * @param string $v new value
     * @return $this|\Puzzle The current object (for fluent API support)
     */
    public function setSlackChannelId($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->slack_channel_id !== $v) {
            $this->slack_channel_id = $v;
            $this->modifiedColumns[PuzzleTableMap::COL_SLACK_CHANNEL_ID] = true;
        }

        return $this;
    } // setSlackChannelId()

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

            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : PuzzleTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : PuzzleTableMap::translateFieldName('Title', TableMap::TYPE_PHPNAME, $indexType)];
            $this->title = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : PuzzleTableMap::translateFieldName('Url', TableMap::TYPE_PHPNAME, $indexType)];
            $this->url = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : PuzzleTableMap::translateFieldName('SpreadsheetId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->spreadsheet_id = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : PuzzleTableMap::translateFieldName('Solution', TableMap::TYPE_PHPNAME, $indexType)];
            $this->solution = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : PuzzleTableMap::translateFieldName('Status', TableMap::TYPE_PHPNAME, $indexType)];
            $this->status = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : PuzzleTableMap::translateFieldName('SlackChannel', TableMap::TYPE_PHPNAME, $indexType)];
            $this->slack_channel = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 7 + $startcol : PuzzleTableMap::translateFieldName('SlackChannelId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->slack_channel_id = (null !== $col) ? (string) $col : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 8; // 8 = PuzzleTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException(sprintf('Error populating %s object', '\\Puzzle'), 0, $e);
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
            $con = Propel::getServiceContainer()->getReadConnection(PuzzleTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildPuzzleQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collNotes = null;

            $this->collPuzzleMembersRelatedByPuzzleId = null;

            $this->collPuzzleMembersRelatedByMemberId = null;

            $this->collPuzzleParentsRelatedByPuzzleId = null;

            $this->collPuzzleParentsRelatedByParentId = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see Puzzle::setDeleted()
     * @see Puzzle::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(PuzzleTableMap::DATABASE_NAME);
        }

        $con->transaction(function () use ($con) {
            $deleteQuery = ChildPuzzleQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
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
            $con = Propel::getServiceContainer()->getWriteConnection(PuzzleTableMap::DATABASE_NAME);
        }

        return $con->transaction(function () use ($con) {
            $ret = $this->preSave($con);
            $isInsert = $this->isNew();
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
                PuzzleTableMap::addInstanceToPool($this);
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

            if ($this->notesScheduledForDeletion !== null) {
                if (!$this->notesScheduledForDeletion->isEmpty()) {
                    \NoteQuery::create()
                        ->filterByPrimaryKeys($this->notesScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->notesScheduledForDeletion = null;
                }
            }

            if ($this->collNotes !== null) {
                foreach ($this->collNotes as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->puzzleMembersRelatedByPuzzleIdScheduledForDeletion !== null) {
                if (!$this->puzzleMembersRelatedByPuzzleIdScheduledForDeletion->isEmpty()) {
                    \PuzzleMemberQuery::create()
                        ->filterByPrimaryKeys($this->puzzleMembersRelatedByPuzzleIdScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->puzzleMembersRelatedByPuzzleIdScheduledForDeletion = null;
                }
            }

            if ($this->collPuzzleMembersRelatedByPuzzleId !== null) {
                foreach ($this->collPuzzleMembersRelatedByPuzzleId as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->puzzleMembersRelatedByMemberIdScheduledForDeletion !== null) {
                if (!$this->puzzleMembersRelatedByMemberIdScheduledForDeletion->isEmpty()) {
                    \PuzzleMemberQuery::create()
                        ->filterByPrimaryKeys($this->puzzleMembersRelatedByMemberIdScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->puzzleMembersRelatedByMemberIdScheduledForDeletion = null;
                }
            }

            if ($this->collPuzzleMembersRelatedByMemberId !== null) {
                foreach ($this->collPuzzleMembersRelatedByMemberId as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->puzzleParentsRelatedByPuzzleIdScheduledForDeletion !== null) {
                if (!$this->puzzleParentsRelatedByPuzzleIdScheduledForDeletion->isEmpty()) {
                    \PuzzleParentQuery::create()
                        ->filterByPrimaryKeys($this->puzzleParentsRelatedByPuzzleIdScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->puzzleParentsRelatedByPuzzleIdScheduledForDeletion = null;
                }
            }

            if ($this->collPuzzleParentsRelatedByPuzzleId !== null) {
                foreach ($this->collPuzzleParentsRelatedByPuzzleId as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->puzzleParentsRelatedByParentIdScheduledForDeletion !== null) {
                if (!$this->puzzleParentsRelatedByParentIdScheduledForDeletion->isEmpty()) {
                    \PuzzleParentQuery::create()
                        ->filterByPrimaryKeys($this->puzzleParentsRelatedByParentIdScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->puzzleParentsRelatedByParentIdScheduledForDeletion = null;
                }
            }

            if ($this->collPuzzleParentsRelatedByParentId !== null) {
                foreach ($this->collPuzzleParentsRelatedByParentId as $referrerFK) {
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

        $this->modifiedColumns[PuzzleTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . PuzzleTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(PuzzleTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'id';
        }
        if ($this->isColumnModified(PuzzleTableMap::COL_TITLE)) {
            $modifiedColumns[':p' . $index++]  = 'title';
        }
        if ($this->isColumnModified(PuzzleTableMap::COL_URL)) {
            $modifiedColumns[':p' . $index++]  = 'url';
        }
        if ($this->isColumnModified(PuzzleTableMap::COL_SPREADSHEET_ID)) {
            $modifiedColumns[':p' . $index++]  = 'spreadsheet_id';
        }
        if ($this->isColumnModified(PuzzleTableMap::COL_SOLUTION)) {
            $modifiedColumns[':p' . $index++]  = 'solution';
        }
        if ($this->isColumnModified(PuzzleTableMap::COL_STATUS)) {
            $modifiedColumns[':p' . $index++]  = 'status';
        }
        if ($this->isColumnModified(PuzzleTableMap::COL_SLACK_CHANNEL)) {
            $modifiedColumns[':p' . $index++]  = 'slack_channel';
        }
        if ($this->isColumnModified(PuzzleTableMap::COL_SLACK_CHANNEL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'slack_channel_id';
        }

        $sql = sprintf(
            'INSERT INTO puzzle (%s) VALUES (%s)',
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
                    case 'url':
                        $stmt->bindValue($identifier, $this->url, PDO::PARAM_STR);
                        break;
                    case 'spreadsheet_id':
                        $stmt->bindValue($identifier, $this->spreadsheet_id, PDO::PARAM_STR);
                        break;
                    case 'solution':
                        $stmt->bindValue($identifier, $this->solution, PDO::PARAM_STR);
                        break;
                    case 'status':
                        $stmt->bindValue($identifier, $this->status, PDO::PARAM_STR);
                        break;
                    case 'slack_channel':
                        $stmt->bindValue($identifier, $this->slack_channel, PDO::PARAM_STR);
                        break;
                    case 'slack_channel_id':
                        $stmt->bindValue($identifier, $this->slack_channel_id, PDO::PARAM_STR);
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
        $pos = PuzzleTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
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
                return $this->getUrl();
                break;
            case 3:
                return $this->getSpreadsheetId();
                break;
            case 4:
                return $this->getSolution();
                break;
            case 5:
                return $this->getStatus();
                break;
            case 6:
                return $this->getSlackChannel();
                break;
            case 7:
                return $this->getSlackChannelId();
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

        if (isset($alreadyDumpedObjects['Puzzle'][$this->hashCode()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Puzzle'][$this->hashCode()] = true;
        $keys = PuzzleTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getTitle(),
            $keys[2] => $this->getUrl(),
            $keys[3] => $this->getSpreadsheetId(),
            $keys[4] => $this->getSolution(),
            $keys[5] => $this->getStatus(),
            $keys[6] => $this->getSlackChannel(),
            $keys[7] => $this->getSlackChannelId(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->collNotes) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'notes';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'notes';
                        break;
                    default:
                        $key = 'Notes';
                }

                $result[$key] = $this->collNotes->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collPuzzleMembersRelatedByPuzzleId) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'puzzleMembers';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'solvers';
                        break;
                    default:
                        $key = 'PuzzleMembers';
                }

                $result[$key] = $this->collPuzzleMembersRelatedByPuzzleId->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collPuzzleMembersRelatedByMemberId) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'puzzleMembers';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'solvers';
                        break;
                    default:
                        $key = 'PuzzleMembers';
                }

                $result[$key] = $this->collPuzzleMembersRelatedByMemberId->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collPuzzleParentsRelatedByPuzzleId) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'puzzleParents';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'relationships';
                        break;
                    default:
                        $key = 'PuzzleParents';
                }

                $result[$key] = $this->collPuzzleParentsRelatedByPuzzleId->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collPuzzleParentsRelatedByParentId) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'puzzleParents';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'relationships';
                        break;
                    default:
                        $key = 'PuzzleParents';
                }

                $result[$key] = $this->collPuzzleParentsRelatedByParentId->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
     * @return $this|\Puzzle
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = PuzzleTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param  int $pos position in xml schema
     * @param  mixed $value field value
     * @return $this|\Puzzle
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
                $this->setUrl($value);
                break;
            case 3:
                $this->setSpreadsheetId($value);
                break;
            case 4:
                $this->setSolution($value);
                break;
            case 5:
                $this->setStatus($value);
                break;
            case 6:
                $this->setSlackChannel($value);
                break;
            case 7:
                $this->setSlackChannelId($value);
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
        $keys = PuzzleTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setId($arr[$keys[0]]);
        }
        if (array_key_exists($keys[1], $arr)) {
            $this->setTitle($arr[$keys[1]]);
        }
        if (array_key_exists($keys[2], $arr)) {
            $this->setUrl($arr[$keys[2]]);
        }
        if (array_key_exists($keys[3], $arr)) {
            $this->setSpreadsheetId($arr[$keys[3]]);
        }
        if (array_key_exists($keys[4], $arr)) {
            $this->setSolution($arr[$keys[4]]);
        }
        if (array_key_exists($keys[5], $arr)) {
            $this->setStatus($arr[$keys[5]]);
        }
        if (array_key_exists($keys[6], $arr)) {
            $this->setSlackChannel($arr[$keys[6]]);
        }
        if (array_key_exists($keys[7], $arr)) {
            $this->setSlackChannelId($arr[$keys[7]]);
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
     * @return $this|\Puzzle The current object, for fluid interface
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
        $criteria = new Criteria(PuzzleTableMap::DATABASE_NAME);

        if ($this->isColumnModified(PuzzleTableMap::COL_ID)) {
            $criteria->add(PuzzleTableMap::COL_ID, $this->id);
        }
        if ($this->isColumnModified(PuzzleTableMap::COL_TITLE)) {
            $criteria->add(PuzzleTableMap::COL_TITLE, $this->title);
        }
        if ($this->isColumnModified(PuzzleTableMap::COL_URL)) {
            $criteria->add(PuzzleTableMap::COL_URL, $this->url);
        }
        if ($this->isColumnModified(PuzzleTableMap::COL_SPREADSHEET_ID)) {
            $criteria->add(PuzzleTableMap::COL_SPREADSHEET_ID, $this->spreadsheet_id);
        }
        if ($this->isColumnModified(PuzzleTableMap::COL_SOLUTION)) {
            $criteria->add(PuzzleTableMap::COL_SOLUTION, $this->solution);
        }
        if ($this->isColumnModified(PuzzleTableMap::COL_STATUS)) {
            $criteria->add(PuzzleTableMap::COL_STATUS, $this->status);
        }
        if ($this->isColumnModified(PuzzleTableMap::COL_SLACK_CHANNEL)) {
            $criteria->add(PuzzleTableMap::COL_SLACK_CHANNEL, $this->slack_channel);
        }
        if ($this->isColumnModified(PuzzleTableMap::COL_SLACK_CHANNEL_ID)) {
            $criteria->add(PuzzleTableMap::COL_SLACK_CHANNEL_ID, $this->slack_channel_id);
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
        $criteria = ChildPuzzleQuery::create();
        $criteria->add(PuzzleTableMap::COL_ID, $this->id);

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
     * @param      object $copyObj An object of \Puzzle (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setTitle($this->getTitle());
        $copyObj->setUrl($this->getUrl());
        $copyObj->setSpreadsheetId($this->getSpreadsheetId());
        $copyObj->setSolution($this->getSolution());
        $copyObj->setStatus($this->getStatus());
        $copyObj->setSlackChannel($this->getSlackChannel());
        $copyObj->setSlackChannelId($this->getSlackChannelId());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getNotes() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addNote($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getPuzzleMembersRelatedByPuzzleId() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addPuzzleMemberRelatedByPuzzleId($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getPuzzleMembersRelatedByMemberId() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addPuzzleMemberRelatedByMemberId($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getPuzzleParentsRelatedByPuzzleId() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addPuzzleParentRelatedByPuzzleId($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getPuzzleParentsRelatedByParentId() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addPuzzleParentRelatedByParentId($relObj->copy($deepCopy));
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
     * @return \Puzzle Clone of current object.
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
        if ('Note' == $relationName) {
            $this->initNotes();
            return;
        }
        if ('PuzzleMemberRelatedByPuzzleId' == $relationName) {
            $this->initPuzzleMembersRelatedByPuzzleId();
            return;
        }
        if ('PuzzleMemberRelatedByMemberId' == $relationName) {
            $this->initPuzzleMembersRelatedByMemberId();
            return;
        }
        if ('PuzzleParentRelatedByPuzzleId' == $relationName) {
            $this->initPuzzleParentsRelatedByPuzzleId();
            return;
        }
        if ('PuzzleParentRelatedByParentId' == $relationName) {
            $this->initPuzzleParentsRelatedByParentId();
            return;
        }
    }

    /**
     * Clears out the collNotes collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addNotes()
     */
    public function clearNotes()
    {
        $this->collNotes = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collNotes collection loaded partially.
     */
    public function resetPartialNotes($v = true)
    {
        $this->collNotesPartial = $v;
    }

    /**
     * Initializes the collNotes collection.
     *
     * By default this just sets the collNotes collection to an empty array (like clearcollNotes());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initNotes($overrideExisting = true)
    {
        if (null !== $this->collNotes && !$overrideExisting) {
            return;
        }

        $collectionClassName = NoteTableMap::getTableMap()->getCollectionClassName();

        $this->collNotes = new $collectionClassName;
        $this->collNotes->setModel('\Note');
    }

    /**
     * Gets an array of ChildNote objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildPuzzle is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildNote[] List of ChildNote objects
     * @throws PropelException
     */
    public function getNotes(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collNotesPartial && !$this->isNew();
        if (null === $this->collNotes || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collNotes) {
                // return empty collection
                $this->initNotes();
            } else {
                $collNotes = ChildNoteQuery::create(null, $criteria)
                    ->filterByPuzzle($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collNotesPartial && count($collNotes)) {
                        $this->initNotes(false);

                        foreach ($collNotes as $obj) {
                            if (false == $this->collNotes->contains($obj)) {
                                $this->collNotes->append($obj);
                            }
                        }

                        $this->collNotesPartial = true;
                    }

                    return $collNotes;
                }

                if ($partial && $this->collNotes) {
                    foreach ($this->collNotes as $obj) {
                        if ($obj->isNew()) {
                            $collNotes[] = $obj;
                        }
                    }
                }

                $this->collNotes = $collNotes;
                $this->collNotesPartial = false;
            }
        }

        return $this->collNotes;
    }

    /**
     * Sets a collection of ChildNote objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $notes A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildPuzzle The current object (for fluent API support)
     */
    public function setNotes(Collection $notes, ConnectionInterface $con = null)
    {
        /** @var ChildNote[] $notesToDelete */
        $notesToDelete = $this->getNotes(new Criteria(), $con)->diff($notes);


        $this->notesScheduledForDeletion = $notesToDelete;

        foreach ($notesToDelete as $noteRemoved) {
            $noteRemoved->setPuzzle(null);
        }

        $this->collNotes = null;
        foreach ($notes as $note) {
            $this->addNote($note);
        }

        $this->collNotes = $notes;
        $this->collNotesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Note objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Note objects.
     * @throws PropelException
     */
    public function countNotes(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collNotesPartial && !$this->isNew();
        if (null === $this->collNotes || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collNotes) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getNotes());
            }

            $query = ChildNoteQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByPuzzle($this)
                ->count($con);
        }

        return count($this->collNotes);
    }

    /**
     * Method called to associate a ChildNote object to this object
     * through the ChildNote foreign key attribute.
     *
     * @param  ChildNote $l ChildNote
     * @return $this|\Puzzle The current object (for fluent API support)
     */
    public function addNote(ChildNote $l)
    {
        if ($this->collNotes === null) {
            $this->initNotes();
            $this->collNotesPartial = true;
        }

        if (!$this->collNotes->contains($l)) {
            $this->doAddNote($l);

            if ($this->notesScheduledForDeletion and $this->notesScheduledForDeletion->contains($l)) {
                $this->notesScheduledForDeletion->remove($this->notesScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildNote $note The ChildNote object to add.
     */
    protected function doAddNote(ChildNote $note)
    {
        $this->collNotes[]= $note;
        $note->setPuzzle($this);
    }

    /**
     * @param  ChildNote $note The ChildNote object to remove.
     * @return $this|ChildPuzzle The current object (for fluent API support)
     */
    public function removeNote(ChildNote $note)
    {
        if ($this->getNotes()->contains($note)) {
            $pos = $this->collNotes->search($note);
            $this->collNotes->remove($pos);
            if (null === $this->notesScheduledForDeletion) {
                $this->notesScheduledForDeletion = clone $this->collNotes;
                $this->notesScheduledForDeletion->clear();
            }
            $this->notesScheduledForDeletion[]= clone $note;
            $note->setPuzzle(null);
        }

        return $this;
    }

    /**
     * Clears out the collPuzzleMembersRelatedByPuzzleId collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addPuzzleMembersRelatedByPuzzleId()
     */
    public function clearPuzzleMembersRelatedByPuzzleId()
    {
        $this->collPuzzleMembersRelatedByPuzzleId = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collPuzzleMembersRelatedByPuzzleId collection loaded partially.
     */
    public function resetPartialPuzzleMembersRelatedByPuzzleId($v = true)
    {
        $this->collPuzzleMembersRelatedByPuzzleIdPartial = $v;
    }

    /**
     * Initializes the collPuzzleMembersRelatedByPuzzleId collection.
     *
     * By default this just sets the collPuzzleMembersRelatedByPuzzleId collection to an empty array (like clearcollPuzzleMembersRelatedByPuzzleId());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initPuzzleMembersRelatedByPuzzleId($overrideExisting = true)
    {
        if (null !== $this->collPuzzleMembersRelatedByPuzzleId && !$overrideExisting) {
            return;
        }

        $collectionClassName = PuzzleMemberTableMap::getTableMap()->getCollectionClassName();

        $this->collPuzzleMembersRelatedByPuzzleId = new $collectionClassName;
        $this->collPuzzleMembersRelatedByPuzzleId->setModel('\PuzzleMember');
    }

    /**
     * Gets an array of ChildPuzzleMember objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildPuzzle is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildPuzzleMember[] List of ChildPuzzleMember objects
     * @throws PropelException
     */
    public function getPuzzleMembersRelatedByPuzzleId(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collPuzzleMembersRelatedByPuzzleIdPartial && !$this->isNew();
        if (null === $this->collPuzzleMembersRelatedByPuzzleId || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collPuzzleMembersRelatedByPuzzleId) {
                // return empty collection
                $this->initPuzzleMembersRelatedByPuzzleId();
            } else {
                $collPuzzleMembersRelatedByPuzzleId = ChildPuzzleMemberQuery::create(null, $criteria)
                    ->filterByPuzzle($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collPuzzleMembersRelatedByPuzzleIdPartial && count($collPuzzleMembersRelatedByPuzzleId)) {
                        $this->initPuzzleMembersRelatedByPuzzleId(false);

                        foreach ($collPuzzleMembersRelatedByPuzzleId as $obj) {
                            if (false == $this->collPuzzleMembersRelatedByPuzzleId->contains($obj)) {
                                $this->collPuzzleMembersRelatedByPuzzleId->append($obj);
                            }
                        }

                        $this->collPuzzleMembersRelatedByPuzzleIdPartial = true;
                    }

                    return $collPuzzleMembersRelatedByPuzzleId;
                }

                if ($partial && $this->collPuzzleMembersRelatedByPuzzleId) {
                    foreach ($this->collPuzzleMembersRelatedByPuzzleId as $obj) {
                        if ($obj->isNew()) {
                            $collPuzzleMembersRelatedByPuzzleId[] = $obj;
                        }
                    }
                }

                $this->collPuzzleMembersRelatedByPuzzleId = $collPuzzleMembersRelatedByPuzzleId;
                $this->collPuzzleMembersRelatedByPuzzleIdPartial = false;
            }
        }

        return $this->collPuzzleMembersRelatedByPuzzleId;
    }

    /**
     * Sets a collection of ChildPuzzleMember objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $puzzleMembersRelatedByPuzzleId A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildPuzzle The current object (for fluent API support)
     */
    public function setPuzzleMembersRelatedByPuzzleId(Collection $puzzleMembersRelatedByPuzzleId, ConnectionInterface $con = null)
    {
        /** @var ChildPuzzleMember[] $puzzleMembersRelatedByPuzzleIdToDelete */
        $puzzleMembersRelatedByPuzzleIdToDelete = $this->getPuzzleMembersRelatedByPuzzleId(new Criteria(), $con)->diff($puzzleMembersRelatedByPuzzleId);


        $this->puzzleMembersRelatedByPuzzleIdScheduledForDeletion = $puzzleMembersRelatedByPuzzleIdToDelete;

        foreach ($puzzleMembersRelatedByPuzzleIdToDelete as $puzzleMemberRelatedByPuzzleIdRemoved) {
            $puzzleMemberRelatedByPuzzleIdRemoved->setPuzzle(null);
        }

        $this->collPuzzleMembersRelatedByPuzzleId = null;
        foreach ($puzzleMembersRelatedByPuzzleId as $puzzleMemberRelatedByPuzzleId) {
            $this->addPuzzleMemberRelatedByPuzzleId($puzzleMemberRelatedByPuzzleId);
        }

        $this->collPuzzleMembersRelatedByPuzzleId = $puzzleMembersRelatedByPuzzleId;
        $this->collPuzzleMembersRelatedByPuzzleIdPartial = false;

        return $this;
    }

    /**
     * Returns the number of related PuzzleMember objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related PuzzleMember objects.
     * @throws PropelException
     */
    public function countPuzzleMembersRelatedByPuzzleId(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collPuzzleMembersRelatedByPuzzleIdPartial && !$this->isNew();
        if (null === $this->collPuzzleMembersRelatedByPuzzleId || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collPuzzleMembersRelatedByPuzzleId) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getPuzzleMembersRelatedByPuzzleId());
            }

            $query = ChildPuzzleMemberQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByPuzzle($this)
                ->count($con);
        }

        return count($this->collPuzzleMembersRelatedByPuzzleId);
    }

    /**
     * Method called to associate a ChildPuzzleMember object to this object
     * through the ChildPuzzleMember foreign key attribute.
     *
     * @param  ChildPuzzleMember $l ChildPuzzleMember
     * @return $this|\Puzzle The current object (for fluent API support)
     */
    public function addPuzzleMemberRelatedByPuzzleId(ChildPuzzleMember $l)
    {
        if ($this->collPuzzleMembersRelatedByPuzzleId === null) {
            $this->initPuzzleMembersRelatedByPuzzleId();
            $this->collPuzzleMembersRelatedByPuzzleIdPartial = true;
        }

        if (!$this->collPuzzleMembersRelatedByPuzzleId->contains($l)) {
            $this->doAddPuzzleMemberRelatedByPuzzleId($l);

            if ($this->puzzleMembersRelatedByPuzzleIdScheduledForDeletion and $this->puzzleMembersRelatedByPuzzleIdScheduledForDeletion->contains($l)) {
                $this->puzzleMembersRelatedByPuzzleIdScheduledForDeletion->remove($this->puzzleMembersRelatedByPuzzleIdScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildPuzzleMember $puzzleMemberRelatedByPuzzleId The ChildPuzzleMember object to add.
     */
    protected function doAddPuzzleMemberRelatedByPuzzleId(ChildPuzzleMember $puzzleMemberRelatedByPuzzleId)
    {
        $this->collPuzzleMembersRelatedByPuzzleId[]= $puzzleMemberRelatedByPuzzleId;
        $puzzleMemberRelatedByPuzzleId->setPuzzle($this);
    }

    /**
     * @param  ChildPuzzleMember $puzzleMemberRelatedByPuzzleId The ChildPuzzleMember object to remove.
     * @return $this|ChildPuzzle The current object (for fluent API support)
     */
    public function removePuzzleMemberRelatedByPuzzleId(ChildPuzzleMember $puzzleMemberRelatedByPuzzleId)
    {
        if ($this->getPuzzleMembersRelatedByPuzzleId()->contains($puzzleMemberRelatedByPuzzleId)) {
            $pos = $this->collPuzzleMembersRelatedByPuzzleId->search($puzzleMemberRelatedByPuzzleId);
            $this->collPuzzleMembersRelatedByPuzzleId->remove($pos);
            if (null === $this->puzzleMembersRelatedByPuzzleIdScheduledForDeletion) {
                $this->puzzleMembersRelatedByPuzzleIdScheduledForDeletion = clone $this->collPuzzleMembersRelatedByPuzzleId;
                $this->puzzleMembersRelatedByPuzzleIdScheduledForDeletion->clear();
            }
            $this->puzzleMembersRelatedByPuzzleIdScheduledForDeletion[]= clone $puzzleMemberRelatedByPuzzleId;
            $puzzleMemberRelatedByPuzzleId->setPuzzle(null);
        }

        return $this;
    }

    /**
     * Clears out the collPuzzleMembersRelatedByMemberId collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addPuzzleMembersRelatedByMemberId()
     */
    public function clearPuzzleMembersRelatedByMemberId()
    {
        $this->collPuzzleMembersRelatedByMemberId = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collPuzzleMembersRelatedByMemberId collection loaded partially.
     */
    public function resetPartialPuzzleMembersRelatedByMemberId($v = true)
    {
        $this->collPuzzleMembersRelatedByMemberIdPartial = $v;
    }

    /**
     * Initializes the collPuzzleMembersRelatedByMemberId collection.
     *
     * By default this just sets the collPuzzleMembersRelatedByMemberId collection to an empty array (like clearcollPuzzleMembersRelatedByMemberId());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initPuzzleMembersRelatedByMemberId($overrideExisting = true)
    {
        if (null !== $this->collPuzzleMembersRelatedByMemberId && !$overrideExisting) {
            return;
        }

        $collectionClassName = PuzzleMemberTableMap::getTableMap()->getCollectionClassName();

        $this->collPuzzleMembersRelatedByMemberId = new $collectionClassName;
        $this->collPuzzleMembersRelatedByMemberId->setModel('\PuzzleMember');
    }

    /**
     * Gets an array of ChildPuzzleMember objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildPuzzle is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildPuzzleMember[] List of ChildPuzzleMember objects
     * @throws PropelException
     */
    public function getPuzzleMembersRelatedByMemberId(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collPuzzleMembersRelatedByMemberIdPartial && !$this->isNew();
        if (null === $this->collPuzzleMembersRelatedByMemberId || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collPuzzleMembersRelatedByMemberId) {
                // return empty collection
                $this->initPuzzleMembersRelatedByMemberId();
            } else {
                $collPuzzleMembersRelatedByMemberId = ChildPuzzleMemberQuery::create(null, $criteria)
                    ->filterByMember($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collPuzzleMembersRelatedByMemberIdPartial && count($collPuzzleMembersRelatedByMemberId)) {
                        $this->initPuzzleMembersRelatedByMemberId(false);

                        foreach ($collPuzzleMembersRelatedByMemberId as $obj) {
                            if (false == $this->collPuzzleMembersRelatedByMemberId->contains($obj)) {
                                $this->collPuzzleMembersRelatedByMemberId->append($obj);
                            }
                        }

                        $this->collPuzzleMembersRelatedByMemberIdPartial = true;
                    }

                    return $collPuzzleMembersRelatedByMemberId;
                }

                if ($partial && $this->collPuzzleMembersRelatedByMemberId) {
                    foreach ($this->collPuzzleMembersRelatedByMemberId as $obj) {
                        if ($obj->isNew()) {
                            $collPuzzleMembersRelatedByMemberId[] = $obj;
                        }
                    }
                }

                $this->collPuzzleMembersRelatedByMemberId = $collPuzzleMembersRelatedByMemberId;
                $this->collPuzzleMembersRelatedByMemberIdPartial = false;
            }
        }

        return $this->collPuzzleMembersRelatedByMemberId;
    }

    /**
     * Sets a collection of ChildPuzzleMember objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $puzzleMembersRelatedByMemberId A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildPuzzle The current object (for fluent API support)
     */
    public function setPuzzleMembersRelatedByMemberId(Collection $puzzleMembersRelatedByMemberId, ConnectionInterface $con = null)
    {
        /** @var ChildPuzzleMember[] $puzzleMembersRelatedByMemberIdToDelete */
        $puzzleMembersRelatedByMemberIdToDelete = $this->getPuzzleMembersRelatedByMemberId(new Criteria(), $con)->diff($puzzleMembersRelatedByMemberId);


        $this->puzzleMembersRelatedByMemberIdScheduledForDeletion = $puzzleMembersRelatedByMemberIdToDelete;

        foreach ($puzzleMembersRelatedByMemberIdToDelete as $puzzleMemberRelatedByMemberIdRemoved) {
            $puzzleMemberRelatedByMemberIdRemoved->setMember(null);
        }

        $this->collPuzzleMembersRelatedByMemberId = null;
        foreach ($puzzleMembersRelatedByMemberId as $puzzleMemberRelatedByMemberId) {
            $this->addPuzzleMemberRelatedByMemberId($puzzleMemberRelatedByMemberId);
        }

        $this->collPuzzleMembersRelatedByMemberId = $puzzleMembersRelatedByMemberId;
        $this->collPuzzleMembersRelatedByMemberIdPartial = false;

        return $this;
    }

    /**
     * Returns the number of related PuzzleMember objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related PuzzleMember objects.
     * @throws PropelException
     */
    public function countPuzzleMembersRelatedByMemberId(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collPuzzleMembersRelatedByMemberIdPartial && !$this->isNew();
        if (null === $this->collPuzzleMembersRelatedByMemberId || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collPuzzleMembersRelatedByMemberId) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getPuzzleMembersRelatedByMemberId());
            }

            $query = ChildPuzzleMemberQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByMember($this)
                ->count($con);
        }

        return count($this->collPuzzleMembersRelatedByMemberId);
    }

    /**
     * Method called to associate a ChildPuzzleMember object to this object
     * through the ChildPuzzleMember foreign key attribute.
     *
     * @param  ChildPuzzleMember $l ChildPuzzleMember
     * @return $this|\Puzzle The current object (for fluent API support)
     */
    public function addPuzzleMemberRelatedByMemberId(ChildPuzzleMember $l)
    {
        if ($this->collPuzzleMembersRelatedByMemberId === null) {
            $this->initPuzzleMembersRelatedByMemberId();
            $this->collPuzzleMembersRelatedByMemberIdPartial = true;
        }

        if (!$this->collPuzzleMembersRelatedByMemberId->contains($l)) {
            $this->doAddPuzzleMemberRelatedByMemberId($l);

            if ($this->puzzleMembersRelatedByMemberIdScheduledForDeletion and $this->puzzleMembersRelatedByMemberIdScheduledForDeletion->contains($l)) {
                $this->puzzleMembersRelatedByMemberIdScheduledForDeletion->remove($this->puzzleMembersRelatedByMemberIdScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildPuzzleMember $puzzleMemberRelatedByMemberId The ChildPuzzleMember object to add.
     */
    protected function doAddPuzzleMemberRelatedByMemberId(ChildPuzzleMember $puzzleMemberRelatedByMemberId)
    {
        $this->collPuzzleMembersRelatedByMemberId[]= $puzzleMemberRelatedByMemberId;
        $puzzleMemberRelatedByMemberId->setMember($this);
    }

    /**
     * @param  ChildPuzzleMember $puzzleMemberRelatedByMemberId The ChildPuzzleMember object to remove.
     * @return $this|ChildPuzzle The current object (for fluent API support)
     */
    public function removePuzzleMemberRelatedByMemberId(ChildPuzzleMember $puzzleMemberRelatedByMemberId)
    {
        if ($this->getPuzzleMembersRelatedByMemberId()->contains($puzzleMemberRelatedByMemberId)) {
            $pos = $this->collPuzzleMembersRelatedByMemberId->search($puzzleMemberRelatedByMemberId);
            $this->collPuzzleMembersRelatedByMemberId->remove($pos);
            if (null === $this->puzzleMembersRelatedByMemberIdScheduledForDeletion) {
                $this->puzzleMembersRelatedByMemberIdScheduledForDeletion = clone $this->collPuzzleMembersRelatedByMemberId;
                $this->puzzleMembersRelatedByMemberIdScheduledForDeletion->clear();
            }
            $this->puzzleMembersRelatedByMemberIdScheduledForDeletion[]= clone $puzzleMemberRelatedByMemberId;
            $puzzleMemberRelatedByMemberId->setMember(null);
        }

        return $this;
    }

    /**
     * Clears out the collPuzzleParentsRelatedByPuzzleId collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addPuzzleParentsRelatedByPuzzleId()
     */
    public function clearPuzzleParentsRelatedByPuzzleId()
    {
        $this->collPuzzleParentsRelatedByPuzzleId = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collPuzzleParentsRelatedByPuzzleId collection loaded partially.
     */
    public function resetPartialPuzzleParentsRelatedByPuzzleId($v = true)
    {
        $this->collPuzzleParentsRelatedByPuzzleIdPartial = $v;
    }

    /**
     * Initializes the collPuzzleParentsRelatedByPuzzleId collection.
     *
     * By default this just sets the collPuzzleParentsRelatedByPuzzleId collection to an empty array (like clearcollPuzzleParentsRelatedByPuzzleId());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initPuzzleParentsRelatedByPuzzleId($overrideExisting = true)
    {
        if (null !== $this->collPuzzleParentsRelatedByPuzzleId && !$overrideExisting) {
            return;
        }

        $collectionClassName = PuzzleParentTableMap::getTableMap()->getCollectionClassName();

        $this->collPuzzleParentsRelatedByPuzzleId = new $collectionClassName;
        $this->collPuzzleParentsRelatedByPuzzleId->setModel('\PuzzleParent');
    }

    /**
     * Gets an array of ChildPuzzleParent objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildPuzzle is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildPuzzleParent[] List of ChildPuzzleParent objects
     * @throws PropelException
     */
    public function getPuzzleParentsRelatedByPuzzleId(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collPuzzleParentsRelatedByPuzzleIdPartial && !$this->isNew();
        if (null === $this->collPuzzleParentsRelatedByPuzzleId || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collPuzzleParentsRelatedByPuzzleId) {
                // return empty collection
                $this->initPuzzleParentsRelatedByPuzzleId();
            } else {
                $collPuzzleParentsRelatedByPuzzleId = ChildPuzzleParentQuery::create(null, $criteria)
                    ->filterByPuzzle($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collPuzzleParentsRelatedByPuzzleIdPartial && count($collPuzzleParentsRelatedByPuzzleId)) {
                        $this->initPuzzleParentsRelatedByPuzzleId(false);

                        foreach ($collPuzzleParentsRelatedByPuzzleId as $obj) {
                            if (false == $this->collPuzzleParentsRelatedByPuzzleId->contains($obj)) {
                                $this->collPuzzleParentsRelatedByPuzzleId->append($obj);
                            }
                        }

                        $this->collPuzzleParentsRelatedByPuzzleIdPartial = true;
                    }

                    return $collPuzzleParentsRelatedByPuzzleId;
                }

                if ($partial && $this->collPuzzleParentsRelatedByPuzzleId) {
                    foreach ($this->collPuzzleParentsRelatedByPuzzleId as $obj) {
                        if ($obj->isNew()) {
                            $collPuzzleParentsRelatedByPuzzleId[] = $obj;
                        }
                    }
                }

                $this->collPuzzleParentsRelatedByPuzzleId = $collPuzzleParentsRelatedByPuzzleId;
                $this->collPuzzleParentsRelatedByPuzzleIdPartial = false;
            }
        }

        return $this->collPuzzleParentsRelatedByPuzzleId;
    }

    /**
     * Sets a collection of ChildPuzzleParent objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $puzzleParentsRelatedByPuzzleId A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildPuzzle The current object (for fluent API support)
     */
    public function setPuzzleParentsRelatedByPuzzleId(Collection $puzzleParentsRelatedByPuzzleId, ConnectionInterface $con = null)
    {
        /** @var ChildPuzzleParent[] $puzzleParentsRelatedByPuzzleIdToDelete */
        $puzzleParentsRelatedByPuzzleIdToDelete = $this->getPuzzleParentsRelatedByPuzzleId(new Criteria(), $con)->diff($puzzleParentsRelatedByPuzzleId);


        $this->puzzleParentsRelatedByPuzzleIdScheduledForDeletion = $puzzleParentsRelatedByPuzzleIdToDelete;

        foreach ($puzzleParentsRelatedByPuzzleIdToDelete as $puzzleParentRelatedByPuzzleIdRemoved) {
            $puzzleParentRelatedByPuzzleIdRemoved->setPuzzle(null);
        }

        $this->collPuzzleParentsRelatedByPuzzleId = null;
        foreach ($puzzleParentsRelatedByPuzzleId as $puzzleParentRelatedByPuzzleId) {
            $this->addPuzzleParentRelatedByPuzzleId($puzzleParentRelatedByPuzzleId);
        }

        $this->collPuzzleParentsRelatedByPuzzleId = $puzzleParentsRelatedByPuzzleId;
        $this->collPuzzleParentsRelatedByPuzzleIdPartial = false;

        return $this;
    }

    /**
     * Returns the number of related PuzzleParent objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related PuzzleParent objects.
     * @throws PropelException
     */
    public function countPuzzleParentsRelatedByPuzzleId(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collPuzzleParentsRelatedByPuzzleIdPartial && !$this->isNew();
        if (null === $this->collPuzzleParentsRelatedByPuzzleId || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collPuzzleParentsRelatedByPuzzleId) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getPuzzleParentsRelatedByPuzzleId());
            }

            $query = ChildPuzzleParentQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByPuzzle($this)
                ->count($con);
        }

        return count($this->collPuzzleParentsRelatedByPuzzleId);
    }

    /**
     * Method called to associate a ChildPuzzleParent object to this object
     * through the ChildPuzzleParent foreign key attribute.
     *
     * @param  ChildPuzzleParent $l ChildPuzzleParent
     * @return $this|\Puzzle The current object (for fluent API support)
     */
    public function addPuzzleParentRelatedByPuzzleId(ChildPuzzleParent $l)
    {
        if ($this->collPuzzleParentsRelatedByPuzzleId === null) {
            $this->initPuzzleParentsRelatedByPuzzleId();
            $this->collPuzzleParentsRelatedByPuzzleIdPartial = true;
        }

        if (!$this->collPuzzleParentsRelatedByPuzzleId->contains($l)) {
            $this->doAddPuzzleParentRelatedByPuzzleId($l);

            if ($this->puzzleParentsRelatedByPuzzleIdScheduledForDeletion and $this->puzzleParentsRelatedByPuzzleIdScheduledForDeletion->contains($l)) {
                $this->puzzleParentsRelatedByPuzzleIdScheduledForDeletion->remove($this->puzzleParentsRelatedByPuzzleIdScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildPuzzleParent $puzzleParentRelatedByPuzzleId The ChildPuzzleParent object to add.
     */
    protected function doAddPuzzleParentRelatedByPuzzleId(ChildPuzzleParent $puzzleParentRelatedByPuzzleId)
    {
        $this->collPuzzleParentsRelatedByPuzzleId[]= $puzzleParentRelatedByPuzzleId;
        $puzzleParentRelatedByPuzzleId->setPuzzle($this);
    }

    /**
     * @param  ChildPuzzleParent $puzzleParentRelatedByPuzzleId The ChildPuzzleParent object to remove.
     * @return $this|ChildPuzzle The current object (for fluent API support)
     */
    public function removePuzzleParentRelatedByPuzzleId(ChildPuzzleParent $puzzleParentRelatedByPuzzleId)
    {
        if ($this->getPuzzleParentsRelatedByPuzzleId()->contains($puzzleParentRelatedByPuzzleId)) {
            $pos = $this->collPuzzleParentsRelatedByPuzzleId->search($puzzleParentRelatedByPuzzleId);
            $this->collPuzzleParentsRelatedByPuzzleId->remove($pos);
            if (null === $this->puzzleParentsRelatedByPuzzleIdScheduledForDeletion) {
                $this->puzzleParentsRelatedByPuzzleIdScheduledForDeletion = clone $this->collPuzzleParentsRelatedByPuzzleId;
                $this->puzzleParentsRelatedByPuzzleIdScheduledForDeletion->clear();
            }
            $this->puzzleParentsRelatedByPuzzleIdScheduledForDeletion[]= clone $puzzleParentRelatedByPuzzleId;
            $puzzleParentRelatedByPuzzleId->setPuzzle(null);
        }

        return $this;
    }

    /**
     * Clears out the collPuzzleParentsRelatedByParentId collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addPuzzleParentsRelatedByParentId()
     */
    public function clearPuzzleParentsRelatedByParentId()
    {
        $this->collPuzzleParentsRelatedByParentId = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collPuzzleParentsRelatedByParentId collection loaded partially.
     */
    public function resetPartialPuzzleParentsRelatedByParentId($v = true)
    {
        $this->collPuzzleParentsRelatedByParentIdPartial = $v;
    }

    /**
     * Initializes the collPuzzleParentsRelatedByParentId collection.
     *
     * By default this just sets the collPuzzleParentsRelatedByParentId collection to an empty array (like clearcollPuzzleParentsRelatedByParentId());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initPuzzleParentsRelatedByParentId($overrideExisting = true)
    {
        if (null !== $this->collPuzzleParentsRelatedByParentId && !$overrideExisting) {
            return;
        }

        $collectionClassName = PuzzleParentTableMap::getTableMap()->getCollectionClassName();

        $this->collPuzzleParentsRelatedByParentId = new $collectionClassName;
        $this->collPuzzleParentsRelatedByParentId->setModel('\PuzzleParent');
    }

    /**
     * Gets an array of ChildPuzzleParent objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildPuzzle is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildPuzzleParent[] List of ChildPuzzleParent objects
     * @throws PropelException
     */
    public function getPuzzleParentsRelatedByParentId(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collPuzzleParentsRelatedByParentIdPartial && !$this->isNew();
        if (null === $this->collPuzzleParentsRelatedByParentId || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collPuzzleParentsRelatedByParentId) {
                // return empty collection
                $this->initPuzzleParentsRelatedByParentId();
            } else {
                $collPuzzleParentsRelatedByParentId = ChildPuzzleParentQuery::create(null, $criteria)
                    ->filterByParent($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collPuzzleParentsRelatedByParentIdPartial && count($collPuzzleParentsRelatedByParentId)) {
                        $this->initPuzzleParentsRelatedByParentId(false);

                        foreach ($collPuzzleParentsRelatedByParentId as $obj) {
                            if (false == $this->collPuzzleParentsRelatedByParentId->contains($obj)) {
                                $this->collPuzzleParentsRelatedByParentId->append($obj);
                            }
                        }

                        $this->collPuzzleParentsRelatedByParentIdPartial = true;
                    }

                    return $collPuzzleParentsRelatedByParentId;
                }

                if ($partial && $this->collPuzzleParentsRelatedByParentId) {
                    foreach ($this->collPuzzleParentsRelatedByParentId as $obj) {
                        if ($obj->isNew()) {
                            $collPuzzleParentsRelatedByParentId[] = $obj;
                        }
                    }
                }

                $this->collPuzzleParentsRelatedByParentId = $collPuzzleParentsRelatedByParentId;
                $this->collPuzzleParentsRelatedByParentIdPartial = false;
            }
        }

        return $this->collPuzzleParentsRelatedByParentId;
    }

    /**
     * Sets a collection of ChildPuzzleParent objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $puzzleParentsRelatedByParentId A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildPuzzle The current object (for fluent API support)
     */
    public function setPuzzleParentsRelatedByParentId(Collection $puzzleParentsRelatedByParentId, ConnectionInterface $con = null)
    {
        /** @var ChildPuzzleParent[] $puzzleParentsRelatedByParentIdToDelete */
        $puzzleParentsRelatedByParentIdToDelete = $this->getPuzzleParentsRelatedByParentId(new Criteria(), $con)->diff($puzzleParentsRelatedByParentId);


        $this->puzzleParentsRelatedByParentIdScheduledForDeletion = $puzzleParentsRelatedByParentIdToDelete;

        foreach ($puzzleParentsRelatedByParentIdToDelete as $puzzleParentRelatedByParentIdRemoved) {
            $puzzleParentRelatedByParentIdRemoved->setParent(null);
        }

        $this->collPuzzleParentsRelatedByParentId = null;
        foreach ($puzzleParentsRelatedByParentId as $puzzleParentRelatedByParentId) {
            $this->addPuzzleParentRelatedByParentId($puzzleParentRelatedByParentId);
        }

        $this->collPuzzleParentsRelatedByParentId = $puzzleParentsRelatedByParentId;
        $this->collPuzzleParentsRelatedByParentIdPartial = false;

        return $this;
    }

    /**
     * Returns the number of related PuzzleParent objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related PuzzleParent objects.
     * @throws PropelException
     */
    public function countPuzzleParentsRelatedByParentId(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collPuzzleParentsRelatedByParentIdPartial && !$this->isNew();
        if (null === $this->collPuzzleParentsRelatedByParentId || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collPuzzleParentsRelatedByParentId) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getPuzzleParentsRelatedByParentId());
            }

            $query = ChildPuzzleParentQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByParent($this)
                ->count($con);
        }

        return count($this->collPuzzleParentsRelatedByParentId);
    }

    /**
     * Method called to associate a ChildPuzzleParent object to this object
     * through the ChildPuzzleParent foreign key attribute.
     *
     * @param  ChildPuzzleParent $l ChildPuzzleParent
     * @return $this|\Puzzle The current object (for fluent API support)
     */
    public function addPuzzleParentRelatedByParentId(ChildPuzzleParent $l)
    {
        if ($this->collPuzzleParentsRelatedByParentId === null) {
            $this->initPuzzleParentsRelatedByParentId();
            $this->collPuzzleParentsRelatedByParentIdPartial = true;
        }

        if (!$this->collPuzzleParentsRelatedByParentId->contains($l)) {
            $this->doAddPuzzleParentRelatedByParentId($l);

            if ($this->puzzleParentsRelatedByParentIdScheduledForDeletion and $this->puzzleParentsRelatedByParentIdScheduledForDeletion->contains($l)) {
                $this->puzzleParentsRelatedByParentIdScheduledForDeletion->remove($this->puzzleParentsRelatedByParentIdScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildPuzzleParent $puzzleParentRelatedByParentId The ChildPuzzleParent object to add.
     */
    protected function doAddPuzzleParentRelatedByParentId(ChildPuzzleParent $puzzleParentRelatedByParentId)
    {
        $this->collPuzzleParentsRelatedByParentId[]= $puzzleParentRelatedByParentId;
        $puzzleParentRelatedByParentId->setParent($this);
    }

    /**
     * @param  ChildPuzzleParent $puzzleParentRelatedByParentId The ChildPuzzleParent object to remove.
     * @return $this|ChildPuzzle The current object (for fluent API support)
     */
    public function removePuzzleParentRelatedByParentId(ChildPuzzleParent $puzzleParentRelatedByParentId)
    {
        if ($this->getPuzzleParentsRelatedByParentId()->contains($puzzleParentRelatedByParentId)) {
            $pos = $this->collPuzzleParentsRelatedByParentId->search($puzzleParentRelatedByParentId);
            $this->collPuzzleParentsRelatedByParentId->remove($pos);
            if (null === $this->puzzleParentsRelatedByParentIdScheduledForDeletion) {
                $this->puzzleParentsRelatedByParentIdScheduledForDeletion = clone $this->collPuzzleParentsRelatedByParentId;
                $this->puzzleParentsRelatedByParentIdScheduledForDeletion->clear();
            }
            $this->puzzleParentsRelatedByParentIdScheduledForDeletion[]= clone $puzzleParentRelatedByParentId;
            $puzzleParentRelatedByParentId->setParent(null);
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
        $this->url = null;
        $this->spreadsheet_id = null;
        $this->solution = null;
        $this->status = null;
        $this->slack_channel = null;
        $this->slack_channel_id = null;
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
            if ($this->collNotes) {
                foreach ($this->collNotes as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collPuzzleMembersRelatedByPuzzleId) {
                foreach ($this->collPuzzleMembersRelatedByPuzzleId as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collPuzzleMembersRelatedByMemberId) {
                foreach ($this->collPuzzleMembersRelatedByMemberId as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collPuzzleParentsRelatedByPuzzleId) {
                foreach ($this->collPuzzleParentsRelatedByPuzzleId as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collPuzzleParentsRelatedByParentId) {
                foreach ($this->collPuzzleParentsRelatedByParentId as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        $this->collNotes = null;
        $this->collPuzzleMembersRelatedByPuzzleId = null;
        $this->collPuzzleMembersRelatedByMemberId = null;
        $this->collPuzzleParentsRelatedByPuzzleId = null;
        $this->collPuzzleParentsRelatedByParentId = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(PuzzleTableMap::DEFAULT_STRING_FORMAT);
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
