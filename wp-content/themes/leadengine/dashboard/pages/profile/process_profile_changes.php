<?php
/**
 * Template Name: process profile changes
 */
?>

<?php
  error_reporting(E_ALL);
  ini_set("display_errors", 1);

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = get_current_user_id();
    include(dirname(__FILE__)."/../../services/connection.php");
    include(dirname(__FILE__)."/../../controllers/user_controller.php");
    include(dirname(__FILE__)."/../../models/user.php");

    $connection = new connection;
    $user_control = new user_controller($connection);
    
    $user = $user_control->get($user_id);

    $audit_visibility_stat = $user->get_visibility('audit');
    $report_visibility_stat = $user->get_visibility('report');

    function if_set_update_int($user, $post_field, $db_field, $table) {
      $absoluteInt = absint($_POST[$post_field]);
      if (isset($_POST[$post_field]) && ($absoluteInt >= 0 && $absoluteInt <= 365)) {
        $user->update($table, $db_field, absint($_POST[$post_field]));
      }
    }

    function if_set_update_textfield($user, $post_field, $db_field, $table) {
      if (isset($_POST[$post_field]) && trim($_POST[$post_field]) <= 999) {
        $user->update($table, $db_field, sanitize_textarea_field(trim($_POST[$post_field])));
      }
    }

    /**
     * Facebook
     */
    if_set_update_textfield($user, 'introduction-audit', 'intro_audit', 'Configtext');
    if_set_update_textfield($user, 'conclusion-audit', 'conclusion_audit', 'Configtext');

    if_set_update_textfield($user, 'introduction-report', 'intro_report', 'Configtext');
    if_set_update_textfield($user, 'conclusion-report', 'conclusion_report', 'Configtext');


    if_set_update_int($user, 'range_fb_1', 'range_number_fb_1', 'Configtext');
    if_set_update_int($user, 'range_fb_2', 'range_number_fb_2', 'Configtext');

    if_set_update_textfield($user, 'fb-audit_1', 'text_fb_1', 'Configtext');
    if_set_update_textfield($user, 'fb-audit_2', 'text_fb_2', 'Configtext');
    if_set_update_textfield($user, 'fb-audit_3', 'text_fb_3', 'Configtext');

    /**
     * Instagram
     */
    if_set_update_int($user, 'range_ig_1', 'range_number_insta_1', 'Configtext');
    if_set_update_int($user, 'range_ig_2', 'range_number_insta_2', 'Configtext');

    if_set_update_textfield($user, 'ig-audit_1', 'text_insta_1', 'Configtext');
    if_set_update_textfield($user, 'ig-audit_2', 'text_insta_2', 'Configtext');
    if_set_update_textfield($user, 'ig-audit_3', 'text_insta_3', 'Configtext');

    /**
     * Website
     */
    if_set_update_int($user, 'range_website_1', 'range_number_website_1', 'Configtext');
    if_set_update_int($user, 'range_website_2', 'range_number_website_2', 'Configtext');

    if_set_update_textfield($user, 'wb-audit_1', 'text_website_1', 'Configtext');
    if_set_update_textfield($user, 'wb-audit_2', 'text_website_2', 'Configtext');
    if_set_update_textfield($user, 'wb-audit_3', 'text_website_3', 'Configtext');

    /**
     * Mail
     */
    if_set_update_int($user, 'day_1', 'day_1', 'Mail_config');
    if_set_update_int($user, 'day_2', 'day_2', 'Mail_config');
    if_set_update_int($user, 'day_3', 'day_3', 'Mail_config');

    if_set_update_textfield($user, 'mail_text', 'mail_text', 'Mail_config');
    if_set_update_textfield($user, 'second_mail_text', 'second_mail_text', 'Mail_config');
    if_set_update_textfield($user, 'third_mail_text', 'third_mail_text', 'Mail_config');

    foreach ($audit_visibility_stat[0] as $field => $value) {
      if (isset($_POST["check-${field}"])) {
        if ((int)$_POST["check-${field}"] xor (int)$value) {
          $user->toggle_visibility($field, 'audit');
        }
      }
    }

    foreach ($report_visibility_stat[0] as $field => $value) {
      if (isset($_POST["check-${field}"])) {
        if ((int)$_POST["check-${field}"] xor (int)$value) {
          $user->toggle_visibility($field, 'report');
        }
      }
    }

    if (true) {
      if (isset($_GET['settings'])) {
        header("Location: https://".getenv('HTTP_HOST')."/profile-page/#".$_GET['settings']."-settings", true, 303);
      } else {
        header("Location: https://".getenv('HTTP_HOST')."/profile-page", true, 303);
      }
      exit();
    }
  } else {
    header("HTTP/1.0 404 Not Found", true, 404);
    include(dirname(__FILE__)."/../../../404.php");
  }
  exit();
?>
