<?php

/**
 * Template Name: Auto send mail
 */
?>

<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function send_mail_to($user, $client, $audit) {
  // Import PHPMailer classes into the global namespace
  // These must be at the top of your script, not inside a function

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
  
  $audit_control->update($audit->id, 'send_mail', 1, 'Audit');
  
  $link = "https://www.socialaudify.com/public/audit-" . str_replace(' ', '-', $audit->name) . "-" . $audit->id;
  $body_string = replace_template_mail_fields($user->initial_text, $client, $audit->name, $link);
  $subject = replace_template_mail_fields($user->subject_initial, $client, $audit->name, $link);
  $subject = $subject == "" ? 'Hi, we created an audit for you!' : $subject;
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
    $company = get_user_meta($user->id, 'rcp_company', true);
    $name = $company !== "" ? $company : $user->name;

    $mail->setFrom('automail@socialaudify.com', $name);
    $mail->addAddress($client->mail, $client->name);
    // $mail->addAddress($client->mail, $client->name);     // Add a recipient              // Name is optional
    $mail->addReplyTo($user->email, $name);

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
