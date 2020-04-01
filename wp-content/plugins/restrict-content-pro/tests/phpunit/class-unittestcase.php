<?php
/**
 * Base Unit Test Case
 *
 * @package   restrict-content-pro
 * @copyright Copyright (c) 2020, Sandhills Development, LLC
 * @license   GPL2+
 */

namespace RCP\Tests;

/**
 * Class UnitTestCase
 *
 * @package RCP\Tests
 */
class UnitTestCase extends \WP_UnitTestCase {

	/**
	 * Delete RCP table data after each class.
	 */
	public static function tearDownAfterClass() {
		self::deleteRCPData();

		return parent::tearDownAfterClass();
	}

	/**
	 * Get the factory
	 *
	 * @return Factory|null
	 */
	protected static function rcp() {
		static $factory = null;

		if ( ! $factory ) {
			$factory = new Factory();
		}

		return $factory;
	}

	/**
	 * Truncate all RCP database tables
	 *
	 * For now this only actually truncates tables registered via BerlinDB.
	 */
	protected static function deleteRCPData() {

		// @todo Would eventually be nice to have component registration to make this better.
		$tables = array(
			restrict_content_pro()->customers_table,
			restrict_content_pro()->discounts_table,
			restrict_content_pro()->memberships_table,
			restrict_content_pro()->membership_meta_table,
			restrict_content_pro()->queue_table,
			restrict_content_pro()->membership_counts_table
		);

		foreach ( $tables as $table ) {
			/**
			 * @var \RCP\Database\Table
			 */
			if ( method_exists( $table, 'truncate' ) ) {
				$table->truncate();
			}
		}

		// Reset settings
		$GLOBALS['rcp_options'] = get_option( 'rcp_settings', array() );

	}

}
