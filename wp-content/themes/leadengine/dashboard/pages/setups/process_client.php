<?php
/**
 * Template Name: process client
 */
?>

<?php
  include(dirname(__FILE__)."/../../assets/php/global_regex.php");
  include(dirname(__FILE__)."/../../services/connection.php");
  include(dirname(__FILE__)."/../../controllers/client_controller.php");
  include(dirname(__FILE__)."/../../models/client.php");

  $Regex = new Regex;

  $connection = new connection;
  $client_control = new client_controller($connection);

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $fb = sanitize_text_field($_POST['facebook_url']);
    $ig = sanitize_text_field($_POST['instagram_url']);
    $wb = sanitize_text_field($_POST['website_url']);
    
    $name = sanitize_text_field($_POST['client_name']);
    $mail = sanitize_email($_POST['client_mail']);
    
    if ($name != "" && $mail != "" && 
      ($Regex->valid_fb($fb) && $Regex->valid_ig($ig) && $Regex->valid_wb($wb))) {

      if (isset($_GET['redirect']) && isset($_POST)) {
        if ($id = $client_control->create($name, $fb, $ig, $wb, $mail)) {
          header("Location: https://".getenv('HTTP_HOST')."/".$_GET['redirect']."?cid=".$id, true, 303);
        } else {
          header("Location: javascript://history.go(-1)", true, 303);
        }
      } elseif (isset($_GET['edit']) && isset($_POST)) {
  
        $client_id = $_POST['client_id'];
        $client = $client_control->get($client_id);
  
        $ad_id = isset($_POST['ad_id']) ? sanitize_text_field($_POST['ad_id']) : "";

        if ($client->update_all($name, $fb, $ig, $wb, $mail, $ad_id)) {
          header('Location: https://'.getenv('HTTP_HOST').'/client-dashboard/', true, 303);
        } else {
          header("Location: https://".getenv('HTTP_HOST')."/dashboard/", true, 303);
        }
      }
    }
  } else {
    header("HTTP/1.0 404 Not Found", true, 404);
    include(dirname(__FILE__)."/../../../404.php");
  }
  exit();
?>