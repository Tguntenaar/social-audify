<?php
/**
 * Customer Factory
 *
 * @package   restrict-content-pro
 * @copyright Copyright (c) 2020, Sandhills Development, LLC
 * @license   GPL2+
 */

namespace RCP\Tests\Factory;

/**
 * Class Customer_Factory
 *
 * @package RCP\Tests\Factory
 */
class Customer_Factory extends \WP_UnitTest_Factory_For_Thing {

	/**
	 * @param array $args
	 * @param null  $generation_definitions
	 *
	 * @return \RCP_Customer|false
	 */
	public function create_and_get( $args = array(), $generation_definitions = null ) {
		return parent::create_and_get( $args, $generation_definitions );
	}

	/**
	 * @inheritDoc
	 */
	public function create_object( $args ) {

		if ( empty( $args['user_id'] ) && empty( $args['user_args'] ) ) {
			// Create a new user.
			$user_factory = new \WP_UnitTest_Factory_For_User();
			$user_id      = $user_factory->create();

			if ( ! is_wp_error( $user_id ) ) {
				$args['user_id'] = absint( $user_id );
			}
		}

		return rcp_add_customer( $args );
	}

	/**
	 * @inheritDoc
	 */
	public function update_object( $object_id, $fields ) {
		return rcp_update_customer( $object_id, $fields );
	}

	/**
	 * Get a single object by ID
	 *
	 * @param int $object_id
	 *
	 * @return \RCP_Customer|false
	 */
	public function get_object_by_id( $object_id ) {
		return rcp_get_customer( $object_id );
	}

}
