<?php
class report_service extends connection {
  /*
   * Uses the exsisting database connection.
   */
  public function __construct($connection) {
    $this->dbwp = $connection->dbwp;
  }

  public function create($data) {
    $this->dbwp->insert('Report', $data);
    return $this->dbwp->insert_id;
  }


  public function get_id($post_id) {
    return $this->dbwp->get_results($this->dbwp->prepare(
      "SELECT id FROM Report WHERE post_id = %d", $post_id))[0];
  }


  public function get($id) {
    return $this->dbwp->get_results($this->dbwp->prepare(
      "SELECT r.*, $this->content_fields, $this->visibility_fields
       FROM Report as r
       LEFT JOIN Report_content as c
         ON c.report_id = r.id
       LEFT JOIN Report_stat_visibility as v
         ON v.report_id = r.id
       WHERE id = %d", $id));
  }

  public function get_all_reports() {
    return $this->dbwp->get_results(
      "SELECT r.*, $this->content_fields, $this->visibility_fields
       FROM Report as r
       LEFT JOIN Report_content as c
         ON c.report_id = r.id
       LEFT JOIN Report_stat_visibility as v
         ON v.report_id = r.id
       WHERE r.create_date >= DATE(NOW()) - INTERVAL 7 DAY
       ORDER BY r.create_date DESC");
  }


  public function get_all($user_id, $date = null) {
    if (!isset($date)) {
      return $this->dbwp->get_results($this->dbwp->prepare(
        "SELECT Report.*, Client.name as client_name FROM Report INNER JOIN (Select * from Client where user_id = %d)
          AS Client ON Report.client_id = Client.id ORDER BY Report.create_date",
        $user_id));
    }
    return $this->dbwp->get_results($this->dbwp->prepare(
      "SELECT Report.*, Client.name as client_name FROM Report INNER JOIN (Select * from Client where user_id = %d)
        AS Client ON Report.client_id = Client.id AND Report.create_date >= %s
      ORDER BY Report.create_date", $user_id, (string)$date));
  }


  public function get_amount($user_id, $date) {
    if (!isset($date)) {
      return $this->dbwp->get_results($this->dbwp->prepare(
          "SELECT COUNT(Report.id) AS count FROM Report INNER JOIN (Select * from Client where user_id = %d)
        AS Client ON Report.client_id = Client.id", $user_id));
    }
    return $this->dbwp->get_results($this->dbwp->prepare(
        "SELECT COUNT(Report.id) AS count FROM Report INNER JOIN (Select * from Client where user_id = %d)
      AS Client ON Report.client_id = Client.id AND Report.create_date >= %s", $user_id, $date));
  }


  public function get_by_post($post_id) {
    return $this->dbwp->get_results( $this->dbwp->prepare(
      "SELECT name FROM Report WHERE post_id = %d", $post_id));
  }


  public function update($id, $table, $field_name, $field_value) {
    $priref = $table == 'Report' ? 'id' : 'report_id';
    return $this->dbwp->update($table, array($field_name => $field_value), array($priref => $id));
  }


  public function delete($id) {
    return $this->dbwp->delete('Report', array('id' => $id));
  }


  public function insert_content($id) {
    return $this->dbwp->insert('Report_content', array('report_id' => $id));
  }

  public function insert_visibility($report_id) {
    return $this->dbwp->get_results($this->dbwp->prepare(
      "INSERT INTO `Report_stat_visibility` (report_id, $this->visibility_fields)
       SELECT %d, $this->visibility_fields
       FROM `User_report_visibility` WHERE user_id = %d", $report_id, get_current_user_id()));
  }

  public function insert_data($id, $social_stats, $chart_data, $competitor = 0, $manual, $currency, $instagram_name) {
    if ($competitor) {
      return $this->dbwp->insert('Report_content',
      array(
        'report_id'             => $id,
        'social_stats_compare'  => $social_stats,
        'chart_data_compare'    => $chart_data,
        'manual'                => $manual,
        'instagram_name'        => $instagram_name
      ));
    }
    return $this->dbwp->insert('Report_content',
      array(
        'report_id'      => $id,
        'social_stats'   => $social_stats,
        'chart_data'     => $chart_data,
        'currency'       => $currency,
        'manual'         => $manual,
        'instagram_name' => $instagram_name
      ));
  }


  public function toggle_config_visibility($id, $field_name) {
    // TODO : dit is nog niet attack-veilig, is wss een betere wp functie voor...
    return $this->dbwp->get_results($this->dbwp->prepare(
      "UPDATE `Report_stat_visibility` SET $field_name = !$field_name WHERE report_id = %d", $id));
  }


  public function get_content_fields() {
    return $this->content_fields;
  }

  private $content_fields = "introduction, social_advice, campaign_advice, conclusion, social_stats, chart_data, social_stats_compare, chart_data_compare, manual, currency, instagram_name, color";
  private $visibility_fields = "soc_pl, soc_aml, soc_inf, soc_inaf, soc_iae, soc_plm, cam_imp, cam_cpc, cam_cpm, cam_cpp, cam_ctr, cam_frq, cam_spd, cam_rch, cam_lcl, cam_ras";
}
?>
