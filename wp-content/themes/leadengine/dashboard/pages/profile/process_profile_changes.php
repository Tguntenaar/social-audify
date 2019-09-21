<?php
/**
 * Template Name: process profile changes
 */
?>

<?php
  error_reporting(E_ALL);
  ini_set("display_errors", 1);

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include(dirname(__FILE__)."/../../services/connection.php");
    include(dirname(__FILE__)."/../../controllers/user_controller.php");
    include(dirname(__FILE__)."/../../models/user.php");

    $connection = new connection;
    $user_control = new user_controller($connection);
    
    $user = $user_control->get(get_current_user_id());

    $post_list = array(
      'introduction-audit' => 'intro_audit',
      'conclusion-audit' => 'conclusion_audit',
      'introduction-report' => 'intro_report',
      'conclusion-report' => 'conclusion_report',
      'range_fb_1' => 'range_number_fb_1',
      'range_fb_2' => 'range_number_fb_2',
      'fb-audit_1' => 'text_fb_1',
      'fb-audit_2' => 'text_fb_2',
      'fb-audit_3' => 'text_fb_3',
      'range_ig_1' => 'range_number_insta_1',
      'range_ig_2' => 'range_number_insta_2',
      'ig-audit_1' => 'text_insta_1',
      'ig-audit_2' => 'text_insta_2',
      'ig-audit_3' => 'text_insta_3',
      'range_wb_1' => 'range_number_website_1',
      'range_wb_2' => 'range_number_website_2',
      'wb-audit_1' => 'text_website_1',
      'wb-audit_2' => 'text_website_2',
      'wb-audit_3' => 'text_website_3',
    );

    $post_list_mail = array(
      'day_1' => 'day_1',
      'day_2' => 'day_2',
      'day_3' => 'day_3',
      'mail_text' => 'mail_text',
      'second_mail_text' => 'second_mail_text',
      'third_mail_text' => 'third_mail_text',
    );

    // TODO ; meer input validation    
    function check_input_valid($input) {
      if (is_int($input) && absint($input) >= 0 && absint($input) <= 365) {
        return true;
      }
      return trim($input) <= 999;
    }

    function fill_values_list($post_list, $user, $output) {
      foreach ($post_list as $post_name => $db_field) {
        if (isset($_POST[$post_name])) {
          $value = $_POST[$post_name];

          if (check_input_valid($value) && $value != $user->{$db_field}) {
            $output[$db_field] = $value;
          }
        }
      }
      return $output;
    }

    $values_list = fill_values_list($post_list, $user, array());
    if (count($values_list) > 0) {
      $user->update_list('Configtext', $values_list);
    }

    $values_list_mail = fill_values_list($post_list_mail, $user, array());
    if (count($values_list_mail) > 0) {
      $user->update_list('Mail_config', $values_list_mail);
    }

    function update_visibility($visibility_list, $type, $user) {
      foreach ($visibility_list as $field => $value) {
        if (isset($_POST["check-${field}"]) && (int)$_POST["check-${field}"] != (int)$value) {
          $user->toggle_visibility($field, $type);
        }
      }
    }

    update_visibility($user->get_visibility('audit')[0], 'audit', $user);
    update_visibility($user->get_visibility('report')[0], 'report', $user);

    $current_section = isset($_GET['settings']) ? "/#{$_GET['settings']}-settings" : "";
    header("Location: https://".getenv('HTTP_HOST')."/profile-page".$current_section, true, 303);
  } else {
    include(dirname(__FILE__)."/../../../404.php");
    header("HTTP/1.0 404 Not Found", true, 404);
  }
  // exit();
?>
