<?php
/**
* KeyDesign Theme Admin Panel
* The KeyDesign_Admin_Page base class
*/

if( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class KeyDesign_Admin_Page extends KeyDesign_Admin {

  public $parent = null;
  public $capability = 'manage_options';
  public $icon = 'dashicons-art';
  public $position;

	public function __construct() {

		$priority = -1;
		if ( isset( $this->parent ) && $this->parent ) {
			$priority = intval( $this->position );
		}
		$this->position = 2;
		$this->add_action( 'admin_menu', 'register_page', $priority );

		if( !isset( $_GET['page'] ) || empty( $_GET['page'] ) || ! $this->id === $_GET['page'] ) {
			return;
		}

		if( method_exists( $this, 'save' ) ) {
			$this->add_action( 'admin_init', 'save' );
		}
	}

	public function register_page() {

		if( ! $this->parent ) {
			add_menu_page(
				$this->page_title,
				$this->menu_title,
				$this->capability,
				$this->id,
				array( $this, 'display' ),
        'dashicons-welcome-widgets-menus',
				$this->position
			);
		}
		else {
			add_submenu_page(
				$this->parent,
				$this->page_title,
				$this->menu_title,
				$this->capability,
				$this->id,
				array( $this, 'display' )
			);
		}
	}

	public function display() {
		echo 'default';
	}
}
