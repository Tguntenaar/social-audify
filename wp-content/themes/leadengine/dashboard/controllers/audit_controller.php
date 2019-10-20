<?php
class audit_controller {

  public function __construct($connection = NULL) {
    include(dirname(__FILE__)."/../services/audit_service.php");
    $this->service = new audit_service($connection);
  }

  function create($page, $client, $options, $competitor) {
    $competitor_name = isset($competitor['name']) ? $competitor['name'] : NULL;

    $instance = new audit($this->service, (object) array(
      'client_id'       => $client['id'],
      'name'            => urldecode($page['name']),
      'create_date'     => date('Y-m-d H:i:s'),
      'post_id'         => 0,
      'facebook_bit'    => $options['facebook_checkbox'],
      'instagram_bit'   => $options['instagram_checkbox'],
      'website_bit'     => $options['website_checkbox'],
      'mail_bit'        => 0,
      'competitor_name' => $competitor_name,
    ));

    // create audit in database
    $instance->id = $this->service->create($instance->get_array_data());

    $this->service->insert_template($instance->id);
    $this->service->insert_visibility($instance->id);

    $slug = strtolower("audit-".str_replace(' ', '-', $instance->name)."-" . $instance->id);

    $new_post = array(
      'post_author' =>  get_current_user_id(),
      'post_title'  =>  $slug,
      'post_type'   => 'page',
      'post_status' => 'publish',
      'post_category' => array('3')
    );

    // create audit in wordpress databasse
    $post_id = wp_insert_post($new_post);
    update_post_meta($post_id, '_wp_page_template', '/dashboard/pages/page-templates/audit_page.php');

    // update audit object with newly created post id
    $instance->update('post_id', $post_id);

    $fb_data = json_encode($client['data']['facebook_data']);
    $ig_data = json_encode($client['data']['instagram_data']);

    $instance->insert_data($client['facebook'], $fb_data, $client['instagram'], $ig_data, 0, (int)$page['manual']);

    if ($competitor != 'false') {
      $fb_data_c = json_encode($competitor['data']['facebook_data']);
      $ig_data_c = json_encode($competitor['data']['instagram_data']);
      $instance->insert_data($competitor['facebook'], $fb_data_c, $competitor['instagram'], $ig_data_c, 1, (int)$page['competitor_manual']);
    }

    // Website stuff
    if ($options['website_checkbox'] && isset($client['id'])) {
      $instance->request_website_meta($client['website']);

      // Check if competitor is set.
      if ($competitor != 'false') {
        $instance->request_website_meta($competitor['website'], 1);
      }
    }

    return $slug;
  }


  function get($id) {
    // TODO: check if not false && dynamisch competitor ophalen/ decode
    $sql_audit = $this->service->get($id);
    $audit = new audit($this->service, $sql_audit[0]);
    $audit->get_competitor();
    $audit->decode_json();
    return $audit;
  }

  public function update($audit_id, $field_name, $value, $table = 'Audit', $comp = 0) {
    return $this->service->update($audit_id, $table, $field_name, $value, $comp);
  }

  function get_all($months = NULL, $user_id = NULL) {
    $user = $user_id == NULL ? get_current_user_id() : $user_id;
    $sql_audits = $this->service->get_all($user, date('Y-m-1', strtotime("-{$months} month")));

    $return_audits = array();
    foreach($sql_audits as $sql_audit) {
      array_unshift($return_audits, new audit($this->service, $sql_audit));
    }

    return $return_audits;
  }

  function get_all_audits() {
     return  $this->service->get_all_audits();
  }


  function get_amount($date = NULL, $user_id = NULL) {
    $user = $user_id == NULL ? get_current_user_id() : $user_id;
    return $this->service->get_amount($user, $date)[0]->count;
  }

  function check_website($id) {
    return $this->service->check_website($id);
  }

  public function get_id($post_id) {
    return $this->service->get_id($post_id)->id;
  }

  public function get_area_fields() {
    return explode(", ", $this->service->get_template_fields());
  }

  public function toggle_visibility($id, $field) {
    return $this->service->toggle_config_visibility($id, $field);
  }

  public function delete_multiple($id, $audits) {
    return $this->service->delete_multiple($id, $audits);
  }
}
?>
