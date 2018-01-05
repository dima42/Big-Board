<?php

namespace Base;

use \Member as ChildMember;
use \MemberQuery as ChildMemberQuery;
use \News as ChildNews;
use \NewsQuery as ChildNewsQuery;
use \Note as ChildNote;
use \NoteQuery as ChildNoteQuery;
use \Puzzle as ChildPuzzle;
use \PuzzleMember as ChildPuzzleMember;
use \PuzzleMemberQuery as ChildPuzzleMemberQuery;
use \PuzzleQuery as ChildPuzzleQuery;
use \Exception;
use \PDO;
use Map\MemberTableMap;
use Map\NewsTableMap;
use Map\NoteTableMap;
use Map\PuzzleMemberTableMap;
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
 * Base class that represents a row from the 'member' table.
 *
 *
 *
 * @package    propel.generator..Base
 */
abstract class Member implements ActiveRecordInterface
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Map\\MemberTableMap';


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
     * The value for the full_name field.
     *
     * @var        string
     */
    protected $full_name;

    /**
     * The value for the google_id field.
     *
     * @var        string
     */
    protected $google_id;

    /**
     * The value for the google_refresh field.
     *
     * @var        string
     */
    protected $google_refresh;

    /**
     * The value for the slack_id field.
     *
     * @var        string
     */
    protected $slack_id;

    /**
     * The value for the slack_handle field.
     *
     * @var        string
     */
    protected $slack_handle;

    /**
     * The value for the strengths field.
     *
     * @var        string
     */
    protected $strengths;

    /**
     * The value for the avatar field.
     *
     * @var        string
     */
    protected $avatar;

    /**
     * The value for the phone_number field.
     *
     * @var        string
     */
    protected $phone_number;

    /**
     * The value for the location field.
     *
     * @var        string
     */
    protected $location;

    /**
     * @var        ObjectCollection|ChildPuzzle[] Collection to store aggregation of ChildPuzzle objects.
     */
    protected $collWrangledPuzzles;
    protected $collWrangledPuzzlesPartial;

    /**
     * @var        ObjectCollection|ChildNote[] Collection to store aggregation of ChildNote objects.
     */
    protected $collNotes;
    protected $collNotesPartial;

    /**
     * @var        ObjectCollection|ChildPuzzleMember[] Collection to store aggregation of ChildPuzzleMember objects.
     */
    protected $collPuzzleMembers;
    protected $collPuzzleMembersPartial;

    /**
     * @var        ObjectCollection|ChildNews[] Collection to store aggregation of ChildNews objects.
     */
    protected $collNews;
    protected $collNewsPartial;

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

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildPuzzle[]
     */
    protected $puzzlesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildPuzzle[]
     */
    protected $wrangledPuzzlesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildNote[]
     */
    protected $notesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildPuzzleMember[]
     */
    protected $puzzleMembersScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildNews[]
     */
    protected $newsScheduledForDeletion = null;

    /**
     * Initializes internal state of Base\Member object.
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
     * Compares this with another <code>Member</code> instance.  If
     * <code>obj</code> is an instance of <code>Member</code>, delegates to
     * <code>equals(Member)</code>.  Otherwise, returns <code>false</code>.
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
     * @return $this|Member The current object, for fluid interface
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
     * Get the [full_name] column value.
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->full_name;
    }

    /**
     * Get the [google_id] column value.
     *
     * @return string
     */
    public function getGoogleId()
    {
        return $this->google_id;
    }

    /**
     * Get the [google_refresh] column value.
     *
     * @return string
     */
    public function getGoogleRefresh()
    {
        return $this->google_refresh;
    }

    /**
     * Get the [slack_id] column value.
     *
     * @return string
     */
    public function getSlackId()
    {
        return $this->slack_id;
    }

    /**
     * Get the [slack_handle] column value.
     *
     * @return string
     */
    public function getSlackHandle()
    {
        return $this->slack_handle;
    }

    /**
     * Get the [strengths] column value.
     *
     * @return string
     */
    public function getStrengths()
    {
        return $this->strengths;
    }

    /**
     * Get the [avatar] column value.
     *
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Get the [phone_number] column value.
     *
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phone_number;
    }

    /**
     * Get the [location] column value.
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set the value of [id] column.
     *
     * @param int $v new value
     * @return $this|\Member The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[MemberTableMap::COL_ID] = true;
        }

        return $this;
    } // setId()

    /**
     * Set the value of [full_name] column.
     *
     * @param string $v new value
     * @return $this|\Member The current object (for fluent API support)
     */
    public function setFullName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->full_name !== $v) {
            $this->full_name = $v;
            $this->modifiedColumns[MemberTableMap::COL_FULL_NAME] = true;
        }

        return $this;
    } // setFullName()

    /**
     * Set the value of [google_id] column.
     *
     * @param string $v new value
     * @return $this|\Member The current object (for fluent API support)
     */
    public function setGoogleId($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->google_id !== $v) {
            $this->google_id = $v;
            $this->modifiedColumns[MemberTableMap::COL_GOOGLE_ID] = true;
        }

        return $this;
    } // setGoogleId()

    /**
     * Set the value of [google_refresh] column.
     *
     * @param string $v new value
     * @return $this|\Member The current object (for fluent API support)
     */
    public function setGoogleRefresh($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->google_refresh !== $v) {
            $this->google_refresh = $v;
            $this->modifiedColumns[MemberTableMap::COL_GOOGLE_REFRESH] = true;
        }

        return $this;
    } // setGoogleRefresh()

    /**
     * Set the value of [slack_id] column.
     *
     * @param string $v new value
     * @return $this|\Member The current object (for fluent API support)
     */
    public function setSlackId($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->slack_id !== $v) {
            $this->slack_id = $v;
            $this->modifiedColumns[MemberTableMap::COL_SLACK_ID] = true;
        }

        return $this;
    } // setSlackId()

    /**
     * Set the value of [slack_handle] column.
     *
     * @param string $v new value
     * @return $this|\Member The current object (for fluent API support)
     */
    public function setSlackHandle($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->slack_handle !== $v) {
            $this->slack_handle = $v;
            $this->modifiedColumns[MemberTableMap::COL_SLACK_HANDLE] = true;
        }

        return $this;
    } // setSlackHandle()

    /**
     * Set the value of [strengths] column.
     *
     * @param string $v new value
     * @return $this|\Member The current object (for fluent API support)
     */
    public function setStrengths($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->strengths !== $v) {
            $this->strengths = $v;
            $this->modifiedColumns[MemberTableMap::COL_STRENGTHS] = true;
        }

        return $this;
    } // setStrengths()

    /**
     * Set the value of [avatar] column.
     *
     * @param string $v new value
     * @return $this|\Member The current object (for fluent API support)
     */
    public function setAvatar($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->avatar !== $v) {
            $this->avatar = $v;
            $this->modifiedColumns[MemberTableMap::COL_AVATAR] = true;
        }

        return $this;
    } // setAvatar()

    /**
     * Set the value of [phone_number] column.
     *
     * @param string $v new value
     * @return $this|\Member The current object (for fluent API support)
     */
    public function setPhoneNumber($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->phone_number !== $v) {
            $this->phone_number = $v;
            $this->modifiedColumns[MemberTableMap::COL_PHONE_NUMBER] = true;
        }

        return $this;
    } // setPhoneNumber()

    /**
     * Set the value of [location] column.
     *
     * @param string $v new value
     * @return $this|\Member The current object (for fluent API support)
     */
    public function setLocation($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->location !== $v) {
            $this->location = $v;
            $this->modifiedColumns[MemberTableMap::COL_LOCATION] = true;
        }

        return $this;
    } // setLocation()

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

            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : MemberTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : MemberTableMap::translateFieldName('FullName', TableMap::TYPE_PHPNAME, $indexType)];
            $this->full_name = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : MemberTableMap::translateFieldName('GoogleId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->google_id = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : MemberTableMap::translateFieldName('GoogleRefresh', TableMap::TYPE_PHPNAME, $indexType)];
            $this->google_refresh = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : MemberTableMap::translateFieldName('SlackId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->slack_id = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : MemberTableMap::translateFieldName('SlackHandle', TableMap::TYPE_PHPNAME, $indexType)];
            $this->slack_handle = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : MemberTableMap::translateFieldName('Strengths', TableMap::TYPE_PHPNAME, $indexType)];
            $this->strengths = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 7 + $startcol : MemberTableMap::translateFieldName('Avatar', TableMap::TYPE_PHPNAME, $indexType)];
            $this->avatar = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 8 + $startcol : MemberTableMap::translateFieldName('PhoneNumber', TableMap::TYPE_PHPNAME, $indexType)];
            $this->phone_number = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 9 + $startcol : MemberTableMap::translateFieldName('Location', TableMap::TYPE_PHPNAME, $indexType)];
            $this->location = (null !== $col) ? (string) $col : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 10; // 10 = MemberTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException(sprintf('Error populating %s object', '\\Member'), 0, $e);
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
            $con = Propel::getServiceContainer()->getReadConnection(MemberTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildMemberQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collWrangledPuzzles = null;

            $this->collNotes = null;

            $this->collPuzzleMembers = null;

            $this->collNews = null;

            $this->collPuzzles = null;
        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see Member::setDeleted()
     * @see Member::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(MemberTableMap::DATABASE_NAME);
        }

        $con->transaction(function () use ($con) {
            $deleteQuery = ChildMemberQuery::create()
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
            $con = Propel::getServiceContainer()->getWriteConnection(MemberTableMap::DATABASE_NAME);
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
                MemberTableMap::addInstanceToPool($this);
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

                    \PuzzleMemberQuery::create()
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


            if ($this->wrangledPuzzlesScheduledForDeletion !== null) {
                if (!$this->wrangledPuzzlesScheduledForDeletion->isEmpty()) {
                    foreach ($this->wrangledPuzzlesScheduledForDeletion as $wrangledPuzzle) {
                        // need to save related object because we set the relation to null
                        $wrangledPuzzle->save($con);
                    }
                    $this->wrangledPuzzlesScheduledForDeletion = null;
                }
            }

            if ($this->collWrangledPuzzles !== null) {
                foreach ($this->collWrangledPuzzles as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->notesScheduledForDeletion !== null) {
                if (!$this->notesScheduledForDeletion->isEmpty()) {
                    foreach ($this->notesScheduledForDeletion as $note) {
                        // need to save related object because we set the relation to null
                        $note->save($con);
                    }
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

            if ($this->puzzleMembersScheduledForDeletion !== null) {
                if (!$this->puzzleMembersScheduledForDeletion->isEmpty()) {
                    \PuzzleMemberQuery::create()
                        ->filterByPrimaryKeys($this->puzzleMembersScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->puzzleMembersScheduledForDeletion = null;
                }
            }

            if ($this->collPuzzleMembers !== null) {
                foreach ($this->collPuzzleMembers as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->newsScheduledForDeletion !== null) {
                if (!$this->newsScheduledForDeletion->isEmpty()) {
                    foreach ($this->newsScheduledForDeletion as $news) {
                        // need to save related object because we set the relation to null
                        $news->save($con);
                    }
                    $this->newsScheduledForDeletion = null;
                }
            }

            if ($this->collNews !== null) {
                foreach ($this->collNews as $referrerFK) {
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

        $this->modifiedColumns[MemberTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . MemberTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(MemberTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'id';
        }
        if ($this->isColumnModified(MemberTableMap::COL_FULL_NAME)) {
            $modifiedColumns[':p' . $index++]  = 'full_name';
        }
        if ($this->isColumnModified(MemberTableMap::COL_GOOGLE_ID)) {
            $modifiedColumns[':p' . $index++]  = 'google_id';
        }
        if ($this->isColumnModified(MemberTableMap::COL_GOOGLE_REFRESH)) {
            $modifiedColumns[':p' . $index++]  = 'google_refresh';
        }
        if ($this->isColumnModified(MemberTableMap::COL_SLACK_ID)) {
            $modifiedColumns[':p' . $index++]  = 'slack_id';
        }
        if ($this->isColumnModified(MemberTableMap::COL_SLACK_HANDLE)) {
            $modifiedColumns[':p' . $index++]  = 'slack_handle';
        }
        if ($this->isColumnModified(MemberTableMap::COL_STRENGTHS)) {
            $modifiedColumns[':p' . $index++]  = 'strengths';
        }
        if ($this->isColumnModified(MemberTableMap::COL_AVATAR)) {
            $modifiedColumns[':p' . $index++]  = 'avatar';
        }
        if ($this->isColumnModified(MemberTableMap::COL_PHONE_NUMBER)) {
            $modifiedColumns[':p' . $index++]  = 'phone_number';
        }
        if ($this->isColumnModified(MemberTableMap::COL_LOCATION)) {
            $modifiedColumns[':p' . $index++]  = 'location';
        }

        $sql = sprintf(
            'INSERT INTO member (%s) VALUES (%s)',
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
                    case 'full_name':
                        $stmt->bindValue($identifier, $this->full_name, PDO::PARAM_STR);
                        break;
                    case 'google_id':
                        $stmt->bindValue($identifier, $this->google_id, PDO::PARAM_STR);
                        break;
                    case 'google_refresh':
                        $stmt->bindValue($identifier, $this->google_refresh, PDO::PARAM_STR);
                        break;
                    case 'slack_id':
                        $stmt->bindValue($identifier, $this->slack_id, PDO::PARAM_STR);
                        break;
                    case 'slack_handle':
                        $stmt->bindValue($identifier, $this->slack_handle, PDO::PARAM_STR);
                        break;
                    case 'strengths':
                        $stmt->bindValue($identifier, $this->strengths, PDO::PARAM_STR);
                        break;
                    case 'avatar':
                        $stmt->bindValue($identifier, $this->avatar, PDO::PARAM_STR);
                        break;
                    case 'phone_number':
                        $stmt->bindValue($identifier, $this->phone_number, PDO::PARAM_STR);
                        break;
                    case 'location':
                        $stmt->bindValue($identifier, $this->location, PDO::PARAM_STR);
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
        $pos = MemberTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
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
                return $this->getFullName();
                break;
            case 2:
                return $this->getGoogleId();
                break;
            case 3:
                return $this->getGoogleRefresh();
                break;
            case 4:
                return $this->getSlackId();
                break;
            case 5:
                return $this->getSlackHandle();
                break;
            case 6:
                return $this->getStrengths();
                break;
            case 7:
                return $this->getAvatar();
                break;
            case 8:
                return $this->getPhoneNumber();
                break;
            case 9:
                return $this->getLocation();
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

        if (isset($alreadyDumpedObjects['Member'][$this->hashCode()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Member'][$this->hashCode()] = true;
        $keys = MemberTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getFullName(),
            $keys[2] => $this->getGoogleId(),
            $keys[3] => $this->getGoogleRefresh(),
            $keys[4] => $this->getSlackId(),
            $keys[5] => $this->getSlackHandle(),
            $keys[6] => $this->getStrengths(),
            $keys[7] => $this->getAvatar(),
            $keys[8] => $this->getPhoneNumber(),
            $keys[9] => $this->getLocation(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->collWrangledPuzzles) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'puzzles';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'puzzles';
                        break;
                    default:
                        $key = 'WrangledPuzzles';
                }

                $result[$key] = $this->collWrangledPuzzles->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
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
            if (null !== $this->collPuzzleMembers) {

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

                $result[$key] = $this->collPuzzleMembers->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collNews) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'news';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'news';
                        break;
                    default:
                        $key = 'News';
                }

                $result[$key] = $this->collNews->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
     * @return $this|\Member
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = MemberTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param  int $pos position in xml schema
     * @param  mixed $value field value
     * @return $this|\Member
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setFullName($value);
                break;
            case 2:
                $this->setGoogleId($value);
                break;
            case 3:
                $this->setGoogleRefresh($value);
                break;
            case 4:
                $this->setSlackId($value);
                break;
            case 5:
                $this->setSlackHandle($value);
                break;
            case 6:
                $this->setStrengths($value);
                break;
            case 7:
                $this->setAvatar($value);
                break;
            case 8:
                $this->setPhoneNumber($value);
                break;
            case 9:
                $this->setLocation($value);
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
        $keys = MemberTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setId($arr[$keys[0]]);
        }
        if (array_key_exists($keys[1], $arr)) {
            $this->setFullName($arr[$keys[1]]);
        }
        if (array_key_exists($keys[2], $arr)) {
            $this->setGoogleId($arr[$keys[2]]);
        }
        if (array_key_exists($keys[3], $arr)) {
            $this->setGoogleRefresh($arr[$keys[3]]);
        }
        if (array_key_exists($keys[4], $arr)) {
            $this->setSlackId($arr[$keys[4]]);
        }
        if (array_key_exists($keys[5], $arr)) {
            $this->setSlackHandle($arr[$keys[5]]);
        }
        if (array_key_exists($keys[6], $arr)) {
            $this->setStrengths($arr[$keys[6]]);
        }
        if (array_key_exists($keys[7], $arr)) {
            $this->setAvatar($arr[$keys[7]]);
        }
        if (array_key_exists($keys[8], $arr)) {
            $this->setPhoneNumber($arr[$keys[8]]);
        }
        if (array_key_exists($keys[9], $arr)) {
            $this->setLocation($arr[$keys[9]]);
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
     * @return $this|\Member The current object, for fluid interface
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
        $criteria = new Criteria(MemberTableMap::DATABASE_NAME);

        if ($this->isColumnModified(MemberTableMap::COL_ID)) {
            $criteria->add(MemberTableMap::COL_ID, $this->id);
        }
        if ($this->isColumnModified(MemberTableMap::COL_FULL_NAME)) {
            $criteria->add(MemberTableMap::COL_FULL_NAME, $this->full_name);
        }
        if ($this->isColumnModified(MemberTableMap::COL_GOOGLE_ID)) {
            $criteria->add(MemberTableMap::COL_GOOGLE_ID, $this->google_id);
        }
        if ($this->isColumnModified(MemberTableMap::COL_GOOGLE_REFRESH)) {
            $criteria->add(MemberTableMap::COL_GOOGLE_REFRESH, $this->google_refresh);
        }
        if ($this->isColumnModified(MemberTableMap::COL_SLACK_ID)) {
            $criteria->add(MemberTableMap::COL_SLACK_ID, $this->slack_id);
        }
        if ($this->isColumnModified(MemberTableMap::COL_SLACK_HANDLE)) {
            $criteria->add(MemberTableMap::COL_SLACK_HANDLE, $this->slack_handle);
        }
        if ($this->isColumnModified(MemberTableMap::COL_STRENGTHS)) {
            $criteria->add(MemberTableMap::COL_STRENGTHS, $this->strengths);
        }
        if ($this->isColumnModified(MemberTableMap::COL_AVATAR)) {
            $criteria->add(MemberTableMap::COL_AVATAR, $this->avatar);
        }
        if ($this->isColumnModified(MemberTableMap::COL_PHONE_NUMBER)) {
            $criteria->add(MemberTableMap::COL_PHONE_NUMBER, $this->phone_number);
        }
        if ($this->isColumnModified(MemberTableMap::COL_LOCATION)) {
            $criteria->add(MemberTableMap::COL_LOCATION, $this->location);
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
        $criteria = ChildMemberQuery::create();
        $criteria->add(MemberTableMap::COL_ID, $this->id);

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
     * @param      object $copyObj An object of \Member (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setFullName($this->getFullName());
        $copyObj->setGoogleId($this->getGoogleId());
        $copyObj->setGoogleRefresh($this->getGoogleRefresh());
        $copyObj->setSlackId($this->getSlackId());
        $copyObj->setSlackHandle($this->getSlackHandle());
        $copyObj->setStrengths($this->getStrengths());
        $copyObj->setAvatar($this->getAvatar());
        $copyObj->setPhoneNumber($this->getPhoneNumber());
        $copyObj->setLocation($this->getLocation());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getWrangledPuzzles() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addWrangledPuzzle($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getNotes() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addNote($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getPuzzleMembers() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addPuzzleMember($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getNews() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addNews($relObj->copy($deepCopy));
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
     * @return \Member Clone of current object.
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
        if ('WrangledPuzzle' == $relationName) {
            $this->initWrangledPuzzles();
            return;
        }
        if ('Note' == $relationName) {
            $this->initNotes();
            return;
        }
        if ('PuzzleMember' == $relationName) {
            $this->initPuzzleMembers();
            return;
        }
        if ('News' == $relationName) {
            $this->initNews();
            return;
        }
    }

    /**
     * Clears out the collWrangledPuzzles collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addWrangledPuzzles()
     */
    public function clearWrangledPuzzles()
    {
        $this->collWrangledPuzzles = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collWrangledPuzzles collection loaded partially.
     */
    public function resetPartialWrangledPuzzles($v = true)
    {
        $this->collWrangledPuzzlesPartial = $v;
    }

    /**
     * Initializes the collWrangledPuzzles collection.
     *
     * By default this just sets the collWrangledPuzzles collection to an empty array (like clearcollWrangledPuzzles());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initWrangledPuzzles($overrideExisting = true)
    {
        if (null !== $this->collWrangledPuzzles && !$overrideExisting) {
            return;
        }

        $collectionClassName = PuzzleTableMap::getTableMap()->getCollectionClassName();

        $this->collWrangledPuzzles = new $collectionClassName;
        $this->collWrangledPuzzles->setModel('\Puzzle');
    }

    /**
     * Gets an array of ChildPuzzle objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildMember is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildPuzzle[] List of ChildPuzzle objects
     * @throws PropelException
     */
    public function getWrangledPuzzles(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collWrangledPuzzlesPartial && !$this->isNew();
        if (null === $this->collWrangledPuzzles || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collWrangledPuzzles) {
                // return empty collection
                $this->initWrangledPuzzles();
            } else {
                $collWrangledPuzzles = ChildPuzzleQuery::create(null, $criteria)
                    ->filterByWrangler($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collWrangledPuzzlesPartial && count($collWrangledPuzzles)) {
                        $this->initWrangledPuzzles(false);

                        foreach ($collWrangledPuzzles as $obj) {
                            if (false == $this->collWrangledPuzzles->contains($obj)) {
                                $this->collWrangledPuzzles->append($obj);
                            }
                        }

                        $this->collWrangledPuzzlesPartial = true;
                    }

                    return $collWrangledPuzzles;
                }

                if ($partial && $this->collWrangledPuzzles) {
                    foreach ($this->collWrangledPuzzles as $obj) {
                        if ($obj->isNew()) {
                            $collWrangledPuzzles[] = $obj;
                        }
                    }
                }

                $this->collWrangledPuzzles = $collWrangledPuzzles;
                $this->collWrangledPuzzlesPartial = false;
            }
        }

        return $this->collWrangledPuzzles;
    }

    /**
     * Sets a collection of ChildPuzzle objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $wrangledPuzzles A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildMember The current object (for fluent API support)
     */
    public function setWrangledPuzzles(Collection $wrangledPuzzles, ConnectionInterface $con = null)
    {
        /** @var ChildPuzzle[] $wrangledPuzzlesToDelete */
        $wrangledPuzzlesToDelete = $this->getWrangledPuzzles(new Criteria(), $con)->diff($wrangledPuzzles);


        $this->wrangledPuzzlesScheduledForDeletion = $wrangledPuzzlesToDelete;

        foreach ($wrangledPuzzlesToDelete as $wrangledPuzzleRemoved) {
            $wrangledPuzzleRemoved->setWrangler(null);
        }

        $this->collWrangledPuzzles = null;
        foreach ($wrangledPuzzles as $wrangledPuzzle) {
            $this->addWrangledPuzzle($wrangledPuzzle);
        }

        $this->collWrangledPuzzles = $wrangledPuzzles;
        $this->collWrangledPuzzlesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Puzzle objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Puzzle objects.
     * @throws PropelException
     */
    public function countWrangledPuzzles(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collWrangledPuzzlesPartial && !$this->isNew();
        if (null === $this->collWrangledPuzzles || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collWrangledPuzzles) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getWrangledPuzzles());
            }

            $query = ChildPuzzleQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByWrangler($this)
                ->count($con);
        }

        return count($this->collWrangledPuzzles);
    }

    /**
     * Method called to associate a ChildPuzzle object to this object
     * through the ChildPuzzle foreign key attribute.
     *
     * @param  ChildPuzzle $l ChildPuzzle
     * @return $this|\Member The current object (for fluent API support)
     */
    public function addWrangledPuzzle(ChildPuzzle $l)
    {
        if ($this->collWrangledPuzzles === null) {
            $this->initWrangledPuzzles();
            $this->collWrangledPuzzlesPartial = true;
        }

        if (!$this->collWrangledPuzzles->contains($l)) {
            $this->doAddWrangledPuzzle($l);

            if ($this->wrangledPuzzlesScheduledForDeletion and $this->wrangledPuzzlesScheduledForDeletion->contains($l)) {
                $this->wrangledPuzzlesScheduledForDeletion->remove($this->wrangledPuzzlesScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildPuzzle $wrangledPuzzle The ChildPuzzle object to add.
     */
    protected function doAddWrangledPuzzle(ChildPuzzle $wrangledPuzzle)
    {
        $this->collWrangledPuzzles[]= $wrangledPuzzle;
        $wrangledPuzzle->setWrangler($this);
    }

    /**
     * @param  ChildPuzzle $wrangledPuzzle The ChildPuzzle object to remove.
     * @return $this|ChildMember The current object (for fluent API support)
     */
    public function removeWrangledPuzzle(ChildPuzzle $wrangledPuzzle)
    {
        if ($this->getWrangledPuzzles()->contains($wrangledPuzzle)) {
            $pos = $this->collWrangledPuzzles->search($wrangledPuzzle);
            $this->collWrangledPuzzles->remove($pos);
            if (null === $this->wrangledPuzzlesScheduledForDeletion) {
                $this->wrangledPuzzlesScheduledForDeletion = clone $this->collWrangledPuzzles;
                $this->wrangledPuzzlesScheduledForDeletion->clear();
            }
            $this->wrangledPuzzlesScheduledForDeletion[]= $wrangledPuzzle;
            $wrangledPuzzle->setWrangler(null);
        }

        return $this;
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
     * If this ChildMember is new, it will return
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
                    ->filterByAuthor($this)
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
     * @return $this|ChildMember The current object (for fluent API support)
     */
    public function setNotes(Collection $notes, ConnectionInterface $con = null)
    {
        /** @var ChildNote[] $notesToDelete */
        $notesToDelete = $this->getNotes(new Criteria(), $con)->diff($notes);


        $this->notesScheduledForDeletion = $notesToDelete;

        foreach ($notesToDelete as $noteRemoved) {
            $noteRemoved->setAuthor(null);
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
                ->filterByAuthor($this)
                ->count($con);
        }

        return count($this->collNotes);
    }

    /**
     * Method called to associate a ChildNote object to this object
     * through the ChildNote foreign key attribute.
     *
     * @param  ChildNote $l ChildNote
     * @return $this|\Member The current object (for fluent API support)
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
        $note->setAuthor($this);
    }

    /**
     * @param  ChildNote $note The ChildNote object to remove.
     * @return $this|ChildMember The current object (for fluent API support)
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
            $this->notesScheduledForDeletion[]= $note;
            $note->setAuthor(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Member is new, it will return
     * an empty collection; or if this Member has previously
     * been saved, it will retrieve related Notes from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Member.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildNote[] List of ChildNote objects
     */
    public function getNotesJoinPuzzle(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildNoteQuery::create(null, $criteria);
        $query->joinWith('Puzzle', $joinBehavior);

        return $this->getNotes($query, $con);
    }

    /**
     * Clears out the collPuzzleMembers collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addPuzzleMembers()
     */
    public function clearPuzzleMembers()
    {
        $this->collPuzzleMembers = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collPuzzleMembers collection loaded partially.
     */
    public function resetPartialPuzzleMembers($v = true)
    {
        $this->collPuzzleMembersPartial = $v;
    }

    /**
     * Initializes the collPuzzleMembers collection.
     *
     * By default this just sets the collPuzzleMembers collection to an empty array (like clearcollPuzzleMembers());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initPuzzleMembers($overrideExisting = true)
    {
        if (null !== $this->collPuzzleMembers && !$overrideExisting) {
            return;
        }

        $collectionClassName = PuzzleMemberTableMap::getTableMap()->getCollectionClassName();

        $this->collPuzzleMembers = new $collectionClassName;
        $this->collPuzzleMembers->setModel('\PuzzleMember');
    }

    /**
     * Gets an array of ChildPuzzleMember objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildMember is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildPuzzleMember[] List of ChildPuzzleMember objects
     * @throws PropelException
     */
    public function getPuzzleMembers(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collPuzzleMembersPartial && !$this->isNew();
        if (null === $this->collPuzzleMembers || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collPuzzleMembers) {
                // return empty collection
                $this->initPuzzleMembers();
            } else {
                $collPuzzleMembers = ChildPuzzleMemberQuery::create(null, $criteria)
                    ->filterByMember($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collPuzzleMembersPartial && count($collPuzzleMembers)) {
                        $this->initPuzzleMembers(false);

                        foreach ($collPuzzleMembers as $obj) {
                            if (false == $this->collPuzzleMembers->contains($obj)) {
                                $this->collPuzzleMembers->append($obj);
                            }
                        }

                        $this->collPuzzleMembersPartial = true;
                    }

                    return $collPuzzleMembers;
                }

                if ($partial && $this->collPuzzleMembers) {
                    foreach ($this->collPuzzleMembers as $obj) {
                        if ($obj->isNew()) {
                            $collPuzzleMembers[] = $obj;
                        }
                    }
                }

                $this->collPuzzleMembers = $collPuzzleMembers;
                $this->collPuzzleMembersPartial = false;
            }
        }

        return $this->collPuzzleMembers;
    }

    /**
     * Sets a collection of ChildPuzzleMember objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $puzzleMembers A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildMember The current object (for fluent API support)
     */
    public function setPuzzleMembers(Collection $puzzleMembers, ConnectionInterface $con = null)
    {
        /** @var ChildPuzzleMember[] $puzzleMembersToDelete */
        $puzzleMembersToDelete = $this->getPuzzleMembers(new Criteria(), $con)->diff($puzzleMembers);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->puzzleMembersScheduledForDeletion = clone $puzzleMembersToDelete;

        foreach ($puzzleMembersToDelete as $puzzleMemberRemoved) {
            $puzzleMemberRemoved->setMember(null);
        }

        $this->collPuzzleMembers = null;
        foreach ($puzzleMembers as $puzzleMember) {
            $this->addPuzzleMember($puzzleMember);
        }

        $this->collPuzzleMembers = $puzzleMembers;
        $this->collPuzzleMembersPartial = false;

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
    public function countPuzzleMembers(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collPuzzleMembersPartial && !$this->isNew();
        if (null === $this->collPuzzleMembers || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collPuzzleMembers) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getPuzzleMembers());
            }

            $query = ChildPuzzleMemberQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByMember($this)
                ->count($con);
        }

        return count($this->collPuzzleMembers);
    }

    /**
     * Method called to associate a ChildPuzzleMember object to this object
     * through the ChildPuzzleMember foreign key attribute.
     *
     * @param  ChildPuzzleMember $l ChildPuzzleMember
     * @return $this|\Member The current object (for fluent API support)
     */
    public function addPuzzleMember(ChildPuzzleMember $l)
    {
        if ($this->collPuzzleMembers === null) {
            $this->initPuzzleMembers();
            $this->collPuzzleMembersPartial = true;
        }

        if (!$this->collPuzzleMembers->contains($l)) {
            $this->doAddPuzzleMember($l);

            if ($this->puzzleMembersScheduledForDeletion and $this->puzzleMembersScheduledForDeletion->contains($l)) {
                $this->puzzleMembersScheduledForDeletion->remove($this->puzzleMembersScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildPuzzleMember $puzzleMember The ChildPuzzleMember object to add.
     */
    protected function doAddPuzzleMember(ChildPuzzleMember $puzzleMember)
    {
        $this->collPuzzleMembers[]= $puzzleMember;
        $puzzleMember->setMember($this);
    }

    /**
     * @param  ChildPuzzleMember $puzzleMember The ChildPuzzleMember object to remove.
     * @return $this|ChildMember The current object (for fluent API support)
     */
    public function removePuzzleMember(ChildPuzzleMember $puzzleMember)
    {
        if ($this->getPuzzleMembers()->contains($puzzleMember)) {
            $pos = $this->collPuzzleMembers->search($puzzleMember);
            $this->collPuzzleMembers->remove($pos);
            if (null === $this->puzzleMembersScheduledForDeletion) {
                $this->puzzleMembersScheduledForDeletion = clone $this->collPuzzleMembers;
                $this->puzzleMembersScheduledForDeletion->clear();
            }
            $this->puzzleMembersScheduledForDeletion[]= clone $puzzleMember;
            $puzzleMember->setMember(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Member is new, it will return
     * an empty collection; or if this Member has previously
     * been saved, it will retrieve related PuzzleMembers from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Member.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildPuzzleMember[] List of ChildPuzzleMember objects
     */
    public function getPuzzleMembersJoinPuzzle(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildPuzzleMemberQuery::create(null, $criteria);
        $query->joinWith('Puzzle', $joinBehavior);

        return $this->getPuzzleMembers($query, $con);
    }

    /**
     * Clears out the collNews collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addNews()
     */
    public function clearNews()
    {
        $this->collNews = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collNews collection loaded partially.
     */
    public function resetPartialNews($v = true)
    {
        $this->collNewsPartial = $v;
    }

    /**
     * Initializes the collNews collection.
     *
     * By default this just sets the collNews collection to an empty array (like clearcollNews());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initNews($overrideExisting = true)
    {
        if (null !== $this->collNews && !$overrideExisting) {
            return;
        }

        $collectionClassName = NewsTableMap::getTableMap()->getCollectionClassName();

        $this->collNews = new $collectionClassName;
        $this->collNews->setModel('\News');
    }

    /**
     * Gets an array of ChildNews objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildMember is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildNews[] List of ChildNews objects
     * @throws PropelException
     */
    public function getNews(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collNewsPartial && !$this->isNew();
        if (null === $this->collNews || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collNews) {
                // return empty collection
                $this->initNews();
            } else {
                $collNews = ChildNewsQuery::create(null, $criteria)
                    ->filterByMember($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collNewsPartial && count($collNews)) {
                        $this->initNews(false);

                        foreach ($collNews as $obj) {
                            if (false == $this->collNews->contains($obj)) {
                                $this->collNews->append($obj);
                            }
                        }

                        $this->collNewsPartial = true;
                    }

                    return $collNews;
                }

                if ($partial && $this->collNews) {
                    foreach ($this->collNews as $obj) {
                        if ($obj->isNew()) {
                            $collNews[] = $obj;
                        }
                    }
                }

                $this->collNews = $collNews;
                $this->collNewsPartial = false;
            }
        }

        return $this->collNews;
    }

    /**
     * Sets a collection of ChildNews objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $news A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildMember The current object (for fluent API support)
     */
    public function setNews(Collection $news, ConnectionInterface $con = null)
    {
        /** @var ChildNews[] $newsToDelete */
        $newsToDelete = $this->getNews(new Criteria(), $con)->diff($news);


        $this->newsScheduledForDeletion = $newsToDelete;

        foreach ($newsToDelete as $newsRemoved) {
            $newsRemoved->setMember(null);
        }

        $this->collNews = null;
        foreach ($news as $news) {
            $this->addNews($news);
        }

        $this->collNews = $news;
        $this->collNewsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related News objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related News objects.
     * @throws PropelException
     */
    public function countNews(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collNewsPartial && !$this->isNew();
        if (null === $this->collNews || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collNews) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getNews());
            }

            $query = ChildNewsQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByMember($this)
                ->count($con);
        }

        return count($this->collNews);
    }

    /**
     * Method called to associate a ChildNews object to this object
     * through the ChildNews foreign key attribute.
     *
     * @param  ChildNews $l ChildNews
     * @return $this|\Member The current object (for fluent API support)
     */
    public function addNews(ChildNews $l)
    {
        if ($this->collNews === null) {
            $this->initNews();
            $this->collNewsPartial = true;
        }

        if (!$this->collNews->contains($l)) {
            $this->doAddNews($l);

            if ($this->newsScheduledForDeletion and $this->newsScheduledForDeletion->contains($l)) {
                $this->newsScheduledForDeletion->remove($this->newsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildNews $news The ChildNews object to add.
     */
    protected function doAddNews(ChildNews $news)
    {
        $this->collNews[]= $news;
        $news->setMember($this);
    }

    /**
     * @param  ChildNews $news The ChildNews object to remove.
     * @return $this|ChildMember The current object (for fluent API support)
     */
    public function removeNews(ChildNews $news)
    {
        if ($this->getNews()->contains($news)) {
            $pos = $this->collNews->search($news);
            $this->collNews->remove($pos);
            if (null === $this->newsScheduledForDeletion) {
                $this->newsScheduledForDeletion = clone $this->collNews;
                $this->newsScheduledForDeletion->clear();
            }
            $this->newsScheduledForDeletion[]= $news;
            $news->setMember(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Member is new, it will return
     * an empty collection; or if this Member has previously
     * been saved, it will retrieve related News from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Member.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildNews[] List of ChildNews objects
     */
    public function getNewsJoinPuzzle(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildNewsQuery::create(null, $criteria);
        $query->joinWith('Puzzle', $joinBehavior);

        return $this->getNews($query, $con);
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
        $collectionClassName = PuzzleMemberTableMap::getTableMap()->getCollectionClassName();

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
     * to the current object by way of the solver cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildMember is new, it will return
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
                    ->filterByMember($this);
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
     * to the current object by way of the solver cross-reference table.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param  Collection $puzzles A Propel collection.
     * @param  ConnectionInterface $con Optional connection object
     * @return $this|ChildMember The current object (for fluent API support)
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
     * to the current object by way of the solver cross-reference table.
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
                    ->filterByMember($this)
                    ->count($con);
            }
        } else {
            return count($this->collPuzzles);
        }
    }

    /**
     * Associate a ChildPuzzle to this object
     * through the solver cross reference table.
     *
     * @param ChildPuzzle $puzzle
     * @return ChildMember The current object (for fluent API support)
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
        $puzzleMember = new ChildPuzzleMember();

        $puzzleMember->setPuzzle($puzzle);

        $puzzleMember->setMember($this);

        $this->addPuzzleMember($puzzleMember);

        // set the back reference to this object directly as using provided method either results
        // in endless loop or in multiple relations
        if (!$puzzle->isMembersLoaded()) {
            $puzzle->initMembers();
            $puzzle->getMembers()->push($this);
        } elseif (!$puzzle->getMembers()->contains($this)) {
            $puzzle->getMembers()->push($this);
        }

    }

    /**
     * Remove puzzle of this object
     * through the solver cross reference table.
     *
     * @param ChildPuzzle $puzzle
     * @return ChildMember The current object (for fluent API support)
     */
    public function removePuzzle(ChildPuzzle $puzzle)
    {
        if ($this->getPuzzles()->contains($puzzle)) {
            $puzzleMember = new ChildPuzzleMember();
            $puzzleMember->setPuzzle($puzzle);
            if ($puzzle->isMembersLoaded()) {
                //remove the back reference if available
                $puzzle->getMembers()->removeObject($this);
            }

            $puzzleMember->setMember($this);
            $this->removePuzzleMember(clone $puzzleMember);
            $puzzleMember->clear();

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
        $this->full_name = null;
        $this->google_id = null;
        $this->google_refresh = null;
        $this->slack_id = null;
        $this->slack_handle = null;
        $this->strengths = null;
        $this->avatar = null;
        $this->phone_number = null;
        $this->location = null;
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
            if ($this->collWrangledPuzzles) {
                foreach ($this->collWrangledPuzzles as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collNotes) {
                foreach ($this->collNotes as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collPuzzleMembers) {
                foreach ($this->collPuzzleMembers as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collNews) {
                foreach ($this->collNews as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collPuzzles) {
                foreach ($this->collPuzzles as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        $this->collWrangledPuzzles = null;
        $this->collNotes = null;
        $this->collPuzzleMembers = null;
        $this->collNews = null;
        $this->collPuzzles = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(MemberTableMap::DEFAULT_STRING_FORMAT);
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
