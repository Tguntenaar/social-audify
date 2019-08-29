<?php
/**
* KeyDesign Theme Admin Panel
* Initiate the theme admin pages
*/

if( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class KeyDesign_Admin {
  public function __construct() {
		$this->add_action( 'init', 'init', 7 );
		$this->add_action( 'admin_menu', 'keydesign_dashboard_menu', 999 );
		$this->add_action( 'admin_bar_menu', 'keydesign_admin_bar', 999 );
		$this->add_action( 'redux/loaded', 'keydesign_remove_redux_demo' );
    $this->add_filter( 'tgmpa_admin_menu_args', 'keydesign_required_plugins_menu', 10, 1 );
	}

  public function add_action( $hook, $function_to_add, $priority = 10, $accepted_args = 1 ) {
		add_action( $hook, array( &$this, $function_to_add ), $priority, $accepted_args );
	}

  public function add_filter( $tag, $function_to_add, $priority = 10, $accepted_args = 1 ) {
		add_filter( $tag, array( &$this, $function_to_add ), $priority, $accepted_args );
	}

  public function init() {
    include_once ( plugin_dir_path( __FILE__ ).'admin-page.php' );
		include_once ( plugin_dir_path( __FILE__ ).'admin-dashboard.php' );
	}

  public function keydesign_dashboard_menu() {

    if ( !current_user_can( 'edit_theme_options' ) ) {
        return;
    }

		global $submenu;
		$submenu['leadengine-dashboard'][0][0] = esc_html__( 'Dashboard', 'leadengine' );

    remove_submenu_page( 'tools.php', 'redux-about' );
	}

  // Add TGMPA menu item in the Dashboard menu dropdown
  public function keydesign_required_plugins_menu($args) {
      $args['parent_slug'] = 'leadengine-dashboard';
      return $args;
  }

  public function keydesign_admin_bar( $wp_admin_bar ) {

		if ( !current_user_can( 'edit_theme_options' ) ) {
		    return;
		}

		//Add parent shortcut link
		$args = array(
			'id'    => 'leadengine-dashboard',
			'title' => 'LeadEngine',
			'href'  => admin_url( 'admin.php?page=leadengine-dashboard' ),
			'meta'  => array(
				'class' => 'leadengine-toolbar-page',
				'title' => 'LeadEngine Options',
			)
		);
		$wp_admin_bar->add_node( $args );

		//Add dashboard shortcut link
		$args = array(
			'id' => 'leadengine-admin',
			'title' => 'Dashboard',
			'href' => admin_url( 'admin.php?page=leadengine-dashboard' ),
			'parent' => 'leadengine-dashboard',
			'meta'  => array(
				'class' => 'leadengine-dashboard',
				'title' => 'Leadengine Dashboard',
			),
		);
		$wp_admin_bar->add_node( $args );

    //Add import-demos shortcut link
    $args = array(
			'id' => 'import-demos',
			'title' => 'Import Demos',
			'href' => admin_url( 'admin.php?page=import-demos' ),
			'parent' => 'leadengine-dashboard',
			'meta'  => array(
				'class' => 'import-demos',
				'title' => 'Import Demos',
			),
		);
		$wp_admin_bar->add_node( $args );

		//Add theme-options shortcut link
		if( class_exists( 'ReduxFrameworkPlugin' ) ) {
			$args = array(
				'id' => 'leadengine-theme-options',
				'title' => 'Theme Options',
				'href' => admin_url( 'admin.php?page=theme-options' ),
				'parent' => 'leadengine-dashboard',
				'meta'  => array(
					'class' => 'leadengine-theme-options',
					'title' => 'Theme Options',
				),
			);
			$wp_admin_bar->add_node( $args );
		}

    //Add install-required-plugins shortcut link
    $args = array(
			'id' => 'install-required-plugins',
			'title' => 'Install Plugins',
			'href' => admin_url( 'themes.php?page=install-required-plugins' ),
			'parent' => 'leadengine-dashboard',
			'meta'  => array(
				'class' => 'install-required-plugins',
				'title' => 'Install Plugins',
			),
		);
		$wp_admin_bar->add_node( $args );
	}

  public function display() {
		echo 'default';
	}

  function keydesign_remove_redux_demo() {
		if ( class_exists( 'ReduxFramework' ) ) {
			remove_filter( 'plugin_row_meta', array( ReduxFrameworkPlugin::instance(), 'plugin_metalinks' ), null, 2);
			remove_action( 'admin_notices', array( ReduxFrameworkPlugin::instance(), 'admin_notices' ) );
		}
	}
}
new KeyDesign_Admin;
