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
    
    $post_list_mail = array(
      'day_1'             => 'day_1',
      'day_2'             => 'day_2',
      'day_3'             => 'day_3',
      'mail_text_1'       => 'mail_text_1',
      'mail_text_2'       => 'mail_text_2',
      'mail_text_3'       => 'mail_text_3',
      'subject_1'    => 'subject_1',
      'subject_2'    => 'subject_2',
      'subject_3'    => 'subject_3',
    );

    // TODO ; meer input validation    
    function check_input_valid($input) {
      if (is_int($input) && absint($input) >= 0 && absint($input) <= 365) {
        return true;
      }
      return trim($input) <= 999;
    }

    // TODO: if sanitized color is null dan standaard color sanitize_hex_color()
    function fill_values_list($post_list, $user, $output) {
      foreach ($post_list as $post_name => $db_field) {
        if (isset($_POST[$post_name])) {
          $value = $_POST[$post_name];

          if (check_input_valid($value) && $value != $user->{$db_field}) {
            $output[$db_field] = sanitize_textarea_field(stripslashes($value));
          }
        }
      }
      return $output;
    }

    $values_list_mail = fill_values_list($post_list_mail, $user, array());
    if (count($values_list_mail) > 0) {
      $user->update_list('Mail_config', $values_list_mail);
    }

    $current_section = isset($_GET['settings']) ? "/#{$_GET['settings']}-settings" : "";
    header("Location: https://".getenv('HTTP_HOST')."/profile-page".$current_section, true, 303);
  } else {
    include(dirname(__FILE__)."/../../../404.php");
    header("HTTP/1.0 404 Not Found", true, 404);
  }
?>
