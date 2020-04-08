<?php
/**
 * Expiration & Renewal Reminder Tests
 *
 * @package   restrict-content-pro
 * @copyright Copyright (c) 2020, Sandhills Development, LLC
 * @license   GPL2+
 * @since     3.3.3
 */

namespace RCP\Tests;

use RCP_Customer;
use RCP_Levels;
use RCP_Reminders;

class Reminder_Tests extends UnitTestCase {

	/**
	 * @var RCP_Reminders Reminder helper class.
	 */
	protected static $reminders;

	/**
	 * @var RCP_Customer Customer object for use in creating memberships.
	 */
	protected static $customer;

	/**
	 * @var false|object Bronze membership level
	 */
	protected static $membership_level_bronze;

	/**
	 * @var false|object Silver membership level
	 */
	protected static $membership_level_silver;

	/**
	 * @var false|object Gold membership level - contains free trial
	 */
	protected static $membership_level_gold;

	/**
	 * Set up once before tests are run
	 */
	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		self::$reminders = new RCP_Reminders();

		// Create a user & customer.
		$user = wp_insert_user( array(
			'user_login' => 'test',
			'user_pass'  => 'pass',
			'first_name' => 'Tester',
			'user_email' => 'test@test.com'
		) );

		if ( ! is_wp_error( $user ) ) {
			$customer_id = rcp_add_customer( array(
				'user_id' => absint( $user )
			) );

			if ( ! empty( $customer_id ) ) {
				self::$customer = rcp_get_customer( $customer_id );
			}
		}

		// Create Bronze, Silver, & Gold membership levels.
		$levels_db = new RCP_Levels();
		$level_id  = $levels_db->insert( array(
			'name'          => 'Bronze',
			'duration'      => 1,
			'duration_unit' => 'month',
			'price'         => 10,
			'status'        => 'active'
		) );

		self::$membership_level_bronze = rcp_get_subscription_details( $level_id );

		$level_id = $levels_db->insert( array(
			'name'          => 'Silver',
			'duration'      => 1,
			'duration_unit' => 'month',
			'price'         => 20,
			'status'        => 'active'
		) );

		self::$membership_level_silver = rcp_get_subscription_details( $level_id );

		$level_id = $levels_db->insert( array(
			'name'                => 'Gold',
			'duration'            => 1,
			'duration_unit'       => 'month',
			'price'               => 50,
			'status'              => 'active',
			'trial_duration'      => 7,
			'trial_duration_unit' => 'day'
		) );

		self::$membership_level_gold = rcp_get_subscription_details( $level_id );

		// Create a membership for "Bronze".
		self::$customer->add_membership( array(
			'object_id'        => self::$membership_level_bronze->id,
			'initial_amount'   => self::$membership_level_bronze->price + self::$membership_level_bronze->fee,
			'recurring_amount' => self::$membership_level_bronze->price,
			'status'           => 'active',
			'expiration_date'  => date( 'Y-m-d 23:59:59', strtotime( '+3 days', current_time( 'timestamp' ) ) ),
			'auto_renew'       => 0
		) );

		// Create a membership for "Silver".
		self::$customer->add_membership( array(
			'object_id'        => self::$membership_level_silver->id,
			'initial_amount'   => self::$membership_level_silver->price + self::$membership_level_silver->fee,
			'recurring_amount' => self::$membership_level_silver->price,
			'status'           => 'active',
			'expiration_date'  => date( 'Y-m-d 23:59:59', strtotime( '+3 days', current_time( 'timestamp' ) ) ),
			'auto_renew'       => 1
		) );

