<?php
/**
 * Base Schema Column Class.
 *
 * @package     RCP
 * @subpackage  Database\Schemas
 * @copyright   Copyright (c) 2018, Easy Digital Downloads, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0
 */

namespace RCP\Database;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Base class used for each column for a custom table.
 *
 * @since 3.0
 *
 * @see   Column::__construct() for accepted arguments.
 */
class Column extends Base {

	/** Table Attributes ******************************************************/

	/**
	 * Name for the database column
	 *
	 * Required. Must contain lowercase alphabetical characters only. Use of any
	 * other character (number, ascii, unicode, emoji, etc...) will result in
	 * fatal application errors.
	 *
	 * @since  3.0
	 * @access public
	 * @var string
	 */
	public $name = '';

	/**
	 * Type of database column
	 *
	 * See: https://dev.mysql.com/doc/en/data-types.html
	 *
	 * @since  3.0
	 * @access public
	 * @var string
	 */
	public $type = '';

	/**
	 * Length of database column
	 *
	 * See: https://dev.mysql.com/doc/en/storage-requirements.html
	 *
	 * @since  3.0
	 * @access public
	 * @var string
	 */
	public $length = false;

	/**
	 * Is integer unsigned?
	 *
	 * See: https://dev.mysql.com/doc/en/numeric-type-overview.html
	 *
	 * @since  3.0
	 * @access public
	 * @var bool
	 */
	public $unsigned = true;

	/**
	 * Is integer filled with zeroes?
	 *
	 * See: https://dev.mysql.com/doc/en/numeric-type-overview.html
	 *
	 * @since  3.0
	 * @access public
	 * @var bool
	 */
	public $zerofill = false;

	/**
	 * Is data in a binary format?
	 *
	 * See: https://dev.mysql.com/doc/en/binary-varbinary.html
	 *
	 * @since  3.0
	 * @access public
	 * @var bool
	 */
	public $binary = false;

	/**
	 * Is null an allowed value?
	 *
	 * See: https://dev.mysql.com/doc/en/data-type-defaults.html
	 *
	 * @since  3.0
	 * @access public
	 * @var bool
	 */
	public $allow_null = false;

	/**
	 * Typically empty/null, or date value
	 *
	 * See: https://dev.mysql.com/doc/en/data-type-defaults.html
	 *
	 * @since  3.0
	 * @access public
	 * @var string
	 */
	public $default = '';

	/**
	 * auto_increment, etc...
	 *
	 * See: https://dev.mysql.com/doc/en/data-type-defaults.html
	 *
	 * @since  3.0
	 * @access public
	 * @var string
	 */
	public $extra = '';

	/**
	 * Typically inherited from the database interface (wpdb).
	 *
	 * By default, this will use the globally available database encoding. You
	 * most likely do not want to change this; if you do, you already know what
	 * to do.
	 *
	 * See: https://dev.mysql.com/doc/mysql/en/charset-column.html
	 *
	 * @since  3.0
	 * @access public
	 * @var string
	 */
	public $encoding = '';

	/**
	 * Typically inherited from the database interface (wpdb).
	 *
	 * By default, this will use the globally available database collation. You
	 * most likely do not want to change this; if you do, you already know what
	 * to do.
	 *
	 * See: https://dev.mysql.com/doc/mysql/en/charset-column.html
	 *
	 * @since  3.0
	 * @access public
	 * @var string
	 */
	public $collation = '';

	/**
	 * Typically empty; probably ignore.
	 *
	 * By default, columns do not have comments. This is unused by any other
	 * relative code, but you can include less than 1024 characters here.
	 *
	 * @since  3.0
	 * @access public
	 * @var string
	 */
	public $comment = '';

	/** Special Attributes ****************************************************/

	/**
	 * Is this the primary column?
	 *
	 * By default, columns are not the primary column. This is used by the Query
	 * class for several critical functions, including (but not limited to) the
	 * cache key, meta-key relationships, auto-incrementing, etc...
	 *
	 * @since  3.0
	 * @access public
	 * @var bool
	 */
	public $primary = false;

	/**
	 * Is this the column used as a created date?
	 *
	 * By default, columns do not represent the date a value was first entered.
	 * This is used by the Query class to set its value automatically to the
	 * current datetime value immediately before insert.
	 *
	 * @since  3.0
	 * @access public
	 * @var bool
	 */
	public $created = false;

