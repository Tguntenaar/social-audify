<?php
/**
 * Customer Object Tests
 *
 * @package   restrict-content-pro
 * @copyright Copyright (c) 2020, Sandhills Development, LLC
 * @license   GPL2+
 */

namespace RCP\Tests;

/**
 * Class Customer_Object_Tests
 *
 * @package RCP\Tests
 */
class Customer_Object_Tests extends UnitTestCase {

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
		$user_id        = self::rcp()->user->create();
		self::$customer = self::rcp()->customer->create_and_get( array(
			'user_id' => $user_id
		) );

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
	 * Delete all of the customer's memberships & reset customer to a default state
	 */
	public function tearDown() {

		parent::tearDown();

		foreach ( self::$customer->get_memberships() as $membership ) {
			rcp_delete_membership( $membership->get_id() );
		}

		self::$customer->update( array(
			'email_verification' => 'none',
			'has_trialed'        => 0,
			'ips'                => '',
			'notes'              => '',
			'last_login'         => '0000-00-00 00:00:00'
		) );

	}

	/**
	 * `has_trialed()` should return false if the customer has no memberships.
	 *
	 * @covers RCP_Customer::has_trialed
	 */
	public function test_customer_no_memberships_has_not_trialed() {
		$this->assertFalse( self::$customer->has_trialed() );
	}

	/**
	 * `has_trialed()` should return false if the customer has a normal free membership.
	 *
	 * @covers RCP_Customer::has_trialed
	 */
	public function test_customer_with_free_membership_not_trialed() {

		self::$customer->add_membership( array(
			'object_id' => self::$level_free->id,
			'status'    => 'active'
		) );

		self::$customer = rcp_get_customer( self::$customer->get_id() );

		$this->assertFalse( self::$customer->has_trialed() );

	}

	/**
	 * `has_trialed()` should return true if the customer has a free trial membership.
	 *
	 * @covers RCP_Customer::has_trialed
	 */
	public function test_customer_with_free_trial_membership_has_trialed() {

		self::$customer->add_membership( array(
			'object_id' => self::$level_free_trial->id,
			'status'    => 'active'
		) );

		self::$customer = rcp_get_customer( self::$customer->get_id() );

		$this->assertTrue( self::$customer->has_trialed() );

	}

	/**
	 * `has_trialed()` should return true if the customer is on a free trial as part of a paid membership.
	 *
	 * @covers RCP_Customer::has_trialed
	 */
	public function test_customer_with_paid_free_trial_membership_has_trialed() {

		self::$customer->add_membership( array(
			'object_id' => self::$level_paid_with_trial->id,
			'status'    => 'active'
		) );

		self::$customer = rcp_get_customer( self::$customer->get_id() );

		$this->assertTrue( self::$customer->has_trialed() );

	}

	/**
	 * `has_trialed()` should return true if the customer had a free trial membership, but it has now expired.
	 *
	 * @covers RCP_Customer::has_trialed
	 */
	public function test_customer_with_expired_trial_membership_has_trialed() {

		$membership_id = self::$customer->add_membership( array(
			'object_id' => self::$level_paid_with_trial->id,
			'status'    => 'active'
		) );

		$membership = rcp_get_membership( $membership_id );
		$membership->expire();

		self::$customer = rcp_get_customer( self::$customer->get_id() );

		$this->assertTrue( self::$customer->has_trialed() );

	}

	/**
	 * `verify_email()` should update the `email_verification` status to `verified`.
	 *
	 * @covers RCP_Customer::verify_email
	 */
	public function test_verifying_email_address_updates_verification_status() {

		$this->assertEquals( 'none', self::$customer->get_email_verification_status() );

		self::$customer->verify_email();

		$this->assertEquals( 'verified', self::$customer->get_email_verification_status() );

	}

}
