<?php
/**
 * Customer Function Tests
 *
 * @package   restrict-content-pro
 * @copyright Copyright (c) 2020, Sandhills Development, LLC
 * @license   GPL2+
 */

namespace RCP\Tests;

/**
 * Class Customer_Function_Tests
 *
 * @package RCP\Tests
 */
class Customer_Function_Tests extends UnitTestCase {

	/**
	 * @var int ID of the user.
	 */
	protected static $user_id;

	/**
	 * @var \RCP_Customer
	 */
	protected static $customer;

	/**
	 * @var object|\WP_Error
	 */
	protected static $level_paid;

	/**
	 * @var object|\WP_Error
	 */
	protected static $level_free;

	/**
	 * Set up once before tests are run
	 */
	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		// Set up a single customer with associated user.
		self::$user_id  = self::rcp()->user->create();
		self::$customer = self::rcp()->customer->create_and_get( array(
			'user_id' => self::$user_id
		) );

		// Set up a paid and free membership level.
		self::$level_paid = self::rcp()->level->create_and_get( array(
			'price' => 10.00
		) );
		self::$level_free = self::rcp()->level->create_and_get( array(
			'price'    => 0.00,
			'duration' => 0
		) );
	}

	/**
	 * Delete all of the customer's memberships
	 */
	public function tearDown() {
		parent::tearDown();

		foreach ( self::$customer->get_memberships() as $membership ) {
			rcp_delete_membership( $membership->get_id() );
		}
	}

	/**
	 * Create a customer with a user ID should return the customer ID
	 *
	 * @covers ::rcp_add_customer()
	 */
	public function test_create_customer_with_user_id_returns_customer_id() {

		$user_id     = $this->rcp()->user->create();
		$customer_id = rcp_add_customer( array(
			'user_id' => $user_id
		) );

		$this->assertGreaterThan( 0, $customer_id );

	}

	/**
	 * Creating a customer via `user_args` but without the `user_email` argument should return false.
	 *
	 * @covers ::rcp_add_customer()
	 */
	public function test_create_customer_with_user_args_missing_email_fails() {

		$customer_id = rcp_add_customer( array(
			'user_args' => array(
				'user_login' => 'user_without_an_email'
			)
		) );

		$this->assertFalse( $customer_id );

	}

	/**
	 * Creating a customer via `user_args` and all required parameters should return the customer ID
	 *
	 * @covers ::rcp_add_customer()
	 */
	public function test_create_customer_with_user_args_returns_customer_id() {

		$customer_id = rcp_add_customer( array(
			'user_args' => array(
				'user_login' => 'my_customer',
				'user_email' => 'my_customer@test.com',
				'user_pass'  => 'soijdffosw£%09wudj'
			)
		) );

		$this->assertGreaterThan( 0, $customer_id );

	}

	/**
	 * Get customer by ID should return the correct customer object
	 *
	 * @covers ::rcp_get_customer()
	 */
	public function test_get_customer_by_id_returns_customer() {

		$customer = rcp_get_customer( self::$customer->get_id() );

		$this->assertInstanceOf( 'RCP_Customer', $customer );
		$this->assertEquals( self::$customer->get_id(), $customer->get_id() );

	}

	/**
	 * Get customer by user ID should return the correct customer object
	 *
	 * @covers ::rcp_get_customer_by_user_id()
	 */
	public function test_get_customer_by_user_id_returns_customer() {

		$customer = rcp_get_customer_by_user_id( self::$user_id );

		$this->assertInstanceOf( 'RCP_Customer', $customer );
		$this->assertEquals( self::$user_id, $customer->get_user_id() );

	}

	/**
	 * Test updating a customer
	 *
	 * @covers RCP_Customer::update
	 * @covers ::rcp_update_customer()
	 */
	public function test_update_customer() {

		$customer = $this->rcp()->customer->create_and_get( array(
			'email_verification' => 'none'
		) );

		$this->assertInstanceOf( 'RCP_Customer', $customer );
		$this->assertEquals( 'none', $customer->get_email_verification_status() );

		$updated = $customer->update( array(
			'email_verification' => 'verified'
		) );

		$this->assertTrue( $updated );
		$this->assertEquals( 'verified', $customer->get_email_verification_status() );

	}

	/**
	 * Test deleting a customer
	 *
	 * The customer record should be deleted, but the user record should not be.
	 *
	 * @covers ::rcp_delete_customer()
	 */
	public function test_delete_customer_returns_true() {

		$customer = $this->rcp()->customer->create_and_get();

		$this->assertInstanceOf( 'RCP_Customer', $customer );

		$user = get_userdata( $customer->get_user_id() );

		$this->assertInstanceOf( 'WP_User', $user );

		$deleted = rcp_delete_customer( $customer->get_id() );

		$this->assertTrue( ! empty( $deleted ) );

		wp_cache_flush();

		$customer_again = rcp_get_customer( $customer->get_id() );

		$this->assertFalse( $customer_again );

		$user_again = get_userdata( $customer->get_user_id() );

		$this->assertInstanceOf( 'WP_User', $user_again );

	}

	/**
	 * If a customer has no memberships, `RCP_Customer::get_memberships()` should return an empty array and
	 * `rcp_user_has_active_membership()` should return false.
	 *
	 * @covers ::rcp_user_has_active_membership()
	 */
	public function test_user_has_active_membership_returns_false_if_no_memberships() {

		$this->assertCount( 0, self::$customer->get_memberships() );
		$this->assertFalse( rcp_user_has_active_membership( self::$customer->get_user_id() ) );

	}

	/**
	 * If a customer has an active membership, `RCP_Customer::get_memberships()` should return a non-empty array and
	 * `rcp_user_has_active_membership()` should return true.
	 *
	 * @covers ::rcp_user_has_active_membership()
	 */
	public function test_user_has_active_membership_returns_true_if_has_active_membership() {

		self::$customer->add_membership( array(
			'object_id' => self::$level_paid->id,
			'status'    => 'active'
		) );

		$this->assertCount( 1, self::$customer->get_memberships() );
		$this->assertTrue( rcp_user_has_active_membership( self::$customer->get_user_id() ) );

	}

	/**
	 * If a customer has a paid membership, `RCP_Customer::get_memberships()` should return a non-empty array and
	 * `rcp_user_has_paid_membership()` should return true.
	 *
	 * @covers ::rcp_user_has_paid_membership()
	 */
	public function test_user_has_paid_membership_returns_true_if_has_active_paid_membership() {

		self::$customer->add_membership( array(
			'object_id' => self::$level_paid->id,
			'status'    => 'active'
		) );

		$this->assertTrue( rcp_user_has_paid_membership( self::$customer->get_user_id() ) );

	}


	/**
	 * If a customer has an expired membership, `rcp_user_has_active_membership()` should return false.
	 *
	 * @covers ::rcp_user_has_active_membership()
	 */
	public function test_user_has_active_membership_returns_false_if_has_expired_membership() {

		self::$customer->add_membership( array(
			'object_id'       => self::$level_paid->id,
			'status'          => 'expired',
			'expiration_date' => date( 'Y-m-d H:i:s', strtotime( '-1 month' ) )
		) );

		$this->assertFalse( rcp_user_has_active_membership( self::$customer->get_user_id() ) );

	}

	/**
	 * Should return false if a customer has a paid membership, but not a free one.
	 *
	 * @covers ::rcp_user_has_free_membership()
	 */
	public function test_user_has_free_membership_returns_false_if_has_paid_membership() {

		self::$customer->add_membership( array(
			'object_id' => self::$level_paid->id,
			'status'    => 'active'
		) );

		$this->assertFalse( rcp_user_has_free_membership( self::$customer->get_user_id() ) );

	}

	/**
	 * Should return true if a customer has a free membership.
	 *
	 * @covers ::rcp_user_has_free_membership()
	 */
	public function test_user_has_free_membership_returns_true_if_has_free_membership() {

		self::$customer->add_membership( array(
			'object_id' => self::$level_free->id,
			'status'    => 'active'
		) );

		$this->assertTrue( rcp_user_has_free_membership( self::$customer->get_user_id() ) );

	}

	/**
	 * Should return true if a user has an expired membership
	 *
	 * @covers ::rcp_user_has_expired_membership()
	 */
	public function test_user_has_expired_memberships_returns_true_if_has_expired_membership() {

		self::$customer->add_membership( array(
			'object_id'       => self::$level_paid->id,
			'status'          => 'expired',
			'expiration_date' => date( 'Y-m-d H:i:s', strtotime( '-1 month' ) )
		) );

		$this->assertTrue( rcp_user_has_expired_membership( self::$customer->get_user_id() ) );

	}

}
