<?php
/**
 * Stripe Gateway Tests
 *
 * @package   restrict-content-pro
 * @copyright Copyright (c) 2020, Sandhills Development, LLC
 * @license   GPL2+
 */

namespace RCP\Tests;

/**
 * Class Stripe_Gateway_Tests
 *
 * @package RCP\Tests
 */
class Stripe_Gateway_Tests extends UnitTestCase {

	/**
	 * @var \RCP_Payment_Gateway_Stripe
	 */
	protected static $stripe_gateway;

	/**
	 * Set up a Stripe gateway instance
	 */
	public static function setUpBeforeClass() {
		self::$stripe_gateway = new \RCP_Payment_Gateway_Stripe();

		return parent::setUpBeforeClass();
	}

	/**
	 * Membership duration: 1 month
	 * Signup date: 30 January 2020
	 * Maximum anchor: 29 February 2020
	 *
	 * @covers \RCP_Payment_Gateway_Stripe::get_stripe_max_billing_cycle_anchor
	 */
	public function test_monthly_subscription_from_end_of_january_should_renew_end_february() {

		$signup_date       = '2020-01-30 12:00:00';
		$stripe_max_anchor = self::$stripe_gateway->get_stripe_max_billing_cycle_anchor( 1, 'month', $signup_date );

		$this->assertEquals( '2020-02-29', $stripe_max_anchor->format( 'Y-m-d' ) );

	}

	/**
	 * Membership duration: 3 months
	 * Signup date: 30 November 2020
	 * Maximum anchor: 28 February 2021
	 *
	 * @covers \RCP_Payment_Gateway_Stripe::get_stripe_max_billing_cycle_anchor
	 */
	public function test_three_month_subscription_from_november_should_renew_end_february() {

		$signup_date = '2020-11-30 12:00:00';
		$stripe_max_anchor = self::$stripe_gateway->get_stripe_max_billing_cycle_anchor( 3, 'month', $signup_date );

		$this->assertEquals( '2021-02-28', $stripe_max_anchor->format( 'Y-m-d' ) );

	}

}
