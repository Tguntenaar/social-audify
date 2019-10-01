<?php
class audit {
  private $service;
  private $sql_data;

  public $competitor;

  public $has_comp;
  public $has_website;

  public function __construct($service, $sql_audit) {
    // Error messages
    error_reporting(E_ALL);
    ini_set("display_errors", 1);

    $this->service = $service;
    $this->sql_data = $sql_audit;

	  $this->competitor = (object) array();
    $this->has_comp = false;

    $this->has_website = isset($this->sql_data->website_size);
  }


  // Anonymous parameter fetch
  public function __get($name) {
    return $this->sql_data->$name;
  }

  // Anonymous parameter set
  public function __set($name, $value) {
    $this->sql_data->$name = $value;
  }


  // Return all sql data
  public function get_array_data() {
    return (array) $this->sql_data;
  }


  // TODO: Dit moet dynamischer kunnen...
  public function get_competitor() {
    $result = $this->service->get_competitor($this->id);
    if ($this->has_comp = !empty($result)) {
      $this->competitor = $result[0];
    }
  }

  // TODO: Dit moet ook dynamischer kunnen...
  public function decode_json() {
    $this->sql_data->facebook_data = json_decode($this->sql_data->facebook_data);
    $this->sql_data->instagram_data = json_decode($this->sql_data->instagram_data);

    if ($this->has_comp) {
      $this->competitor->facebook_data = json_decode($this->competitor->facebook_data);
      $this->competitor->instagram_data = json_decode($this->competitor->instagram_data);
    }
  }


  // Update Website Meta
  public function request_website_meta($website_url, $competitor = 0) {
    $user_id = get_current_user_id();
    $key = md5("t harum quidem rerum facilis" . $this->id . "est et expedita distinctio.");
    $post_url = htmlentities(base64_encode($website_url));

    if($_SERVER['SERVER_NAME'] == "dev.socialaudify.com") {

        $ch = curl_init("http://crawl.socialaudify.com/api/$this->id/$competitor/$post_url/$key/$user_id");

    } else {
        $ch = curl_init("http://136.144.132.69/api/$this->id/$competitor/$post_url/$key/$user_id");
    }

    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
    curl_setopt($ch, CURLOPT_NOSIGNAL, 1);

    $response = curl_exec($ch);

    curl_close($ch);
  }


  public function insert_data($fb_name, $fb_data, $ig_name, $ig_data, $comp = 0, $manual = 0) {
    if ($comp === 0) {
      $this->sql_data->facebook_name = $fb_name;
      $this->sql_data->facebook_data = $fb_data;
      $this->sql_data->instagram_name = $ig_name;
      $this->sql_data->instagram_data = $ig_data;
    }
    else {
      $this->competitor->facebook_name = $fb_name;
      $this->competitor->facebook_data = $fb_data;
      $this->competitor->instagram_name = $ig_name;
      $this->competitor->instagram_data = $ig_data;
    }
    return $this->service->insert_data($this->id, $fb_name, $fb_data, $ig_name, $ig_data, $comp, $manual);
  }


  public function update($field_name, $value, $table = 'Audit', $comp = 0) {
    $this->sql_data->$field_name = $value;
    return $this->service->update($this->id, $table, $field_name, $value, $comp);
  }

  // Flips the current post status
  public function change_post_status() {
    $current_status = get_post_meta($this->post_id, '_wp_page_template');
    if (substr($current_status[0], -strlen('audit_page.php')) === 'audit_page.php') {
      update_post_meta($this->id, '_wp_page_template', '/dashboard/pages/page-templates/stopped.php');
		}
		else {
			update_post_meta($this->id, '_wp_page_template', '/dashboard/pages/page-templates/audit_page.php');
		}
  }


  public function delete() {
    wp_delete_post($this->post_id);
    $this->service->delete($this->id);
  }
}
?>
