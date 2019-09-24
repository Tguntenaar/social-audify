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
include dirname(__FILE__). '/../../../../../wp-load.php';

// NIEUWE INCLUDES CHECK
include(dirname(__FILE__)."/../controllers/audit_controller.php");
include(dirname(__FILE__)."/../controllers/report_controller.php");
include(dirname(__FILE__)."/../controllers/client_controller.php");
include(dirname(__FILE__)."/../controllers/user_controller.php");

include(dirname(__FILE__)."/../services/connection.php");

include(dirname(__FILE__)."/../models/client.php");
include(dirname(__FILE__)."/../models/audit.php");
include(dirname(__FILE__)."/../models/report.php");
include(dirname(__FILE__)."/../models/user.php");

// new controllers @Daan
$connection = new connection;
$user_control = new user_controller($connection);

$audit_control  = new audit_controller($connection);
$report_control = new report_controller($connection);
$client_control = new client_controller($connection);

// HIER BEGINT DE CODE
$users = get_users();

foreach($users as $user_id) {
    $audit_send_list = array();

    if($user_id->ID == 1) {
        continue;
    }
    // Get config from users
    // $mail_data = $main_control->get_user_mail_config($user_id->ID);
    $mail_data = $user_control->get($user_id->ID);

    // Check if it is a socialaudify user
    if(isset($mail_data)) {

        // Check if mail fields are set
        if($mail_data->day_1 != 0 || $mail_data->day_2 != 0
           || $mail_data->day_3 != 0) {

            $audits = $audit_control->get_all(NULL, $user_id->ID);

            $i = 0;
            foreach($audits as $audit) {
                if($audit->mail_bit == 0) {
                    continue;
                }

                $client = $client_control->get($audit->client_id);
                $temp = array();
                $earlier = new DateTime($audit->create_date);
                $later = new DateTime(date('Y-m-d H:i:s'));
                $day_difference = $later->diff($earlier)->format("%a");

                // Check if audit is viewed and if we have to send a auto mail
                if($audit->view_time == NULL && (($day_difference == $mail_data->day_1) ||
                                                  ($day_difference == $mail_data->day_2) ||
                                                  ($day_difference == $mail_data->day_3))) {

                    $link = "https://www.socialaudify.com/public/audit-" . str_replace(' ', '-', $audit->name) . "-" . $audit->id;

                    $i++;
                    // Create mail body
                    if($day_difference == $mail_data->day_1) {
                        $body_string = str_replace("#{name}", $client->name, $mail_data->mail_text);

                    } else if($day_difference == $mail_data->day_2) {
                        $body_string = str_replace("#{name}", $client->name, $mail_data->second_mail_text);
                    } else {
                        $body_string = str_replace("#{name}", $client->name, $mail_data->third_mail_text);
                    }

                    $body_string = str_replace("\n", "<br />", $body_string);

                    $body_string .= "<br /><br />";
                    $body_string .= "Audit: " . $audit->name . ".<br />";
                    $body_string .= 'Link: <a href='. $link .' title="Audit link">' . $audit->name . "</a>.<br /><br />";

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
                        $mail->Port       = 465;                                    // TCP port to connect to

                        //Recipients
                        $mail->setFrom('automail@socialaudify.com', $user_id->display_name);
                        $mail->addAddress($client->mail, $client->name);     // Add a recipient              // Name is optional
                        $mail->addReplyTo($user_id->user_email, $user_id->display_name);

                         // Content
                         $mail->isHTML(true);                                  // Set email format to HTML
                         $mail->Subject = 'Hi, a reminder to open the audit we made for you!';
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
    }
}

?>
