<?php

/**
 * Template Name: Auto mailer
 */
?>

<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php';
include dirname(__FILE__) . '/../../../../../wp-load.php';

// NIEUWE INCLUDES CHECK
include(dirname(__FILE__) . "/../controllers/audit_controller.php");
include(dirname(__FILE__) . "/../controllers/report_controller.php");
include(dirname(__FILE__) . "/../controllers/client_controller.php");
include(dirname(__FILE__) . "/../controllers/user_controller.php");

include(dirname(__FILE__) . "/../services/connection.php");

include(dirname(__FILE__) . "/../models/client.php");
include(dirname(__FILE__) . "/../models/audit.php");
include(dirname(__FILE__) . "/../models/report.php");
include(dirname(__FILE__) . "/../models/user.php");

// new controllers @Daan
$connection = new connection;
$user_control = new user_controller($connection);

$audit_control  = new audit_controller($connection);
$report_control = new report_controller($connection);
$client_control = new client_controller($connection);

// HIER BEGINT DE CODE
$wp_users = get_users();

foreach ($wp_users as $wp_user) {

  if ($wp_user->ID == 1) {
    continue;
  }
  
  // Get config from users
  $mail_data = $user_control->get($wp_user->ID);
  $company = get_user_meta($wp_user->ID, 'rcp_company', true);
  $name = $company !== "" ? $company : $wp_user->display_name;
  $signature = wp_get_attachment_url($mail_data->signature);

  // Check if it is a socialaudify user
  if (!isset($mail_data)) {
    continue;
  }

  // Check if mail fields are set
  if ($mail_data->day_1 == 0 && $mail_data->day_2 == 0 && $mail_data->day_3 == 0) {
    continue;
  }

  // get all audits from past 4 months (day_3 max value is 90 days anyways)
  $audits = $audit_control->get_all(4, $wp_user->ID);

  foreach ($audits as $audit) {
    if ($audit->mail_bit == 0) {
      continue;
    }

    $client = $client_control->get($audit->client_id);
    
    $earlier = new DateTime($audit->create_date);
    $later = new DateTime(date('Y-m-d H:i:s'));
    $day_difference = $later->diff($earlier)->format("%a");

    // Check if audit is viewed and if we have to send a auto mail
    if ($audit->view_time == NULL && (($day_difference == $mail_data->day_1) || ($day_difference == $mail_data->day_2) || ($day_difference == $mail_data->day_3))) {

      $link = "https://www.socialaudify.com/public/audit-" . str_replace(' ', '-', $audit->name) . "-" . $audit->id;

      // Create mail body
      if ($day_difference == $mail_data->day_1) {
        $subject = $mail_data->subject_1;
        $body_string = $mail_data->mail_text_1;
      } else if ($day_difference == $mail_data->day_2) {
        $subject = $mail_data->subject_2;
        $body_string = $mail_data->mail_text_2;
      } else {
        $subject = $mail_data->subject_3;
        $body_string = $mail_data->mail_text_3;
      }

      $mail_controller = new mail_controller();
      $mail_controller->send($name, $wp_user->user_email, $client->name, $client->mail, $subject, $body_string, $signature, $audit->name, $link);
    }
  }
}
?>