	/**
	 * Is this the column used as a modified date?
	 *
	 * By default, columns do not represent the date a value was last changed.
	 * This is used by the Query class to update its value automatically to the
	 * current datetime value immediately before insert|update.
	 *
	 * @since  3.0
	 * @access public
	 * @var bool
	 */
	public $modified = false;

	/**
	 * Is this the column used as a unique universal identifier?
	 *
	 * By default, columns are not UUIDs. This is used by the Query class to
	 * generate a unique string that can be used to identify a row in a database
	 * table, typically in such a way that is unrelated to the row data itself.
	 *
	 * @since  3.0
	 * @access public
	 * @var bool
	 */
	public $uuid = false;

	/** Query Attributes ******************************************************/

	/**
	 * What is the string-replace pattern?
	 *
	 * By default, column patterns will be guessed based on their type. Set this
	 * manually to `%s|%d|%f` only if you are doing something weird, or are
	 * explicitly storing numeric values in text-based column types.
	 *
	 * @since  3.0
	 * @access public
	 * @var string
	 */
	public $pattern = '';

	/**
	 * Is this column searchable?
	 *
	 * By default, columns are not searchable. When `true`, the Query class will
	 * add this column to the results of search queries.
	 *
	 * Avoid setting to `true` on large blobs of text, unless you've optimized
	 * your database server to accommodate these kinds of queries.
	 *
	 * @since  3.0
	 * @access public
	 * @var bool
	 */
	public $searchable = false;

	/**
	 * Is this column a date (that uses WP_Date_Query?)
	 *
	 * By default, columns do not support date queries. When `true`, the Query
	 * class will accept complex statements to help narrow results down to
	 * specific periods of time for values in this column.
	 *
	 * @since  3.0
	 * @access public
	 * @var bool
	 */
	public $date_query = false;

	/**
	 * Is this column used in orderby?
	 *
	 * By default, columns are not sortable. This ensures that the database
	 * table does not perform costly operations on unindexed columns or columns
	 * of an inefficient type.
	 *
	 * You can safely turn this on for most numeric columns, indexed columns,
	 * and text columns with intentionally limited lengths.
	 *
	 * @since  3.0
	 * @access public
	 * @var bool
	 */
	public $sortable = false;

	/**
	 * Is __in supported?
	 *
	 * By default, columns support being queried using an `IN` statement. This
	 * allows the Query class to retrieve rows that match your array of values.
	 *
	 * Consider setting this to `false` for longer text columns.
	 *
	 * @since  3.0
	 * @access public
	 * @var bool
	 */
	public $in = true;

	/**
	 * Is __not_in supported?
	 *
	 * By default, columns support being queried using a `NOT IN` statement.
	 * This allows the Query class to retrieve rows that do not match your array
	 * of values.
	 *
	 * Consider setting this to `false` for longer text columns.
	 *
	 * @since  3.0
	 * @access public
	 * @var bool
	 */
	public $not_in = true;

	/** Cache Attributes ******************************************************/

	/**
	 * Does this column have its own cache key?
	 *
	 * By default, only primary columns are used as cache keys. If this column
	 * is unique, or is frequently used to get database results, you may want to
	 * consider setting this to true.
	 *
	 * Use in conjunction with a database index for speedy queries.
	 *
	 * @since  3.0
	 * @access public
	 * @var string
	 */
	public $cache_key = false;

	/** Action Attributes *****************************************************/

	/**
	 * Does this column fire a transition action when it's value changes?
	 *
	 * By default, columns do not fire transition actions. In some cases, it may
	 * be desirable to know when a database value changes, and what the old and
	 * new values are when that happens.
	 *
	 * The Query class is responsible for triggering the event action.
	 *
	 * @since  3.0
	 * @access public
	 * @var bool
	 */
	public $transition = false;

	/** Callback Attributes ***************************************************/

	/**
	 * Maybe validate this data before it is written to the database.
	 *
	 * By default, column data is validated based on the type of column that it
	 * is. You can set this to a callback function of your choice to override
	 * the default validation behavior.
	 *
	 * @since  3.0.0
	 * @access public
	 * @var string
	 */
	public $validate = '';

