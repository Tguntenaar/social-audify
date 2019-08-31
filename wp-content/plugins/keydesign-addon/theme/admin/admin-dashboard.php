<?php
/**
* KeyDesign Theme Admin Panel
* The KeyDesign_Admin_Dashboard base class
*/

if( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class KeyDesign_Admin_Dashboard extends KeyDesign_Admin_Page {

	public function __construct() {

		$this->id = 'leadengine-dashboard';
		$this->page_title = 'LeadEngine Dashboard';
		$this->menu_title = 'LeadEngine';
		$this->position = '50';

		parent::__construct();
	}

	public function display() {
		include_once( plugin_dir_path( __FILE__ ).'views/keydesign-dashboard.php' );
	}
}
new KeyDesign_Admin_Dashboard;
