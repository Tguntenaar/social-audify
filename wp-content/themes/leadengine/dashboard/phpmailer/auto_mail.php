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
$users = get_users();

foreach ($users as $user) {
  $audit_send_list = array();

  if ($user->ID == 1) {
    continue;
  }
  // Get config from users
  // $mail_data = $main_control->get_user_mail_config($user->ID);
  $mail_data = $user_control->get($user->ID);

  // Check if it is a socialaudify user
  if (!isset($mail_data)) {
    continue;
  }

  // Check if mail fields are set
  if ($mail_data->day_1 == 0 && $mail_data->day_2 == 0 && $mail_data->day_3 == 0) {
    continue;
  }

  // get all audits from past 4 months (day_3 max value is 90 days anyways)
  $audits = $audit_control->get_all(4, $user->ID);

  foreach ($audits as $audit) {
    if ($audit->mail_bit == 0) {
      continue;
    }

    $client = $client_control->get($audit->client_id);
    $company = get_user_meta($user->ID, 'rcp_company', true);
    $earlier = new DateTime($audit->create_date);
    $later = new DateTime(date('Y-m-d H:i:s'));
    $day_difference = $later->diff($earlier)->format("%a");

    // Check if audit is viewed and if we have to send a auto mail
    if ($audit->view_time == NULL && (($day_difference == $mail_data->day_1) || ($day_difference == $mail_data->day_2) || ($day_difference == $mail_data->day_3))) {

      $link = "https://www.socialaudify.com/public/audit-" . str_replace(' ', '-', $audit->name) . "-" . $audit->id;

      // Create mail body
      if ($day_difference == $mail_data->day_1) {
        $subject = replace_template_mail_fields($mail_data->subject_1, $client, $audit->name, $link);
        $body_string = replace_template_mail_fields($mail_data->mail_text, $client, $audit->name, $link);
      } else if ($day_difference == $mail_data->day_2) {
        $subject = replace_template_mail_fields($mail_data->subject_1, $client, $audit->name, $link);
        $body_string = replace_template_mail_fields($mail_data->second_mail_text, $client, $audit->name, $link);
      } else {
        $subject = replace_template_mail_fields($mail_data->subject_3, $client, $audit->name, $link);
        $body_string = replace_template_mail_fields($mail_data->third_mail_text, $client, $audit->name, $link);
      }

      $subject = $subject == "" ? 'Hi, here is a reminder to open the audit we made for you!' : $subject;
      $body_string = str_replace("\n", "<br />", $body_string);

      $body_string .= '<br /><br />Link: <a href=' . $link . ' title="Audit link">' . $audit->name . "</a>.<br /><br />";

      // Instantiation and passing `true` enables exceptions
      $mail = new PHPMailer(true);

      try {
        //Server settings
        $mail->SMTPDebug = 0;                                       // Enable verbose debug output
        $mail->isSMTP();                                            // Set mailer to use SMTP
        $mail->Host       = 'smtp.transip.email';                   // Specify main and backup SMTP servers
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = 'socialaudify@vps.transip.email';       // SMTP username
        $mail->Password   = 'XQhkUjNxqxBsaZrq';                     // SMTP password
        $mail->SMTPSecure = 'ssl';                                  // Enable TLS encryption, `ssl` also accepted
        $mail->Port       = 465;
        $mail->CharSet    = 'UTF-8';                                  // TCP port to connect to

        //Recipients  
        $company = get_user_meta($user->ID, 'rcp_company', true);
        $name = $company !== "" ? $company : $user->display_name;

        $mail->setFrom('automail@socialaudify.com', $name);
        $mail->addAddress($client->mail, $client->name);     // Add a recipient              // Name is optional
        $mail->addReplyTo($user->user_email, $name);

        // Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $body_string;

        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $mail->send();
        echo 'Message has been sent';
      } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
      }
    }
  }
}

function replace_template_mail_fields($string, $client, $audit, $link) {
  $a = str_replace("#{name}", $client->name, $string);
  $b = str_replace("#{audit}", $audit, $a);
  $str =  "<a href='{$link}' title='Audit link'>{$audit}</a>";
  $c = str_replace("#{auditlink}", $str, $b);
  // add more fields
  // $d = str_replace("#{company}", $company, $c);
  return $c;
}

?>