	/**
	 * Array of capabilities used to interface with this column.
	 *
	 * These are used by the Query class to allow and disallow CRUD access to
	 * column data, typically based on roles or capabilities.
	 *
	 * @since  3.0.0
	 * @access public
	 * @var array
	 */
	public $caps = array();

	/**
	 * Array of possible aliases this column can be referred to as.
	 *
	 * These are used by the Query class to allow for columns to be renamed
	 * without requiring complex architectural backwards compatability support.
	 *
	 * @since  3.0
	 * @access public
	 * @var array
	 */
	public $aliases = array();

	/**
	 * Array of possible relationships this column has with columns in other
	 * database tables.
	 *
	 * These are typically unenforced foreign keys, and are used by the Query
	 * class to help prime related items.
	 *
	 * @since  3.0
	 * @access public
	 * @var array
	 */
	public $relationships = array();

	/** Methods ***************************************************************/

	/**
	 * Sets up the order query, based on the query vars passed.
	 *
	 * @since  3.0
	 * @access public
	 *
	 * @param string|array $args          {
	 *                                    Optional. Array or query string of order query parameters. Default empty.
	 *
	 * @type string        $name          Name of database column
	 * @type string        $type          Type of database column
	 * @type integer       $length        Length of database column
	 * @type boolean       $unsigned      Is integer unsigned?
	 * @type boolean       $zerofill      Is integer filled with zeroes?
	 * @type boolean       $binary        Is data in a binary format?
	 * @type boolean       $allow_null    Is null an allowed value?
	 * @type mixed         $default       Typically empty/null, or date value
	 * @type string        $extra         auto_increment, etc...
	 * @type string        $encoding      Typically inherited from wpdb
	 * @type string        $collation     Typically inherited from wpdb
	 * @type string        $comment       Typically empty
	 * @type boolean       $pattern       What is the string-replace pattern?
	 * @type boolean       $primary       Is this the primary column?
	 * @type boolean       $created       Is this the column used as a created date?
	 * @type boolean       $modified      Is this the column used as a modified date?
	 * @type boolean       $uuid          Is this the column used as a universally unique identifier?
	 * @type boolean       $searchable    Is this column searchable?
	 * @type boolean       $sortable      Is this column used in orderby?
	 * @type boolean       $date_query    Is this column a datetime?
	 * @type boolean       $in            Is __in supported?
	 * @type boolean       $not_in        Is __not_in supported?
	 * @type boolean       $cache_key     Is this column queried independently?
	 * @type boolean       $transition    Does this column transition between changes?
	 * @type string        $validate      A callback function used to validate on save.
	 * @type array         $caps          Array of capabilities to check.
	 * @type array         $aliases       Array of possible column name aliases.
	 * @type array         $relationships Array of columns in other tables this column relates to.
	 * }
	 */
	public function __construct( $args = array() ) {

		// Parse arguments
		$r = $this->parse_args( $args );

		// Maybe set variables from arguments
		if ( ! empty( $r ) ) {
			$this->set_vars( $r );
		}
	}

	/** Argument Handlers *****************************************************/