		// Create a membership for "Gold" that's trialling.
		self::$customer->add_membership( array(
			'object_id'        => self::$membership_level_gold->id,
			'initial_amount'   => 0.00,
			'recurring_amount' => self::$membership_level_gold->price,
			'status'           => 'active',
			'trial_end_date'   => date( 'Y-m-d 23:59:59', strtotime( '+3 days', current_time( 'timestamp' ) ) ),
			'expiration_date'  => date( 'Y-m-d 23:59:59', strtotime( '+3 days', current_time( 'timestamp' ) ) ),
			'auto_renew'       => 1
		) );

	}

	/**
	 * A membership that does not have auto renew should receive an expiration reminder.
	 *
	 * @covers RCP_Reminders::get_reminder_subscriptions
	 */
	public function test_non_recurring_membership_receives_expiration_reminder() {

		$bronze_membership = self::$customer->get_memberships( array(
			'object_id' => self::$membership_level_bronze->id
		) );

		$bronze_membership = reset( $bronze_membership );

		/**
		 * @var \RCP_Membership $bronze_membership
		 */

		$this->assertEquals( date( 'Y-m-d 23:59:59', strtotime( '+3 days', current_time( 'timestamp' ) ) ), $bronze_membership->get_expiration_date( false ) );
		$this->assertFalse( $bronze_membership->is_recurring() );

		$memberships    = self::$reminders->get_reminder_subscriptions( '+3 days', 'expiration', 'all' );
		$membership_ids = array();

		$this->assertTrue( is_array( $memberships ) );

		foreach ( $memberships as $membership ) {
			$membership_ids[] = $membership->get_id();
		}

		$this->assertTrue( in_array( $bronze_membership->get_id(), $membership_ids ) );

	}

	/**
	 * A membership that does have auto renew enabled should not receive an expiration reminder.
	 *
	 * @covers RCP_Reminders::get_reminder_subscriptions
	 */
	public function test_recurring_membership_doesnt_receive_expiration_reminder() {

		$silver_membership = self::$customer->get_memberships( array(
			'object_id' => self::$membership_level_silver->id
		) );

		$silver_membership = reset( $silver_membership );

		/**
		 * @var \RCP_Membership $silver_membership
		 */

		$this->assertEquals( date( 'Y-m-d 23:59:59', strtotime( '+3 days', current_time( 'timestamp' ) ) ), $silver_membership->get_expiration_date( false ) );
		$this->assertTrue( $silver_membership->is_recurring() );

		$memberships    = self::$reminders->get_reminder_subscriptions( '+3 days', 'expiration', 'all' );
		$membership_ids = array();

		$this->assertTrue( is_array( $memberships ) );

		foreach ( $memberships as $membership ) {
			$membership_ids[] = $membership->get_id();
		}

		$this->assertFalse( in_array( $silver_membership->get_id(), $membership_ids ) );

	}

	/**
	 * A membership that does have auto renew enabled should receive a renewal reminder.
	 *
	 * @covers RCP_Reminders::get_reminder_subscriptions
	 */
	public function test_recurring_membership_receives_renewal_reminder() {

		$silver_membership = self::$customer->get_memberships( array(
			'object_id' => self::$membership_level_silver->id
		) );

		$silver_membership = reset( $silver_membership );

		/**
		 * @var \RCP_Membership $silver_membership
		 */

		$this->assertEquals( date( 'Y-m-d 23:59:59', strtotime( '+3 days', current_time( 'timestamp' ) ) ), $silver_membership->get_expiration_date( false ) );
		$this->assertTrue( $silver_membership->is_recurring() );

		$memberships    = self::$reminders->get_reminder_subscriptions( '+3 days', 'renewal', 'all' );
		$membership_ids = array();

		$this->assertTrue( is_array( $memberships ) );

		foreach ( $memberships as $membership ) {
			$membership_ids[] = $membership->get_id();
		}

		$this->assertTrue( in_array( $silver_membership->get_id(), $membership_ids ) );

	}

	/**
	 * A membership that does not have auto renew enabled should not receive a renewal reminder.
	 *
	 * @covers RCP_Reminders::get_reminder_subscriptions
	 */
	public function test_non_recurring_membership_doesnt_receive_renewal_reminder() {

		$bronze_membership = self::$customer->get_memberships( array(
			'object_id' => self::$membership_level_bronze->id
		) );

		$bronze_membership = reset( $bronze_membership );

		/**
		 * @var \RCP_Membership $bronze_membership
		 */

		$this->assertEquals( date( 'Y-m-d 23:59:59', strtotime( '+3 days', current_time( 'timestamp' ) ) ), $bronze_membership->get_expiration_date( false ) );
		$this->assertFalse( $bronze_membership->is_recurring() );

		$memberships    = self::$reminders->get_reminder_subscriptions( '+3 days', 'renewal', 'all' );
		$membership_ids = array();

		$this->assertTrue( is_array( $memberships ) );

		foreach ( $memberships as $membership ) {
			$membership_ids[] = $membership->get_id();
		}

		$this->assertFalse( in_array( $bronze_membership->get_id(), $membership_ids ) );

	}

	/**
	 * Ensure a membership that has a non-standard expiration time (not our normal forced 23:59:59) will still
	 * receive a reminder.
	 *
	 * @covers RCP_Reminders::get_reminder_subscriptions
	 */
	public function test_non_standard_expiry_time_receives_reminder() {

		$bronze_membership = self::$customer->get_memberships( array(
			'object_id' => self::$membership_level_bronze->id
		) );

		$bronze_membership = reset( $bronze_membership );

		/**
		 * @var \RCP_Membership $bronze_membership
		 */

		$bronze_membership->update( array(
			'expiration_date' => date( 'Y-m-d 12:00:00', strtotime( '+3 days', current_time( 'timestamp' ) ) )
		) );

		$memberships    = self::$reminders->get_reminder_subscriptions( '+3 days', 'expiration', 'all' );
		$membership_ids = array();

		$this->assertTrue( is_array( $memberships ) );

		foreach ( $memberships as $membership ) {
			$membership_ids[] = $membership->get_id();
		}

		$this->assertTrue( in_array( $bronze_membership->get_id(), $membership_ids ) );

	}

	/**
	 * A membership that's outside the queried range should not receive a reminder.
	 *
	 * @covers RCP_Reminders::get_reminder_subscriptions
	 */
	public function test_membership_outside_range_should_not_receive_reminder() {

		$bronze_membership = self::$customer->get_memberships( array(
			'object_id' => self::$membership_level_bronze->id
		) );

		$bronze_membership = reset( $bronze_membership );

		/**
		 * @var \RCP_Membership $bronze_membership
		 */

		// Check expiration reminders.
		$memberships = self::$reminders->get_reminder_subscriptions( '+5 days', 'expiration', 'all' );

		$this->assertFalse( $memberships );

	}

	/**
	 * A "Bronze" membership shouldn't receive an expiration reminder when we're querying for "Silver" memberships only.
	 *
	 * @covers RCP_Reminders::get_reminder_subscriptions
	 */
	public function test_bronze_membership_doesnt_receive_silver_expiration_reminder() {

		$bronze_membership = self::$customer->get_memberships( array(
			'object_id' => self::$membership_level_bronze->id
		) );

		$bronze_membership = reset( $bronze_membership );

		/**
		 * @var \RCP_Membership $bronze_membership
		 */

		$memberships = self::$reminders->get_reminder_subscriptions( '+3 days', 'expiration', array( self::$membership_level_silver->id ) );

		$this->assertFalse( $memberships );

	}

	/**
	 * A cancelled membership should receive an expiration reminder.
	 *
	 * @covers RCP_Reminders::get_reminder_subscriptions
	 */
	public function test_cancelled_membership_receives_expiration_reminder() {

		$bronze_membership = self::$customer->get_memberships( array(
			'object_id' => self::$membership_level_bronze->id
		) );

		$bronze_membership = reset( $bronze_membership );

		/**
		 * @var \RCP_Membership $bronze_membership
		 */

		$bronze_membership->cancel();

		$memberships    = self::$reminders->get_reminder_subscriptions( '+3 days', 'expiration', 'all' );
		$membership_ids = array();

		$this->assertTrue( is_array( $memberships ) );

		foreach ( $memberships as $membership ) {
			$membership_ids[] = $membership->get_id();
		}

		$this->assertTrue( in_array( $bronze_membership->get_id(), $membership_ids ) );

	}

	/**
	 * A trialling membership with auto renew enabled should receive a renewal reminder
	 *
	 * @covers RCP_Reminders::get_reminder_subscriptions
	 */
	public function test_recurring_trialling_membership_receives_renewal_reminder() {

		$gold_membership = self::$customer->get_memberships( array(
			'object_id' => self::$membership_level_gold->id
		) );

		$gold_membership = reset( $gold_membership );

		/**
		 * @var \RCP_Membership $gold_membership
		 */

		$this->assertEquals( date( 'Y-m-d 23:59:59', strtotime( '+3 days', current_time( 'timestamp' ) ) ), $gold_membership->get_expiration_date( false ) );
		$this->assertTrue( $gold_membership->is_trialing() );
		$this->assertTrue( $gold_membership->is_recurring() );

		$memberships    = self::$reminders->get_reminder_subscriptions( '+3 days', 'renewal', 'all' );
		$membership_ids = array();

		$this->assertTrue( is_array( $memberships ) );

		foreach ( $memberships as $membership ) {
			$membership_ids[] = $membership->get_id();
		}

		$this->assertTrue( in_array( $gold_membership->get_id(), $membership_ids ) );

	}

	/**
	 * A trialling membership with auto renew disabled should receive an expiration reminder
	 *
	 * @covers RCP_Reminders::get_reminder_subscriptions
	 */
	public function test_non_recurring_trialling_membership_receives_expiration_reminder() {

		$gold_membership = self::$customer->get_memberships( array(
			'object_id' => self::$membership_level_gold->id
		) );

		$gold_membership = reset( $gold_membership );

		/**
		 * @var \RCP_Membership $gold_membership
		 */

		$gold_membership->cancel();

		$this->assertEquals( date( 'Y-m-d 23:59:59', strtotime( '+3 days', current_time( 'timestamp' ) ) ), $gold_membership->get_expiration_date( false ) );
		$this->assertTrue( $gold_membership->is_trialing() );
		$this->assertFalse( $gold_membership->is_recurring() );

		$memberships    = self::$reminders->get_reminder_subscriptions( '+3 days', 'expiration', 'all' );
		$membership_ids = array();

		$this->assertTrue( is_array( $memberships ) );

		foreach ( $memberships as $membership ) {
			$membership_ids[] = $membership->get_id();
		}

		$this->assertTrue( in_array( $gold_membership->get_id(), $membership_ids ) );

	}

}
