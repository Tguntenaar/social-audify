<?php
/**
 * Memberships Backwards Compatibility
 *
 * Testing for deprecated functions.
 *
 * @package   restrict-content-pro
 * @copyright Copyright (c) 2020, Sandhills Development, LLC
 * @license   GPL2+
 */

namespace RCP\Tests;

/**
 * Class Membership_Backwards_Compat_Tests
 *
 * @package RCP\Tests
 */
class Membership_Backwards_Compat_Tests extends UnitTestCase {

	/**
	 * @var \RCP_Customer
	 */
	protected static $customer;

	/**
	 * @var object|\WP_Error
	 */
	protected static $level_free;

	/**
	 * @var object|\WP_Error
	 */
	protected static $level_paid;

	/**
	 * @var object|\WP_Error
	 */
	protected static $level_free_trial;

	/**
	 * @var object|\WP_Error
	 */
	protected static $level_paid_with_trial;

	/**
	 * Set up once before tests are run
	 */
	public static function setUpBeforeClass() {

		parent::setUpBeforeClass();

		// Set up a single customer with associated user.
		self::$customer = self::rcp()->customer->create_and_get();

		// Set up some membership levels.
		self::$level_free            = self::rcp()->level->create_and_get( array(
			'price'    => 0.00,
			'duration' => 0
		) );
		self::$level_paid            = self::rcp()->level->create_and_get( array(
			'price' => 10.00
		) );
		self::$level_free_trial      = self::rcp()->level->create_and_get( array(
			'price'         => 0.00,
			'duration'      => 7,
			'duration_unit' => 'day'
		) );
		self::$level_paid_with_trial = self::rcp()->level->create_and_get( array(
			'price'               => 10.00,
			'duration'            => 1,
			'duration_unit'       => 'month',
			'trial_duration'      => 7,
			'trial_duration_unit' => 'day'
		) );

	}

	/**
	 * If a user has one expired and one active membership then `rcp_is_active()` should still return `true`.
	 *
	 * @link https://github.com/restrictcontentpro/restrict-content-pro/issues/2684
	 *
	 * @covers ::rcp_is_active()
	 */
	public function test_member_with_active_and_expired_membership_is_active() {

		// Enable multiple memberships.
		global $rcp_options;
		$rcp_options['multiple_memberships'] = 1;

		// Add two memberships.
		$membership_1_id = self::$customer->add_membership( array(
			'object_id' => self::$level_paid->id,
			'status'    => 'active'
		) );

		$membership_2_id = self::$customer->add_membership( array(
			'object_id' => self::$level_paid->id,
			'status'    => 'active'
		) );

		$this->assertEquals( 2, count( self::$customer->get_memberships( array( 'status' => 'active' ) ) ) );
		$this->assertTrue( rcp_is_active( self::$customer->get_user_id() ) );

		// Expire one of the memberships.
		$membership_1 = rcp_get_membership( $membership_1_id );
		$membership_1->expire();

		$this->assertEquals( 1, count( self::$customer->get_memberships( array( 'status' => 'active' ) ) ) );

		// User should still be active.
		$this->assertTrue( rcp_is_active( self::$customer->get_user_id() ) );

		// Now expire the other one.
		$membership_2 = rcp_get_membership( $membership_2_id );
		$membership_2->expire();

		$this->assertEquals( 0, count( self::$customer->get_memberships( array( 'status' => 'active' ) ) ) );

		// User should no longer be active.
		$this->assertFalse( rcp_is_active( self::$customer->get_user_id() ) );

	}

	/**
	 * Member with a free trial membership is considered active
	 *
	 * @covers ::rcp_is_active()
	 */
	public function test_member_with_trial_membership_is_active() {

		$customer      = $this->rcp()->customer->create_and_get();
		$membership_id = $customer->add_membership( array(
			'object_id' => self::$level_paid_with_trial->id,
			'status'    => 'active'
		) );
		$membership    = rcp_get_membership( $membership_id );

		$this->assertTrue( $membership->is_trialing() );
		$this->assertTrue( rcp_is_active( $customer->get_user_id() ) );

	}

	/**
	 * Member with a free trial membership is considered active
	 *
	 * New `RCP_Membership::is_active()` should return true, but deprecated `rcp_is_active()` should return false.
	 *
	 * @covers ::rcp_is_active()
	 */
	public function test_member_with_free_membership_not_active() {

		$customer      = $this->rcp()->customer->create_and_get();
		$membership_id = $customer->add_membership( array(
			'object_id' => self::$level_free->id,
			'status'    => 'active'
		) );
		$membership    = rcp_get_membership( $membership_id );

		$this->assertTrue( $membership->is_active() );
		$this->assertFalse( rcp_is_active( $customer->get_user_id() ) );

	}

}
