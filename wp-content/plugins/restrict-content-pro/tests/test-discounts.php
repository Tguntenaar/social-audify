<?php

class RCP_Discount_Tests extends WP_UnitTestCase {

	/**
	 * Database query class.
	 *
	 * @var RCP_Discounts
	 */
	protected $db;

	/**
	 * ID of the discount code.
	 *
	 * @var int
	 */
	protected $discount_id;

	public function setUp() {
		parent::setUp();

		$this->db = new RCP_Discounts;

		$args = array(
			'name'       => 'Test Code',
			'code'       => 'test',
			'status'     => 'active',
			'amount'     => '10',
			'expiration' => '2024-10-10 12:12:50',
			'max_uses'   => 2
		);

		$this->discount_id = rcp_add_discount( $args );

	}

	/**
	 * Format a discount amount
	 *      Percentage, integer
	 *
	 * @covers ::rcp_sanitize_discount_amount()
	 */
	function test_format_amount() {
		$formatted_amount = rcp_sanitize_discount_amount( '10', '%' );
		$this->assertEquals( '10', $formatted_amount );
	}

	/**
	 * Format a discount amount
	 *      Percentage, decimal
	 *      Flat, decimal
	 *
	 * @covers ::rcp_sanitize_discount_amount()
	 */
	function test_format_amount_decimal() {
		$formatted_amount = rcp_sanitize_discount_amount( '10.25', '%' );
		$this->assertTrue( is_wp_error( $formatted_amount ) );

		$formatted_amount = rcp_sanitize_discount_amount( '10.25', 'flat' );
		$this->assertEquals( '10.25', $formatted_amount );
	}

	/**
	 * @covers ::rcp_has_discounts()
	 */
	function test_has_discounts() {
		$this->assertTrue( rcp_has_discounts() );
	}

	/**
	 * Insert a new discount
	 *
	 * @covers ::rcp_add_discount()
	 */
	function test_insert_discount() {

		$args = array(
			'name'   => 'Test Code 2',
			'code'   => 'test2',
			'status' => 'active',
			'amount' => '10',
		);

		$discount_id = rcp_add_discount( $args );

		$this->assertGreaterThan( 1, $discount_id );

	}

	/**
	 * Update an existing discount
	 *
	 * @covers ::rcp_update_discount()
	 */
	function test_update_discount() {

		$updated = rcp_update_discount( $this->discount_id, array(
			'name'   => 'Updated Code',
			'amount' => '10'
		) );

		$this->assertTrue( $updated );

		$discount = rcp_get_discount( $this->discount_id );

		$this->assertEquals( 'Updated Code', $discount->get_name() );

	}

	/**
	 * Get a discount
	 *
	 * @covers ::rcp_get_discount()
	 */
	function test_get_discount() {

		$discount = rcp_get_discount( $this->discount_id );

		$this->assertNotEmpty( $discount );
		$this->assertEquals( 'Test Code', $discount->get_name() );
		$this->assertEquals( 'test', $discount->get_code() );
		$this->assertEquals( 'active', $discount->get_status() );
		$this->assertEquals( '10', $discount->get_amount() );

	}

	/**
	 * Get a discount code by its code
	 *
	 * @covers ::rcp_get_discount_by()
	 */
	function test_get_by() {

		$discount = rcp_get_discount_by( 'code', 'test' );

		$this->assertNotEmpty( $discount );
		$this->assertEquals( 'Test Code', $discount->get_name() );
		$this->assertEquals( 'test', $discount->get_code() );
		$this->assertEquals( 'active', $discount->get_status() );
		$this->assertEquals( '10', $discount->get_amount() );

	}

	/**
	 * Get a discount's status
	 *
	 * @covers RCP_Discount::get_status
	 */
	function test_get_status() {
		$discount = rcp_get_discount( $this->discount_id );
		$this->assertEquals( 'active', $discount->get_status() );
	}

	/**
	 * Get a discount amount
	 *
	 * @covers RCP_Discount::get_amount
	 */
	function test_get_amount() {
		$discount = rcp_get_discount( $this->discount_id );
		$this->assertEquals( '10', $discount->get_amount() );
	}

	/**
	 * Get use count
	 *
	 * @covers RCP_Discount::get_use_count
	 * @covers RCP_Discount::increment_use_count
	 */
	function test_get_uses() {
		$discount = rcp_get_discount( $this->discount_id );

		$this->assertEquals( 0, $discount->get_use_count() );

		$discount->increment_use_count();

		$this->assertEquals( 1, $discount->get_use_count() );
	}

