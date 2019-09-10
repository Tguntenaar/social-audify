<?php
class report_controller {

  public function __construct($connection = NULL) {
    // Error messages
    error_reporting(E_ALL);
    ini_set("display_errors", 1);

    include(dirname(__FILE__)."/../services/report_service.php");
    $this->service = new report_service($connection);
  }


  function create($name, $client, $options, $competitor, $manual, $currency) {
    $client_id = $client['id'];
    $social_stats = json_encode($client['data']);
    $chart_data =  json_encode($client['chart_data']);

    // new instance
    $instance = new report($this->service, (object) array(
      'client_id'     => $client_id,
      'name'          => $name,
      'create_date'   => date('Y-m-d H:i:s'),
      'post_id'       => 0,
      'facebook_bit'  => $options['facebook_checkbox'],
      'instagram_bit' => $options['instagram_checkbox']
    ));

    // create report in database
    $instance->id = $this->service->create($instance->get_array_data());

    // $this->service->insert_content($instance->id);
    $this->service->insert_visibility($instance->id);

    //create slug
    $slug = strtolower("report-".str_replace(' ', '-', $instance->name)."-".$instance->id);

    $new_post = array(
      'post_author' =>  get_current_user_id(),
      'post_title'  =>  $slug,
      'post_type'   => 'page',
      'post_status' => 'publish',
      'post_category' => array('3')
    );

    // create report in wordpress database
    $post_id = wp_insert_post($new_post);
    update_post_meta($post_id, '_wp_page_template', '/dashboard/pages/page-templates/report_page.php');

    // update report object with newly created post id
    $instance->update('post_id', $post_id);

    // insert the new data
    $instance->insert_data($social_stats, $chart_data, 0, (int)$manual, $currency, $client['instagram']);

    if ($competitor != 'false') {
      $compare_report_id = (int)$competitor['id'];
      $compare = $this->get($compare_report_id);
      $instance->insert_data($compare->social_stats, $compare->chart_data, 1, (int)$manual);
    }

    return $slug;
  }


  function get($id) {
    // TODO: check if not false
    $sql_report = $this->service->get($id);
    return new report($this->service, $sql_report[0]);
  }


  function get_all($months = NULL, $user_id = NULL) {
    $user = $user_id == NULL ? get_current_user_id() : $user_id;
    $sql_reports = $this->service->get_all($user, date('Y-m-1', strtotime("-{$months} month")));

    $return_reports = array();
    foreach($sql_reports as $sql_report) {
      array_unshift($return_reports, report::initiate($this->service, $sql_report));
    }

    return $return_reports;
  }

  function get_all_reports() {
     return $this->service->get_all_reports();
  }

  function get_amount($date = NULL, $user_id = NULL) {
    $user = $user_id == NULL ? get_current_user_id() : $user_id;
    return $this->service->get_amount($user, $date)[0]->count;
  }

  public function get_id($page_id) {
    return $this->service->get_id($page_id)->id;
  }

  public function update_template($report_id, $field_name, $value) {
    return $this->service->update_template($report_id, $field_name, $value);
  }
}
?>
