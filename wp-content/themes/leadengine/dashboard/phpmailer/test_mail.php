<?php

class mail_controller {
  
}
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
$wp_user = get_current_user();

// Get config from users
$user = $user_control->get($wp_user->ID);
$company = get_user_meta($wp_user->ID, 'rcp_company', true);
$name = $company !== "" ? $company : $wp_user->display_name;

$audit_name = "<example audit name>";
$client = "<example client name>";

$link = "https://www.socialaudify.com/public/audit-config";

// Create mail body
if ($send_mail == 1) {
  $subject = replace_template_mail_fields($user->subject_1, $client, $audit_name, $link);
  $body_string = replace_template_mail_fields($user->mail_text, $client, $audit_name, $link);
} else if ($send_mail == 2) {
  $subject = replace_template_mail_fields($user->subject_1, $client, $audit_name, $link);
  $body_string = replace_template_mail_fields($user->second_mail_text, $client, $audit_name, $link);
} else {
  $subject = replace_template_mail_fields($user->subject_3, $client, $audit_name, $link);
  $body_string = replace_template_mail_fields($user->third_mail_text, $client, $audit_name, $link);
}

$subject = $subject == "" ? 'Hi, here is a reminder to open the audit we made for you!' : $subject;
$body_string = str_replace("\n", "<br />", $body_string);

$body_string .= '<br /><br />Link: <a href=' . $link . ' title="Audit link">' . $audit->name . "</a>.<br /><br />";

send_mail($name, $wp_user->user_email, $client, $wp_user->user_email, $subject, $body_string);



function send_mail($sender_name, $sender_email, $recipient_name, $recipient_email, $subject, $body) {
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
    $mail->setFrom('automail@socialaudify.com', $sender_name);
    $mail->addAddress($recipient_email, $recipient_name);     // Add a recipient              // Name is optional
    $mail->addReplyTo($sender_email, $sender_name);

    // Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = $subject;
    $mail->Body    = $body;

    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    echo 'Message has been sent';
  } catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
}

function replace_template_mail_fields($string, $client, $audit, $link) {
  $a = str_replace("#{name}", $client, $string);
  $b = str_replace("#{audit}", $audit, $a);
  $str =  "<a href='{$link}' title='Audit link'>{$audit}</a>";
  $c = str_replace("#{auditlink}", $str, $b);
  // add more fields
  // $d = str_replace("#{company}", $company, $c);
  return $c;
}

?>
