<?php
session_start();
// Error Logging
include(dirname(__FILE__)."/../../controllers/log_controller.php");
$ErrorLogger = new Logger;

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
include(dirname(__FILE__)."/../../models/signature.php");

// PHP Regex
include(dirname(__FILE__)."/../../assets/php/global_regex.php");
$Regex = new Regex;

// Cache busting
include(dirname(__FILE__)."/../../assets/php/cache_version.php");

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

function percent_tuple($val, $val2) {
  if ($val > $val2) {
    return array("100", (string)percent_diff($val2, $val));
  }
  return array((string)percent_diff($val, $val2), "100");
}

function make_slug($type, $name, $id) {
  return strtolower('/'.$type.'-'.sanitize_title($name).'-'.$id.'/');
}
?>