	/**
	 * Parse column arguments
	 *
	 * @since  3.0
	 * @access public
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	private function parse_args( $args = array() ) {

		// Parse arguments
		$r = wp_parse_args( $args, array(

			// Table
			'name'          => '',
			'type'          => '',
			'length'        => '',
			'unsigned'      => false,
			'zerofill'      => false,
			'binary'        => false,
			'allow_null'    => false,
			'default'       => '',
			'extra'         => '',
			'encoding'      => $GLOBALS['wpdb']->charset,
			'collation'     => $GLOBALS['wpdb']->collate,
			'comment'       => '',

			// Query
			'pattern'       => false,
			'searchable'    => false,
			'sortable'      => false,
			'date_query'    => false,
			'transition'    => false,
			'in'            => true,
			'not_in'        => true,

			// Special
			'primary'       => false,
			'created'       => false,
			'modified'      => false,
			'uuid'          => false,

			// Cache
			'cache_key'     => false,

			// Validation
			'validate'      => '',

			// Capabilities
			'caps'          => array(),

			// Backwards Compatibility
			'aliases'       => array(),

			// Column Relationships
			'relationships' => array()
		) );

		// Force some arguments for special column types
		$r = $this->special_args( $r );

		// Set the args before they are sanitized
		$this->set_vars( $r );

		// Return array
		return $this->validate_args( $r );
	}

	/**
	 * Validate arguments after they are parsed.
	 *
	 * @since  3.0
	 * @access private
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	private function validate_args( $args = array() ) {

		// Sanitization callbacks
		$callbacks = array(
			'name'       => 'sanitize_key',
			'type'       => 'strtoupper',
			'length'     => 'intval',
			'unsigned'   => 'wp_validate_boolean',
			'zerofill'   => 'wp_validate_boolean',
			'binary'     => 'wp_validate_boolean',
			'allow_null' => 'wp_validate_boolean',
			'default'    => 'wp_kses_data',
			'extra'      => 'wp_kses_data',
			'encoding'   => 'wp_kses_data',
			'collation'  => 'wp_kses_data',
			'comment'    => 'wp_kses_data',

			'primary'  => 'wp_validate_boolean',
			'created'  => 'wp_validate_boolean',
			'modified' => 'wp_validate_boolean',
			'uuid'     => 'wp_validate_boolean',

			'searchable' => 'wp_validate_boolean',
			'sortable'   => 'wp_validate_boolean',
			'date_query' => 'wp_validate_boolean',
			'transition' => 'wp_validate_boolean',
			'in'         => 'wp_validate_boolean',
			'not_in'     => 'wp_validate_boolean',
			'cache_key'  => 'wp_validate_boolean',

			'pattern'       => array( $this, 'sanitize_pattern' ),
			'validate'      => array( $this, 'sanitize_validation' ),
			'caps'          => array( $this, 'sanitize_capabilities' ),
			'aliases'       => array( $this, 'sanitize_aliases' ),
			'relationships' => array( $this, 'sanitize_relationships' )
		);

		// Default args array
		$r = array();

		// Loop through and try to execute callbacks
		foreach ( $args as $key => $value ) {

			// Callback is callable
			if ( isset( $callbacks[$key] ) && is_callable( $callbacks[$key] ) ) {
				$r[$key] = call_user_func( $callbacks[$key], $value );

				// Callback is malformed so just let it through to avoid breakage
			} else {
				$r[$key] = $value;
			}
		}

		// Return sanitized arguments
		return $r;
	}

	/**
	 * Force column arguments for special column types
	 *
	 * @since 3.0
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	private function special_args( $args = array() ) {

		// Primary key columns are always used as cache keys
		if ( ! empty( $args['primary'] ) ) {
			$args['cache_key'] = true;

			// All UUID columns need to follow a very specific pattern
		} elseif ( ! empty( $args['uuid'] ) ) {
			$args['name']       = 'uuid';
			$args['type']       = 'varchar';
			$args['length']     = '100';
			$args['in']         = false;
			$args['not_in']     = false;
			$args['searchable'] = false;
			$args['sortable']   = false;
		}

		// Return args
		return (array) $args;
	}

	/** Public Helpers ********************************************************/

	/**
	 * Return if a column type is numeric or not.
	 *
	 * @since  3.0
	 * @access public
	 * @return boolean
	 */
	public function is_numeric() {
		return $this->is_type( array(
			'tinyint',
			'int',
			'mediumint',
			'bigint'
		) );
	}

	/** Private Helpers *******************************************************/

	/**
	 * Return if this column is of a certain type.
	 *
	 * @since 3.0
	 *
	 * @param mixed $type The type to check. Accepts an array.
	 *
	 * @return boolean True if of type, False if not
	 */
	private function is_type( $type = '' ) {

		// If string, cast to array
		if ( is_string( $type ) ) {
			$type = (array) $type;
		}

		// Make them lowercase
		$types = array_map( 'strtolower', $type );

		// Return if match or not
		return (bool) in_array( strtolower( $this->type ), $types, true );
	}

	/** Private Sanitizers ****************************************************/

	/**
	 * Sanitize capabilities array
	 *
	 * @since 3.0
	 *
	 * @param array $caps
	 *
	 * @return array
	 */
	private function sanitize_capabilities( $caps = array() ) {
		return wp_parse_args( $caps, array(
			'select' => 'exist',
			'insert' => 'exist',
			'update' => 'exist',
			'delete' => 'exist'
		) );
	}

