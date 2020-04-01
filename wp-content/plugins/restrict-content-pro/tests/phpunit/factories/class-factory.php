<?php
/**
 * Factory
 *
 * @package   restrict-content-pro
 * @copyright Copyright (c) 2020, Sandhills Development, LLC
 * @license   GPL2+
 */

namespace RCP\Tests;

/**
 * Class Factory
 *
 * @package RCP\Tests
 */
class Factory extends \WP_UnitTest_Factory {

	/**
	 * @var Factory\Customer_Factory
	 */
	public $customer;

	/**
	 * @var Factory\Level_Factory
	 */
	public $level;

	/**
	 * Factory constructor.
	 */
	public function __construct() {
		parent::__construct();

		$this->customer = new Factory\Customer_Factory( $this );
		$this->level    = new Factory\Level_Factory( $this );
	}

}
