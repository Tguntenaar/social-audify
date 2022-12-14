<?php
class user_service extends connection {
  /*
   * Uses the exsisting database connection.
   */
  public function __construct($connection) {
    $this->dbwp = $connection->dbwp;
  }


  public function create($user_id, $name, $email) {
    $this->dbwp->insert('User', array(
      'id' => $user_id,
      'name' => $name,
      'email' => $email,
    ));

    return $this->dbwp->insert_id;
  }


  public function create_config($user_id) {
    $this->dbwp->insert('Configtext', array(
      'user_id' => $user_id,
      'color_audit' => '#6e9d9c',
      'color_report' => '#6e9d9c',
    ));
  }


  public function create_stat_visibility($user_id) {
    $this->dbwp->insert('User_audit_visibility', array('user_id' => $user_id));
    $this->dbwp->insert('User_report_visibility', array('user_id' => $user_id));
  }


  public function create_mail_config($user_id) {
    $this->dbwp->insert('Mail_config', array(
      'user_id' => $user_id,
      'day_1' => 3,
      'day_2' => 5,
      'day_3' => 7
    ));
  }


  public function get($user_id) {
    return $this->dbwp->get_results( $this->dbwp->prepare(
      "SELECT * FROM User
        INNER JOIN Configtext
          ON User.id = Configtext.user_id
        INNER JOIN Mail_config
          ON User.id = Mail_config.user_id
        INNER JOIN User_audit_visibility
          ON User.id = User_audit_visibility.user_id
        INNER JOIN User_report_visibility
          ON User.id = User_report_visibility.user_id
        WHERE User.id = %d", $user_id));
  }


  // TODO: deze function return een array met 1 element -> stdClass
  public function get_visibility_preference($user_id, $type) {
    $table = ($type == 'audit') ? 'User_audit_visibility' : 'User_report_visibility';
    return $this->dbwp->get_results( $this->dbwp->prepare(
      "SELECT * FROM $table WHERE user_id = %d", $user_id));
  }

  public function get_area_fields() {
      return "intro_audit, conclusion_audit, intro_report, conclusion_report, color_audit, color_report, range_number_fb_1, range_number_fb_2, text_fb_1, text_fb_2, text_fb_3, range_number_insta_1, range_number_insta_2, text_insta_1, text_insta_2, text_insta_3, range_number_website_1, range_number_website_2, text_website_1, text_website_2, text_website_3, std_iframe, facebook_title, facebook_sub_title, instagram_title, instagram_sub_title, website_title, website_sub_title, appointment_text, campaign_advice, graph_advice";
  }

  public function toggle_config_visibility($id, $field_name, $type) {
    $table = ($type == 'audit') ? 'User_audit_visibility' : 'User_report_visibility';
    return $this->dbwp->get_results($this->dbwp->prepare(
      "UPDATE $table SET $field_name = !$field_name WHERE user_id = %d", $id));
  }

  /*
   * Get all users.
   */
  public function get_all() {
    return $this->dbwp->get_results("SELECT * FROM User");
  }

  public function get_amount() {
    return $this->dbwp->get_results("SELECT COUNT(id) AS count FROM User");
  }


  public function update($id, $field_name, $field_value, $table) {
    $id_field = $table === 'User' ? 'id':'user_id';
    return $this->dbwp->update($table,
      array($field_name => $field_value), array($id_field => $id));
  }

  public function update_all($field_name, $field_value, $table) {
    return $this->dbwp->update($table, array($field_name => $field_value), 1);
  }

  public function update_list($id, $list_values, $table) {
    $id_field = $table === 'User' ? 'id':'user_id';
    return $this->dbwp->update($table, $list_values, array($id_field => $id));
  }


  public function delete($user_id) {
    $this->dbwp->delete('Client', array( 'user_id' => $user_id ));
    $this->dbwp->delete('User', array( 'id' => $user_id ));
  }
}
?>