	/**
	 * Sanitize aliases array using `sanitize_key()`
	 *
	 * @since 3.0.0
	 *
	 * @param array $aliases
	 *
	 * @return array
	 */
	private function sanitize_aliases( $aliases = array() ) {
		return array_map( 'sanitize_key', $aliases );
	}

	/**
	 * Sanitize relationships array
	 *
	 * @since 3.0.0
	 *
	 * @param array $relationships
	 *
	 * @return array
	 */
	private function sanitize_relationships( $relationships = array() ) {
		return array_filter( $relationships );
	}

	/**
	 * Sanitize the pattern
	 *
	 * @since 3.0
	 *
	 * @param mixed $pattern
	 *
	 * @return string
	 */
	private function sanitize_pattern( $pattern = false ) {

		// Allowed patterns
		$allowed_patterns = array( '%s', '%d', '%f' );

		// Return pattern if allowed
		if ( in_array( $pattern, $allowed_patterns, true ) ) {
			return $pattern;
		}

		// Fallback to digit or string
		return $this->is_numeric()
			? '%d'
			: '%s';
	}

	/**
	 * Sanitize the validation callback
	 *
	 * @since 3.0
	 *
	 * @param string $callback A callable PHP function name or method
	 *
	 * @return string The most appropriate callback function for the value
	 */
	private function sanitize_validation( $callback = '' ) {

		// Return callback if it's callable
		if ( is_callable( $callback ) ) {
			return $callback;
		}

		// UUID special column
		if ( true === $this->uuid ) {
			$callback = array( $this, 'validate_uuid' );

			// Intval fallback
		} elseif ( $this->is_type( array( 'tinyint', 'int' ) ) ) {
			$callback = 'intval';

			// Datetime fallback
		} elseif ( $this->is_type( 'datetime' ) ) {
			$callback = array( $this, 'validate_datetime' );
		}

		// Return the callback
		return $callback;
	}

	/** Public Validators *****************************************************/

	/**
	 * Fallback to validate a datetime value if no other is set.
	 *
	 * @since 3.0
	 *
	 * @param string $value A datetime value that needs validating
	 *
	 * @return string A valid datetime value
	 */
	public function validate_datetime( $value = '0000-00-00 00:00:00' ) {

		// Fallback for empty values
		if ( empty( $value ) || ( '0000-00-00 00:00:00' === $value ) ) {
			$value = ! empty( $this->default )
				? $this->default
				: '0000-00-00 00:00:00';

			// Fallback if WordPress function exists
		} elseif ( function_exists( 'date' ) ) {
			$value = date( 'Y-m-d H:i:s', strtotime( $value ) );
		}

		// Return the validated value
		return $value;
	}

	/**
	 * Validate a UUID
	 *
	 * This uses the v4 algorithm to generate a UUID that is used to uniquely
	 * and universally identify a given database row without any direct
	 * connection or correlation to the data in that row.
	 *
	 * From http://php.net/manual/en/function.uniqid.php#94959
	 *
	 * @since 3.0
	 *
	 * @param string $uuid The UUID value (empty on insert, string on update)
	 *
	 * @return string Generated UUID.
	 */
	public function validate_uuid( $uuid = '' ) {

		// Bail if not empty (UUIDs should never change once they are set)
		if ( ! empty( $uuid ) ) {
			return $uuid;
		}

		// Default URN UUID prefix
		$prefix = 'urn:uuid:';

		// Put the pieces together
		$uuid = sprintf( "{$prefix}%04x%04x-%04x-%04x-%04x-%04x%04x%04x",

			// 32 bits for "time_low"
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

			// 16 bits for "time_mid"
			mt_rand( 0, 0xffff ),

			// 16 bits for "time_hi_and_version",
			// four most significant bits holds version number 4
			mt_rand( 0, 0x0fff ) | 0x4000,

			// 16 bits, 8 bits for "clk_seq_hi_res",
			// 8 bits for "clk_seq_low",
			// two most significant bits holds zero and one for variant DCE1.1
			mt_rand( 0, 0x3fff ) | 0x8000,

			// 48 bits for "node"
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
		);

		// Return the new UUID
		return $uuid;
	}
}