<?php

require 'vendor/autoload.php';
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// TODO:
/**  
 * Load Composer's autoloader
 * require 'vendor/autoload.php';
 * include dirname(__FILE__) . '/../../../../../wp-load.php';
 */

 class mail_controller {
  
  public function __construct() {
    // Instantiation and passing `true` enables exceptions
    $this->mailer = new PHPMailer(true);
  }

  function send($sender_name, $sender_email, $recipient_name, $recipient_email, 
    $subject, $body, $signature = false, $audit_name = "", $audit_link = "") {

    try {
      $subject = $subject == "" ? 'Hi, here is a reminder to open the audit we made for you!' : $subject;

      $body_html = str_replace("\n", "<br />", $body);
      $body_html .= "<br /><br />Link: <a href='{$audit_link}' title='Audit link'>{$audit_name}</a><br /><br />";

      $subject = $this->replace_template_fields($subject, $recipient_name, $audit_name, $audit_link);
      $body_html = $this->replace_template_fields($body_html, $recipient_name, $audit_name, $audit_link);
      $body = $this->replace_template_fields($body, $recipient_name, $audit_name, $audit_link, false);

      //Server settings
      $this->mailer->SMTPDebug = 0;                                       // Enable verbose debug output
      $this->mailer->isSMTP();                                            // Set mailer to use SMTP
      $this->mailer->Host       = 'smtp.transip.email';                   // Specify main and backup SMTP servers
      $this->mailer->SMTPAuth   = true;                                   // Enable SMTP authentication
      $this->mailer->Username   = 'socialaudify@vps.transip.email';       // SMTP username
      $this->mailer->Password   = 'XQhkUjNxqxBsaZrq';                     // SMTP password
      $this->mailer->SMTPSecure = 'ssl';                                  // Enable TLS encryption, `ssl` also accepted
      $this->mailer->Port       = 465;
      $this->mailer->CharSet    = 'UTF-8';                                // TCP port to connect to
      $this->mailer->setFrom('automail@socialaudify.com', $sender_name);  // Name is optional
      $this->mailer->addAddress($recipient_email, $recipient_name);       // Add a recipient
      $this->mailer->addReplyTo($sender_email, $sender_name);

      // Content
      $this->mailer->isHTML(true);                                  // Set email format to HTML
      $this->mailer->Subject = $subject;
      $this->mailer->Body    = $body_html;
      $this->mailer->AltBody = $body + "\n\n" + $audit_link;

      // Signature & Send
      $this->add_signature($signature);
      $this->add_link();
      $this->mailer->send();
      
    } catch (Exception $e) {
      return "Message could not be sent. Mailer Error: {$this->mailer->ErrorInfo}";
    }
    return 1;
  }

  function replace_template_fields($string, $client_name, $audit_name, $audit_link, $isHtml = true) {
    $a = str_replace("#{name}", $client_name, $string);
    $b = str_replace("#{audit}", $audit_name, $a);

    $link_tag = "<a href='{$audit_link}' title='Audit link'>{$audit_name}</a>";
    $c = str_replace("#{auditlink}", $isHtml ? $link_tag : $audit_link, $b);

    // add more fields
    // $d = str_replace("#{company}", $company, $c);

    return $c;
  }

  /**
   * Dit ook voor Facebook Instagram LinkedIn
   */
  function add_link() {
    $body = $this->mailer->Body;
    $new_body = preg_replace('/#{link}{(.*)}{(.*)}/', "<a href='$2'>$1</a>", $body);
    $this->mailer->Body = $new_body;
  }
  
  function add_signature($signature) {
    
    // TODO: maybe..? : https://stackoverflow.com/questions/3708153/send-email-with-phpmailer-embed-image-in-body
    // $mail->AddEmbeddedImage(filename, cid, name);
    // $mail->AddEmbeddedImage('my-photo.jpg', 'my-photo', 'my-photo.jpg ');
    // $mail->AddEmbeddedImage("rocks.png", "my-attach", "rocks.png");
    // $mail->Body = 'Embedded Image: <img alt="Signature" src="https://www.socialaudify.com/wp-content/uploads/signatures/3_signature.jpg"> Here is an image!';

    if ($signature) {
      $body = $this->mailer->Body;
      $new_body = str_replace("#{signature}", "<img alt='Signature' width='250' src='{$signature}'/>", $body);
      $this->mailer->Body = $new_body;
    }
  }
}
?>