<?php

namespace Base;

use \PuzzleArchiveQuery as ChildPuzzleArchiveQuery;
use \DateTime;
use \Exception;
use \PDO;
use Map\PuzzleArchiveTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\BadMethodCallException;
use Propel\Runtime\Exception\LogicException;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Parser\AbstractParser;
use Propel\Runtime\Util\PropelDateTime;

/**
 * Base class that represents a row from the 'puzzle_archive' table.
 *
 *
 *
 * @package    propel.generator..Base
 */
abstract class PuzzleArchive implements ActiveRecordInterface
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Map\\PuzzleArchiveTableMap';


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
     * The value for the wrangler_id field.
     *
     * @var        int
     */
    protected $wrangler_id;

    /**
     * The value for the sheet_mod_date field.
     *
     * @var        DateTime
     */
    protected $sheet_mod_date;

    /**
     * The value for the post_count field.
     *
     * @var        int
     */
    protected $post_count;

    /**
     * The value for the solver_count field.
     *
     * @var        int
     */
    protected $solver_count;

    /**
     * The value for the created_at field.
     *
     * @var        DateTime
     */
    protected $created_at;

    /**
     * The value for the updated_at field.
     *
     * @var        DateTime
     */
    protected $updated_at;

    /**
     * The value for the archived_at field.
     *
     * @var        DateTime
     */
    protected $archived_at;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var boolean
     */
    protected $alreadyInSave = false;

    /**
     * Initializes internal state of Base\PuzzleArchive object.
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
     * Compares this with another <code>PuzzleArchive</code> instance.  If
     * <code>obj</code> is an instance of <code>PuzzleArchive</code>, delegates to
     * <code>equals(PuzzleArchive)</code>.  Otherwise, returns <code>false</code>.
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
     * @return $this|PuzzleArchive The current object, for fluid interface
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
     * Get the [wrangler_id] column value.
     *
     * @return int
     */
    public function getWranglerId()
    {
        return $this->wrangler_id;
    }

    /**
     * Get the [optionally formatted] temporal [sheet_mod_date] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw DateTime object will be returned.
     *
     * @return string|DateTime Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getSheetModDate($format = NULL)
    {
        if ($format === null) {
            return $this->sheet_mod_date;
        } else {
            return $this->sheet_mod_date instanceof \DateTimeInterface ? $this->sheet_mod_date->format($format) : null;
        }
    }

    /**
     * Get the [post_count] column value.
     *
     * @return int
     */
    public function getPostCount()
    {
        return $this->post_count;
    }

    /**
     * Get the [solver_count] column value.
     *
     * @return int
     */
    public function getSolverCount()
    {
        return $this->solver_count;
    }

    /**
     * Get the [optionally formatted] temporal [created_at] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw DateTime object will be returned.
     *
     * @return string|DateTime Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getCreatedAt($format = NULL)
    {
        if ($format === null) {
            return $this->created_at;
        } else {
            return $this->created_at instanceof \DateTimeInterface ? $this->created_at->format($format) : null;
        }
    }

    /**
     * Get the [optionally formatted] temporal [updated_at] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw DateTime object will be returned.
     *
     * @return string|DateTime Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getUpdatedAt($format = NULL)
    {
        if ($format === null) {
            return $this->updated_at;
        } else {
            return $this->updated_at instanceof \DateTimeInterface ? $this->updated_at->format($format) : null;
        }
    }

    /**
     * Get the [optionally formatted] temporal [archived_at] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw DateTime object will be returned.
     *
     * @return string|DateTime Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getArchivedAt($format = NULL)
    {
        if ($format === null) {
            return $this->archived_at;
        } else {
            return $this->archived_at instanceof \DateTimeInterface ? $this->archived_at->format($format) : null;
        }
    }

    /**
     * Set the value of [id] column.
     *
     * @param int $v new value
     * @return $this|\PuzzleArchive The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[PuzzleArchiveTableMap::COL_ID] = true;
        }

        return $this;
    } // setId()

    /**
     * Set the value of [title] column.
     *
     * @param string $v new value
     * @return $this|\PuzzleArchive The current object (for fluent API support)
     */
    public function setTitle($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->title !== $v) {
            $this->title = $v;
            $this->modifiedColumns[PuzzleArchiveTableMap::COL_TITLE] = true;
        }

        return $this;
    } // setTitle()

    /**
     * Set the value of [url] column.
     *
     * @param string $v new value
     * @return $this|\PuzzleArchive The current object (for fluent API support)
     */
    public function setUrl($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->url !== $v) {
            $this->url = $v;
            $this->modifiedColumns[PuzzleArchiveTableMap::COL_URL] = true;
        }

        return $this;
    } // setUrl()

    /**
     * Set the value of [spreadsheet_id] column.
     *
     * @param string $v new value
     * @return $this|\PuzzleArchive The current object (for fluent API support)
     */
    public function setSpreadsheetId($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->spreadsheet_id !== $v) {
            $this->spreadsheet_id = $v;
            $this->modifiedColumns[PuzzleArchiveTableMap::COL_SPREADSHEET_ID] = true;
        }

        return $this;
    } // setSpreadsheetId()

    /**
     * Set the value of [solution] column.
     *
     * @param string $v new value
     * @return $this|\PuzzleArchive The current object (for fluent API support)
     */
    public function setSolution($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->solution !== $v) {
            $this->solution = $v;
            $this->modifiedColumns[PuzzleArchiveTableMap::COL_SOLUTION] = true;
        }

        return $this;
    } // setSolution()

    /**
     * Set the value of [status] column.
     *
     * @param string $v new value
     * @return $this|\PuzzleArchive The current object (for fluent API support)
     */
    public function setStatus($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->status !== $v) {
            $this->status = $v;
            $this->modifiedColumns[PuzzleArchiveTableMap::COL_STATUS] = true;
        }

        return $this;
    } // setStatus()

    /**
     * Set the value of [slack_channel] column.
     *
     * @param string $v new value
     * @return $this|\PuzzleArchive The current object (for fluent API support)
     */
    public function setSlackChannel($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->slack_channel !== $v) {
            $this->slack_channel = $v;
            $this->modifiedColumns[PuzzleArchiveTableMap::COL_SLACK_CHANNEL] = true;
        }

        return $this;
    } // setSlackChannel()

    /**
     * Set the value of [slack_channel_id] column.
     *
     * @param string $v new value
     * @return $this|\PuzzleArchive The current object (for fluent API support)
     */
    public function setSlackChannelId($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->slack_channel_id !== $v) {
            $this->slack_channel_id = $v;
            $this->modifiedColumns[PuzzleArchiveTableMap::COL_SLACK_CHANNEL_ID] = true;
        }

        return $this;
    } // setSlackChannelId()

    /**
     * Set the value of [wrangler_id] column.
     *
     * @param int $v new value
     * @return $this|\PuzzleArchive The current object (for fluent API support)
     */
    public function setWranglerId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->wrangler_id !== $v) {
            $this->wrangler_id = $v;
            $this->modifiedColumns[PuzzleArchiveTableMap::COL_WRANGLER_ID] = true;
        }

        return $this;
    } // setWranglerId()

    /**
     * Sets the value of [sheet_mod_date] column to a normalized version of the date/time value specified.
     *
     * @param  mixed $v string, integer (timestamp), or \DateTimeInterface value.
     *               Empty strings are treated as NULL.
     * @return $this|\PuzzleArchive The current object (for fluent API support)
     */
    public function setSheetModDate($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->sheet_mod_date !== null || $dt !== null) {
            if ($this->sheet_mod_date === null || $dt === null || $dt->format("Y-m-d H:i:s.u") !== $this->sheet_mod_date->format("Y-m-d H:i:s.u")) {
                $this->sheet_mod_date = $dt === null ? null : clone $dt;
                $this->modifiedColumns[PuzzleArchiveTableMap::COL_SHEET_MOD_DATE] = true;
            }
        } // if either are not null

        return $this;
    } // setSheetModDate()

    /**
     * Set the value of [post_count] column.
     *
     * @param int $v new value
     * @return $this|\PuzzleArchive The current object (for fluent API support)
     */
    public function setPostCount($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->post_count !== $v) {
            $this->post_count = $v;
            $this->modifiedColumns[PuzzleArchiveTableMap::COL_POST_COUNT] = true;
        }

        return $this;
    } // setPostCount()

    /**
     * Set the value of [solver_count] column.
     *
     * @param int $v new value
     * @return $this|\PuzzleArchive The current object (for fluent API support)
     */
    public function setSolverCount($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->solver_count !== $v) {
            $this->solver_count = $v;
            $this->modifiedColumns[PuzzleArchiveTableMap::COL_SOLVER_COUNT] = true;
        }

        return $this;
    } // setSolverCount()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param  mixed $v string, integer (timestamp), or \DateTimeInterface value.
     *               Empty strings are treated as NULL.
     * @return $this|\PuzzleArchive The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->created_at !== null || $dt !== null) {
            if ($this->created_at === null || $dt === null || $dt->format("Y-m-d H:i:s.u") !== $this->created_at->format("Y-m-d H:i:s.u")) {
                $this->created_at = $dt === null ? null : clone $dt;
                $this->modifiedColumns[PuzzleArchiveTableMap::COL_CREATED_AT] = true;
            }
        } // if either are not null

        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     *
     * @param  mixed $v string, integer (timestamp), or \DateTimeInterface value.
     *               Empty strings are treated as NULL.
     * @return $this|\PuzzleArchive The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            if ($this->updated_at === null || $dt === null || $dt->format("Y-m-d H:i:s.u") !== $this->updated_at->format("Y-m-d H:i:s.u")) {
                $this->updated_at = $dt === null ? null : clone $dt;
                $this->modifiedColumns[PuzzleArchiveTableMap::COL_UPDATED_AT] = true;
            }
        } // if either are not null

        return $this;
    } // setUpdatedAt()

    /**
     * Sets the value of [archived_at] column to a normalized version of the date/time value specified.
     *
     * @param  mixed $v string, integer (timestamp), or \DateTimeInterface value.
     *               Empty strings are treated as NULL.
     * @return $this|\PuzzleArchive The current object (for fluent API support)
     */
    public function setArchivedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->archived_at !== null || $dt !== null) {
            if ($this->archived_at === null || $dt === null || $dt->format("Y-m-d H:i:s.u") !== $this->archived_at->format("Y-m-d H:i:s.u")) {
                $this->archived_at = $dt === null ? null : clone $dt;
                $this->modifiedColumns[PuzzleArchiveTableMap::COL_ARCHIVED_AT] = true;
            }
        } // if either are not null

        return $this;
    } // setArchivedAt()

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

            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : PuzzleArchiveTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : PuzzleArchiveTableMap::translateFieldName('Title', TableMap::TYPE_PHPNAME, $indexType)];
            $this->title = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : PuzzleArchiveTableMap::translateFieldName('Url', TableMap::TYPE_PHPNAME, $indexType)];
            $this->url = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : PuzzleArchiveTableMap::translateFieldName('SpreadsheetId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->spreadsheet_id = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : PuzzleArchiveTableMap::translateFieldName('Solution', TableMap::TYPE_PHPNAME, $indexType)];
            $this->solution = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : PuzzleArchiveTableMap::translateFieldName('Status', TableMap::TYPE_PHPNAME, $indexType)];
            $this->status = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : PuzzleArchiveTableMap::translateFieldName('SlackChannel', TableMap::TYPE_PHPNAME, $indexType)];
            $this->slack_channel = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 7 + $startcol : PuzzleArchiveTableMap::translateFieldName('SlackChannelId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->slack_channel_id = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 8 + $startcol : PuzzleArchiveTableMap::translateFieldName('WranglerId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->wrangler_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 9 + $startcol : PuzzleArchiveTableMap::translateFieldName('SheetModDate', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->sheet_mod_date = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 10 + $startcol : PuzzleArchiveTableMap::translateFieldName('PostCount', TableMap::TYPE_PHPNAME, $indexType)];
            $this->post_count = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 11 + $startcol : PuzzleArchiveTableMap::translateFieldName('SolverCount', TableMap::TYPE_PHPNAME, $indexType)];
            $this->solver_count = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 12 + $startcol : PuzzleArchiveTableMap::translateFieldName('CreatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->created_at = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 13 + $startcol : PuzzleArchiveTableMap::translateFieldName('UpdatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->updated_at = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 14 + $startcol : PuzzleArchiveTableMap::translateFieldName('ArchivedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->archived_at = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 15; // 15 = PuzzleArchiveTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException(sprintf('Error populating %s object', '\\PuzzleArchive'), 0, $e);
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
            $con = Propel::getServiceContainer()->getReadConnection(PuzzleArchiveTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildPuzzleArchiveQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see PuzzleArchive::setDeleted()
     * @see PuzzleArchive::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(PuzzleArchiveTableMap::DATABASE_NAME);
        }

        $con->transaction(function () use ($con) {
            $deleteQuery = ChildPuzzleArchiveQuery::create()
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
            $con = Propel::getServiceContainer()->getWriteConnection(PuzzleArchiveTableMap::DATABASE_NAME);
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
                PuzzleArchiveTableMap::addInstanceToPool($this);
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


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(PuzzleArchiveTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'id';
        }
        if ($this->isColumnModified(PuzzleArchiveTableMap::COL_TITLE)) {
            $modifiedColumns[':p' . $index++]  = 'title';
        }
        if ($this->isColumnModified(PuzzleArchiveTableMap::COL_URL)) {
            $modifiedColumns[':p' . $index++]  = 'url';
        }
        if ($this->isColumnModified(PuzzleArchiveTableMap::COL_SPREADSHEET_ID)) {
            $modifiedColumns[':p' . $index++]  = 'spreadsheet_id';
        }
        if ($this->isColumnModified(PuzzleArchiveTableMap::COL_SOLUTION)) {
            $modifiedColumns[':p' . $index++]  = 'solution';
        }
        if ($this->isColumnModified(PuzzleArchiveTableMap::COL_STATUS)) {
            $modifiedColumns[':p' . $index++]  = 'status';
        }
        if ($this->isColumnModified(PuzzleArchiveTableMap::COL_SLACK_CHANNEL)) {
            $modifiedColumns[':p' . $index++]  = 'slack_channel';
        }
        if ($this->isColumnModified(PuzzleArchiveTableMap::COL_SLACK_CHANNEL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'slack_channel_id';
        }
        if ($this->isColumnModified(PuzzleArchiveTableMap::COL_WRANGLER_ID)) {
            $modifiedColumns[':p' . $index++]  = 'wrangler_id';
        }
        if ($this->isColumnModified(PuzzleArchiveTableMap::COL_SHEET_MOD_DATE)) {
            $modifiedColumns[':p' . $index++]  = 'sheet_mod_date';
        }
        if ($this->isColumnModified(PuzzleArchiveTableMap::COL_POST_COUNT)) {
            $modifiedColumns[':p' . $index++]  = 'post_count';
        }
        if ($this->isColumnModified(PuzzleArchiveTableMap::COL_SOLVER_COUNT)) {
            $modifiedColumns[':p' . $index++]  = 'solver_count';
        }
        if ($this->isColumnModified(PuzzleArchiveTableMap::COL_CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'created_at';
        }
        if ($this->isColumnModified(PuzzleArchiveTableMap::COL_UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'updated_at';
        }
        if ($this->isColumnModified(PuzzleArchiveTableMap::COL_ARCHIVED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'archived_at';
        }

        $sql = sprintf(
            'INSERT INTO puzzle_archive (%s) VALUES (%s)',
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
                    case 'wrangler_id':
                        $stmt->bindValue($identifier, $this->wrangler_id, PDO::PARAM_INT);
                        break;
                    case 'sheet_mod_date':
                        $stmt->bindValue($identifier, $this->sheet_mod_date ? $this->sheet_mod_date->format("Y-m-d H:i:s.u") : null, PDO::PARAM_STR);
                        break;
                    case 'post_count':
                        $stmt->bindValue($identifier, $this->post_count, PDO::PARAM_INT);
                        break;
                    case 'solver_count':
                        $stmt->bindValue($identifier, $this->solver_count, PDO::PARAM_INT);
                        break;
                    case 'created_at':
                        $stmt->bindValue($identifier, $this->created_at ? $this->created_at->format("Y-m-d H:i:s.u") : null, PDO::PARAM_STR);
                        break;
                    case 'updated_at':
                        $stmt->bindValue($identifier, $this->updated_at ? $this->updated_at->format("Y-m-d H:i:s.u") : null, PDO::PARAM_STR);
                        break;
                    case 'archived_at':
                        $stmt->bindValue($identifier, $this->archived_at ? $this->archived_at->format("Y-m-d H:i:s.u") : null, PDO::PARAM_STR);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), 0, $e);
        }

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
        $pos = PuzzleArchiveTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
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
            case 8:
                return $this->getWranglerId();
                break;
            case 9:
                return $this->getSheetModDate();
                break;
            case 10:
                return $this->getPostCount();
                break;
            case 11:
                return $this->getSolverCount();
                break;
            case 12:
                return $this->getCreatedAt();
                break;
            case 13:
                return $this->getUpdatedAt();
                break;
            case 14:
                return $this->getArchivedAt();
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
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = TableMap::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array())
    {

        if (isset($alreadyDumpedObjects['PuzzleArchive'][$this->hashCode()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['PuzzleArchive'][$this->hashCode()] = true;
        $keys = PuzzleArchiveTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getTitle(),
            $keys[2] => $this->getUrl(),
            $keys[3] => $this->getSpreadsheetId(),
            $keys[4] => $this->getSolution(),
            $keys[5] => $this->getStatus(),
            $keys[6] => $this->getSlackChannel(),
            $keys[7] => $this->getSlackChannelId(),
            $keys[8] => $this->getWranglerId(),
            $keys[9] => $this->getSheetModDate(),
            $keys[10] => $this->getPostCount(),
            $keys[11] => $this->getSolverCount(),
            $keys[12] => $this->getCreatedAt(),
            $keys[13] => $this->getUpdatedAt(),
            $keys[14] => $this->getArchivedAt(),
        );
        if ($result[$keys[9]] instanceof \DateTimeInterface) {
            $result[$keys[9]] = $result[$keys[9]]->format('c');
        }

        if ($result[$keys[12]] instanceof \DateTimeInterface) {
            $result[$keys[12]] = $result[$keys[12]]->format('c');
        }

        if ($result[$keys[13]] instanceof \DateTimeInterface) {
            $result[$keys[13]] = $result[$keys[13]]->format('c');
        }

        if ($result[$keys[14]] instanceof \DateTimeInterface) {
            $result[$keys[14]] = $result[$keys[14]]->format('c');
        }

        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
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
     * @return $this|\PuzzleArchive
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = PuzzleArchiveTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param  int $pos position in xml schema
     * @param  mixed $value field value
     * @return $this|\PuzzleArchive
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
            case 8:
                $this->setWranglerId($value);
                break;
            case 9:
                $this->setSheetModDate($value);
                break;
            case 10:
                $this->setPostCount($value);
                break;
            case 11:
                $this->setSolverCount($value);
                break;
            case 12:
                $this->setCreatedAt($value);
                break;
            case 13:
                $this->setUpdatedAt($value);
                break;
            case 14:
                $this->setArchivedAt($value);
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
        $keys = PuzzleArchiveTableMap::getFieldNames($keyType);

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
        if (array_key_exists($keys[8], $arr)) {
            $this->setWranglerId($arr[$keys[8]]);
        }
        if (array_key_exists($keys[9], $arr)) {
            $this->setSheetModDate($arr[$keys[9]]);
        }
        if (array_key_exists($keys[10], $arr)) {
            $this->setPostCount($arr[$keys[10]]);
        }
        if (array_key_exists($keys[11], $arr)) {
            $this->setSolverCount($arr[$keys[11]]);
        }
        if (array_key_exists($keys[12], $arr)) {
            $this->setCreatedAt($arr[$keys[12]]);
        }
        if (array_key_exists($keys[13], $arr)) {
            $this->setUpdatedAt($arr[$keys[13]]);
        }
        if (array_key_exists($keys[14], $arr)) {
            $this->setArchivedAt($arr[$keys[14]]);
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
     * @return $this|\PuzzleArchive The current object, for fluid interface
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
        $criteria = new Criteria(PuzzleArchiveTableMap::DATABASE_NAME);

        if ($this->isColumnModified(PuzzleArchiveTableMap::COL_ID)) {
            $criteria->add(PuzzleArchiveTableMap::COL_ID, $this->id);
        }
        if ($this->isColumnModified(PuzzleArchiveTableMap::COL_TITLE)) {
            $criteria->add(PuzzleArchiveTableMap::COL_TITLE, $this->title);
        }
        if ($this->isColumnModified(PuzzleArchiveTableMap::COL_URL)) {
            $criteria->add(PuzzleArchiveTableMap::COL_URL, $this->url);
        }
        if ($this->isColumnModified(PuzzleArchiveTableMap::COL_SPREADSHEET_ID)) {
            $criteria->add(PuzzleArchiveTableMap::COL_SPREADSHEET_ID, $this->spreadsheet_id);
        }
        if ($this->isColumnModified(PuzzleArchiveTableMap::COL_SOLUTION)) {
            $criteria->add(PuzzleArchiveTableMap::COL_SOLUTION, $this->solution);
        }
        if ($this->isColumnModified(PuzzleArchiveTableMap::COL_STATUS)) {
            $criteria->add(PuzzleArchiveTableMap::COL_STATUS, $this->status);
        }
        if ($this->isColumnModified(PuzzleArchiveTableMap::COL_SLACK_CHANNEL)) {
            $criteria->add(PuzzleArchiveTableMap::COL_SLACK_CHANNEL, $this->slack_channel);
        }
        if ($this->isColumnModified(PuzzleArchiveTableMap::COL_SLACK_CHANNEL_ID)) {
            $criteria->add(PuzzleArchiveTableMap::COL_SLACK_CHANNEL_ID, $this->slack_channel_id);
        }
        if ($this->isColumnModified(PuzzleArchiveTableMap::COL_WRANGLER_ID)) {
            $criteria->add(PuzzleArchiveTableMap::COL_WRANGLER_ID, $this->wrangler_id);
        }
        if ($this->isColumnModified(PuzzleArchiveTableMap::COL_SHEET_MOD_DATE)) {
            $criteria->add(PuzzleArchiveTableMap::COL_SHEET_MOD_DATE, $this->sheet_mod_date);
        }
        if ($this->isColumnModified(PuzzleArchiveTableMap::COL_POST_COUNT)) {
            $criteria->add(PuzzleArchiveTableMap::COL_POST_COUNT, $this->post_count);
        }
        if ($this->isColumnModified(PuzzleArchiveTableMap::COL_SOLVER_COUNT)) {
            $criteria->add(PuzzleArchiveTableMap::COL_SOLVER_COUNT, $this->solver_count);
        }
        if ($this->isColumnModified(PuzzleArchiveTableMap::COL_CREATED_AT)) {
            $criteria->add(PuzzleArchiveTableMap::COL_CREATED_AT, $this->created_at);
        }
        if ($this->isColumnModified(PuzzleArchiveTableMap::COL_UPDATED_AT)) {
            $criteria->add(PuzzleArchiveTableMap::COL_UPDATED_AT, $this->updated_at);
        }
        if ($this->isColumnModified(PuzzleArchiveTableMap::COL_ARCHIVED_AT)) {
            $criteria->add(PuzzleArchiveTableMap::COL_ARCHIVED_AT, $this->archived_at);
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
        $criteria = ChildPuzzleArchiveQuery::create();
        $criteria->add(PuzzleArchiveTableMap::COL_ID, $this->id);

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
     * @param      object $copyObj An object of \PuzzleArchive (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setId($this->getId());
        $copyObj->setTitle($this->getTitle());
        $copyObj->setUrl($this->getUrl());
        $copyObj->setSpreadsheetId($this->getSpreadsheetId());
        $copyObj->setSolution($this->getSolution());
        $copyObj->setStatus($this->getStatus());
        $copyObj->setSlackChannel($this->getSlackChannel());
        $copyObj->setSlackChannelId($this->getSlackChannelId());
        $copyObj->setWranglerId($this->getWranglerId());
        $copyObj->setSheetModDate($this->getSheetModDate());
        $copyObj->setPostCount($this->getPostCount());
        $copyObj->setSolverCount($this->getSolverCount());
        $copyObj->setCreatedAt($this->getCreatedAt());
        $copyObj->setUpdatedAt($this->getUpdatedAt());
        $copyObj->setArchivedAt($this->getArchivedAt());
        if ($makeNew) {
            $copyObj->setNew(true);
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
     * @return \PuzzleArchive Clone of current object.
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
        $this->wrangler_id = null;
        $this->sheet_mod_date = null;
        $this->post_count = null;
        $this->solver_count = null;
        $this->created_at = null;
        $this->updated_at = null;
        $this->archived_at = null;
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
        } // if ($deep)

    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(PuzzleArchiveTableMap::DEFAULT_STRING_FORMAT);
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
