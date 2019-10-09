<?php
class report {
  private $service;
  private $sql_data;

  public $has_comp;

  public function __construct($service, $sql_report) {
    $this->service = $service;
    $this->sql_data = $sql_report;

    $this->has_comp = false;
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

  // Create report from sql data
  public static function initiate($service, $sql_report) {
    $instance = new self($service, $sql_report);
    $instance->sql_data = $sql_report;
    return $instance;
  }


  // Updates database record and current instance
  public function update($field_name, $value, $table = 'Report') {
    $this->sql_data->$field_name = $value;
    return $this->service->update($this->id, $table, $field_name, $value);
  }


  // Flips the current post status
  public function change_post_status() {
    $current_status = get_post_meta($this->post_id, '_wp_page_template');
    if (substr($current_status[0], -strlen('report_page.php')) === 'report_page.php') {
      update_post_meta($this->id, '_wp_page_template', '/dashboard/pages/page-templates/stopped.php');
		}
		else {
			update_post_meta($this->id, '_wp_page_template', '/dashboard/pages/page-templates/report_page.php');
		}
  }

  public function insert_data($social_stats, $chart_data, $competitor = 0, $manual, $currency = NULL, $instagram_name = NULL) {
    if ($competitor == 0) {
      $this->sql_data->social_stats = $social_stats;
      $this->sql_data->chart_data = $chart_data;
      return $this->service->insert_data($this->id, $social_stats, $chart_data, $competitor, $manual, $currency, $instagram_name);
    }

    if ($competitor == 1) {
      $this->service->update($this->id, 'Report_content', 'social_stats_compare', $social_stats);
      $this->service->update($this->id, 'Report_content',  'chart_data_compare', $chart_data);
    }
  }

  public function delete() {
    wp_delete_post($this->post_id);
    $this->service->delete($this->id);
  }
}
?>
