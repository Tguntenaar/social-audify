<?php
/**
 * Cron Job Tests
 *
 * @package   restrict-content-pro
 * @copyright Copyright (c) 2020, Sandhills Development, LLC
 * @license   GPL2+
 */

namespace RCP\Tests;

/**
 * Class Cron_Tests
 *
 * @package RCP\Tests
 */
class Cron_Tests extends UnitTestCase {

	/**
	 * Check member counts cron job should add entries to the `membership_counts` database table.
	 *
	 * @covers ::rcp_check_member_counts
	 */
	public function test_membership_counts_added() {

		$entries_today = rcp_get_membership_count_entries( array(
			'number'             => 1,
			'date_created_query' => array(
				'year'  => date( 'Y' ),
				'month' => date( 'm' ),
				'day'   => date( 'd' )
			)
		) );

		$this->assertEquals( 0, count( $entries_today ) );

		// Run counts.
		rcp_check_member_counts();

		// We should now have results.
		$entries_today = rcp_get_membership_count_entries( array(
			'number'             => 1,
			'date_created_query' => array(
				'year'  => date( 'Y' ),
				'month' => date( 'm' ),
				'day'   => date( 'd' )
			)
		) );

		$this->assertEquals( 1, count( $entries_today ) );

	}

}
