<?php
session_start();

// Error messages
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// new includes
include(dirname(__FILE__)."/../../controllers/audit_controller.php");
include(dirname(__FILE__)."/../../controllers/report_controller.php");
include(dirname(__FILE__)."/../../controllers/client_controller.php");
include(dirname(__FILE__)."/../../controllers/user_controller.php");

include(dirname(__FILE__)."/../../services/connection.php");

include(dirname(__FILE__)."/../../models/client.php");
include(dirname(__FILE__)."/../../models/audit.php");
include(dirname(__FILE__)."/../../models/report.php");
include(dirname(__FILE__)."/../../models/user.php");
include(dirname(__FILE__)."/../../assets/php/global_regex.php");

// Execute
$user_id = get_current_user_id();
$wp_current_user = wp_get_current_user();

// new controllers @Daan
$connection = new connection;
$user_control = new user_controller($connection);

$audit_control  = new audit_controller($connection);
$report_control = new report_controller($connection);
$client_control = new client_controller($connection);

$number_of_audits  = $audit_control->get_amount();
$number_of_reports = $report_control->get_amount();
$number_of_clients = $client_control->get_amount();

/*
 * COMMON FUNCTIONS
 */
function calculate_monthly_amount($objects) {
  $monthly_amount = array_fill(0, 12, 0);
  foreach ($objects as $object) {
    $array_location = (date("m",strtotime($object->create_date)) - date("m") + 11) % 12;
    $monthly_amount[$array_location] += 1;
  }
  return $monthly_amount;
}

function calculate_daily_amount($objects) {
  $daily_amount = array_fill(0, date('t'), 0);
  foreach ($objects as $object) {
    $daily_amount[date("d", strtotime($object->create_date)) - 1] += 1;
  }
  return $daily_amount;
}

function percent_print($value, $alternative = "-") {
  return $value == 0 ? $alternative : $value."%";
}

function percent_diff($primary, $rest, $direct_print = false) {
  $result = $rest == 0 ? 0 : number_format(($primary / $rest) * 100, 1);
  return $direct_print ? percent_print($result) : $result;
}

function castToJSObject() {
  $jsObj = new StdClass;
  $args = func_get_args();
  $phpObj = array_pop($args);
  // rename the database property names to smaller versions
  $newName = array(
    'id' => 'id',
    'name' => 'name',
    'facebook' => 'fb',
    'instagram' => 'ig',
    'website' => 'wb',
    'mail' => 'ml',
    'ad_id' => 'ad_id'
  );
  foreach($args as $arg) {
    $jsObj->{$newName[$arg]} = $phpObj->{$arg};
  }
  return $jsObj;
}
?>
