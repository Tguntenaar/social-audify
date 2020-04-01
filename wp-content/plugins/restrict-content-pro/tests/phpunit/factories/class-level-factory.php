<?php
/**
 * Membership Level Factory
 *
 * @package   restrict-content-pro
 * @copyright Copyright (c) 2020, Sandhills Development, LLC
 * @license   GPL2+
 */

namespace RCP\Tests\Factory;

/**
 * Class Level_Factory
 *
 * @package RCP\Tests\Factory
 */
class Level_Factory extends \WP_UnitTest_Factory_For_Thing {

	/**
	 * @var \RCP_Levels
	 */
	protected $levels_db;

	/**
	 * Level_Factory constructor.
	 *
	 * @param       $factory
	 * @param array $default_generation_definitions
	 */
	public function __construct( $factory, $default_generation_definitions = array() ) {
		$default_generation_definitions = array(
			'name'                => new \WP_UnitTest_Generator_Sequence( 'Level Name %s' ),
			'description'         => new \WP_UnitTest_Generator_Sequence( 'Level Description %s' ),
			'duration'            => 1,
			'duration_unit'       => 'month',
			'price'               => 10.00,
			'fee'                 => 0.00,
			'status'              => 'active'
		);

		parent::__construct( $factory, $default_generation_definitions );

		$this->levels_db = new \RCP_Levels();
	}

	/**
	 * @param array $args
	 * @param null  $generation_definitions
	 *
	 * @return object|\WP_Error
	 */
	public function create_and_get( $args = array(), $generation_definitions = null ) {
		return parent::create_and_get( $args, $generation_definitions );
	}

	/**
	 * @return int|\WP_Error
	 */
	public function create_object( $args ) {
		return $this->levels_db->insert( $args );
	}

	/**
	 * @inheritDoc
	 */
	public function update_object( $object_id, $fields ) {
		return $this->levels_db->update( $object_id, $fields );
	}

	/**
	 * Get a single object by ID
	 *
	 * @param int $object_id
	 *
	 * @return \object|false
	 */
	public function get_object_by_id( $object_id ) {
		return $this->levels_db->get_level( $object_id );
	}

}