	/**
	 * Get the maximum number of uses
	 *
	 * @covers RCP_Discount::get_max_uses
	 */
	function test_get_max_uses() {
		$discount = rcp_get_discount( $this->discount_id );
		$this->assertEquals( 2, $discount->get_max_uses() );
	}

	/**
	 * A new discount code should not have any membership level ids assigned by default.
	 *
	 * @covers RCP_Discount::get_membership_level_ids()
	 * @since 3.0
	 */
	function test_get_membership_level_ids() {
		$discount = rcp_get_discount( $this->discount_id );
		$this->assertEmpty( $discount->get_membership_level_ids() );
	}

	/**
	 * A new discount code should not have any membership level ids assigned by default.
	 *
	 * @covers RCP_Discount::has_membership_level_ids()
	 * @since 3.0
	 */
	function test_has_membership_level_ids() {
		$discount = rcp_get_discount( $this->discount_id );
		$this->assertFalse( $discount->has_membership_level_ids() );
	}

	/**
	 * Get expiration date
	 *
	 * @covers RCP_Discount::get_expiration
	 */
	function test_get_expiration() {
		$discount = rcp_get_discount( $this->discount_id );
		$this->assertEquals( '2024-10-10 12:12:50', $discount->get_expiration() );
	}

	/**
	 * Get unit
	 *
	 * @covers RCP_Discount::get_unit
	 */
	function test_get_unit() {
		$discount = rcp_get_discount( $this->discount_id );
		$this->assertEquals( '%', $discount->get_unit() );
	}

	/**
	 * Delete a discount
	 *
	 * @covers ::rcp_delete_discount()
	 */
	function test_delete() {

		$deleted = rcp_delete_discount( $this->discount_id );
		$this->assertEquals( 1, $deleted );
		wp_cache_flush(); // @todo Tests were failing without this.. should look into it.
		$discount = rcp_get_discount( $this->discount_id );
		$this->assertEmpty( $discount );
	}

	/**
	 * Max out a discount, then ensure it's maxed out.
	 *
	 * @covers RCP_Discount::is_maxed_out
	 */
	function test_is_maxed_out() {

		$discount = rcp_get_discount( $this->discount_id );

		$this->assertFalse( $discount->is_maxed_out() );

		$discount->increment_use_count();
		$discount->increment_use_count();

		$this->assertTrue( $discount->is_maxed_out() );

	}

	/**
	 * Expire a discount and ensure it's expired.
	 *
	 * @covers RCP_Discount::is_expired
	 */
	function test_is_expired() {

		$discount = rcp_get_discount( $this->discount_id );

		$this->assertFalse( $discount->is_expired() );

		rcp_update_discount( $this->discount_id, array( 'expiration' => '2012-10-10 00:00:00' ) );

		$discount = rcp_get_discount( $this->discount_id );

		$this->assertTrue( $discount->is_expired() );
	}

	/**
	 * User's discount code history
	 *
	 * @covers RCP_Discount::store_for_user
	 * @covers ::rcp_user_has_used_discount()
	 */
	function test_user_has_used() {

		$this->assertFalse( rcp_user_has_used_discount( 1, 'test' ) );

		$discount = rcp_get_discount_by( 'code', 'test' );
		$discount->store_for_user( 1 );

		$this->assertTrue( rcp_user_has_used_discount( 1, 'test' ) );
	}

	/**
	 * @covers ::rcp_discount_sign_filter()
	 */
	function test_format_discount() {

		$this->assertEquals( '&#36;10.00', rcp_discount_sign_filter( 10, 'flat' ) );
		$this->assertEquals( '10%', rcp_discount_sign_filter( 10, '%' ) );
	}

	/**
	 * @covers ::rcp_get_discounted_price()
	 */
	function test_calc_discounted_price() {

		$this->assertEquals( 90, rcp_get_discounted_price( 100, 10, '%' ) );
		$this->assertEquals( 450, rcp_get_discounted_price( 500, 10, '%' ) );

		$this->assertEquals( 90, rcp_get_discounted_price( 100, 10, 'flat' ) );
	}

	/**
	 * Discounted price
	 *      High base price, flat discount amount
	 *
	 * @covers ::rcp_get_discounted_price()
	 */
	function test_calc_discounted_price_with_high_price_and_flat_discount() {

		$this->assertEquals( 1979, rcp_get_discounted_price( 1999, 20, 'flat', false ) );

	}

	/**
	 * Discounted price
	 *      High base price, percentage discount amount
	 *
	 * @covers ::rcp_get_discounted_price()
	 */
	function test_calc_discounted_price_with_high_price_and_percentage_discount() {

		$this->assertEquals( 1599.2, rcp_get_discounted_price( 1999, 20, '%', false ) );

	}

}

