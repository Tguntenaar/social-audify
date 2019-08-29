<?php
/**
 * Template Name: Subscription test
 */


include(dirname(__FILE__)."/../controllers/audit_controller_V2.php");
include(dirname(__FILE__)."/../controllers/report_controller_V2.php");
include(dirname(__FILE__)."/../controllers/client_controller_V2.php");
include(dirname(__FILE__)."/../controllers/user_controller.php");

include(dirname(__FILE__)."/../services/connection.php");

include(dirname(__FILE__)."/../models/client.php");
include(dirname(__FILE__)."/../models/audit.php");
include(dirname(__FILE__)."/../models/report.php");
include(dirname(__FILE__)."/../models/user.php");

// new controllers @Daan
$connection = new connection;
$user_control = new user_controller($connection);

$audit_control  = new audit_controller_v2($connection);
$report_control = new report_controller_v2($connection);
$client_control = new client_controller_v2($connection);

// require_once(dirname(__FILE__)."/../controllers/main_controller.php");
// require_once(dirname(__FILE__)."/../controllers/audit_controller.php");
//
//
// $main_control = new main_controller;
// $audit_control = new audit_controller($main_control);

$users = get_users( array( 'fields' => array( 'ID', 'display_name' ) ) );

foreach($users as $user_id){
      // echo $user_id->ID .": ".rcp_get_subscription( $user_id->ID ) . " -- " . $user_id->display_name . " -- " . rcp_user_has_access( $user_id->ID, "1" ) . "<br />";

      if(!rcp_user_has_access( $user_id->ID, "1" )) {
          $audits = $audit_control->get_all(NULL, $user_id->ID);
          foreach($audits as $audit) {
              echo "test";
              update_post_meta($audit->post_id, '_wp_page_template', '/dashboard/pages/page-templates/stopped.php');
           }
      } else {
         $audits = $audit_control->get_all(NULL, $user_id->ID);
         foreach($audits as $audit) {
           update_post_meta($audit->post_id, '_wp_page_template', '/dashboard/pages/page-templates/audit_page.php');
         }
      }


      // $audits = $audit_control->get_audits(2, NULL, NULL, $user_id->ID);



}

?>
