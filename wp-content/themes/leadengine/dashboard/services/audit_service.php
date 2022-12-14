<?php
class audit_service extends connection {
  /*
   * Uses the exsisting database connection.
   */
  public function __construct($connection) {
    $this->dbwp = $connection->dbwp;
  }


  public function create($id, $values) {
    $this->dbwp->query(
      "INSERT INTO Audit ($this->create_fields) VALUES ($values
        (SELECT std_mail_bit FROM Configtext WHERE user_id = $id))");
    return $this->dbwp->insert_id;
  }


  public function get_id($post_id) {
    return $this->dbwp->get_results($this->dbwp->prepare(
      "SELECT id FROM Audit WHERE post_id = %d", $post_id))[0];
  }


  public function get($id) {
    return $this->dbwp->get_results($this->dbwp->prepare(
      "SELECT a.*, d.manual, $this->template_fields, $this->visibility_fields, $this->crawl_fields, $this->data_fields
        FROM Audit as a
        LEFT JOIN Audit_template as t
          ON t.audit_id = a.id
        LEFT JOIN Audit_stat_visibility as v
          ON v.audit_id = a.id
        LEFT JOIN Audit_crawl as c
          ON c.audit_id = a.id and c.competitor = 0
        LEFT JOIN Audit_data as d
          on d.audit_id = a.id and d.competitor = 0
        WHERE id = %d", $id));
  }

  public function get_field_template($id, $field_name) {
    return $this->dbwp->get_results($this->dbwp->prepare(
      "SELECT %s FROM Audit_template WHERE audit_id = %d", $field_name, $id));
  }

  // TODO: get moet de competitor erbij ophalen in 1 keer
  public function get_competitor($id) {
    return $this->dbwp->get_results($this->dbwp->prepare(
      "SELECT d.manual, $this->crawl_fields, $this->data_fields FROM Audit as a
        INNER JOIN Audit_data as d
          on d.audit_id = a.id and d.competitor = 1
        LEFT JOIN Audit_crawl as c
          ON c.audit_id = a.id and c.competitor = 1
        where id = %d", $id));
  }

  public function get_all_audits() {
    return $this->dbwp->get_results(
      "SELECT a.*, d.manual, $this->template_fields, $this->visibility_fields, $this->crawl_fields, $this->data_fields
        FROM Audit as a
        LEFT JOIN Audit_template as t
          ON t.audit_id = a.id
        LEFT JOIN Audit_stat_visibility as v
          ON v.audit_id = a.id
        LEFT JOIN Audit_crawl as c
          ON c.audit_id = a.id and c.competitor = 0
        LEFT JOIN Audit_data as d
          on d.audit_id = a.id and d.competitor = 0
        WHERE a.create_date >= DATE(NOW()) - INTERVAL 7 DAY
        ORDER BY a.create_date DESC");
  }


  public function get_all($user_id, $date = null) {
    if (!isset($date)) {
      return $this->dbwp->get_results($this->dbwp->prepare(
        "SELECT Audit.*, Client.name as client_name FROM Audit INNER JOIN (Select * from Client where user_id = %d)
          AS Client ON Audit.client_id = Client.id ORDER BY Audit.create_date",
        $user_id));
    }
    return $this->dbwp->get_results($this->dbwp->prepare(
      "SELECT Audit.*, Client.name as client_name, Client.mail as client_mail, Client.facebook as client_facebook, Client.instagram as client_instagram, Client.website as client_website FROM Audit
        INNER JOIN (Select * from Client where user_id = %d) AS Client ON Audit.client_id = Client.id AND Audit.create_date >= %s
      ORDER BY Audit.create_date", $user_id, (string)$date));
  }


  public function get_amount($user_id, $date) {
    if (!isset($date)) {
      return $this->dbwp->get_results($this->dbwp->prepare(
          "SELECT COUNT(Audit.id) AS count FROM Audit INNER JOIN (Select * from Client where user_id = %d)
        AS Client ON Audit.client_id = Client.id", $user_id));
    }
    return $this->dbwp->get_results($this->dbwp->prepare(
        "SELECT COUNT(Audit.id) AS count FROM Audit INNER JOIN (Select * from Client where user_id = %d)
      AS Client ON Audit.client_id = Client.id AND Audit.create_date >= %s", $user_id, $date));
  }


  public function check_website($id, $comp) {
    return $this->dbwp->get_results($this->dbwp->prepare(
      "SELECT $this->crawl_fields FROM Audit_crawl WHERE audit_id = %d and competitor = %d", $id, $comp));
  }


  // TODO : bovenste update functie is alles overkoepelende, volgende twee zijn eigenlijk overbodig...
  public function update($id, $table, $field_name, $field_value, $comp) {
    $priref = $table === 'Audit' ? 'id' : 'audit_id';
    $where = $table == 'Audit_data' ? array($priref => $id, 'competitor' => $comp) : array($priref => $id);
    return $this->dbwp->update($table, array($field_name => $field_value), $where);
  }


  public function toggle_config_visibility($id, $field_name) {
    // TODO : dit is nog niet attack-veilig, is wss een betere wp functie voor...
    return $this->dbwp->get_results($this->dbwp->prepare(
      "UPDATE `Audit_stat_visibility` SET $field_name = !$field_name WHERE audit_id = %d", $id));
  }


  public function insert_template($id, $audit_id) {
    return $this->dbwp->get_results($this->dbwp->prepare(
      "INSERT INTO Audit_template (audit_id, video_iframe, language) 
        SELECT %d, std_iframe, language FROM Configtext WHERE user_id = %d", $audit_id, $id));
  }


  public function insert_visibility($id, $audit_id) {
    return $this->dbwp->get_results($this->dbwp->prepare(
      "INSERT INTO Audit_stat_visibility (audit_id, $this->visibility_fields)
        SELECT %d, $this->visibility_fields FROM User_audit_visibility WHERE user_id = %d", $audit_id, $id));
  }


  public function insert_data($id, $fb_name, $fb_data, $ig_name, $ig_data, $comp = 0, $manual = 0) {
    return $this->dbwp->insert('Audit_data', array(
      'audit_id'     => $id,
      'facebook_name'  => $fb_name,
      'facebook_data'  => $fb_data,
      'instagram_name' => $ig_name,
      'instagram_data' => $ig_data,
      'competitor'     => $comp,
      'manual'         => $manual
    ));
  }


  public function delete($id) {
    return $this->dbwp->delete('Audit', array('id' => $id));
  }

  public function delete_multiple($id, $audit_ids) {
    return $this->dbwp->query(
      "DELETE FROM Audit WHERE client_id IN
      (SELECT id FROM Client WHERE user_id = $id) AND id IN ($audit_ids)");
  }

  public function get_template_fields() {
    return $this->template_fields;
  }

  private $create_fields = "client_id, name, create_date, post_id, facebook_bit, instagram_bit, website_bit, competitor_name, mail_bit";
  private $create_template_fields = "introduction, conclusion, video_iframe, color";

  private $template_fields = "introduction, conclusion, facebook_advice, instagram_advice, website_advice, facebook_score, instagram_score, website_score, video_iframe, color, language";
  private $visibility_fields = "fb_likes, fb_pem, fb_dpp, fb_dph, fb_apl, fb_loc, fb_ntv, fb_tab, fb_cp, insta_nof, insta_ae, insta_nplm, insta_nopf, insta_ac, insta_al, website_pixel, website_ga, website_googletag, website_mf, website_lt, website_ws, insta_hashtag, insta_lpd, fb_ads, fb_ads_comp, facebook_vis_bit, instagram_vis_bit, website_vis_bit, introduction_vis_bit, conclusion_vis_bit, picture_vis_bit";

  private $crawl_fields = "facebook_pixel, google_analytics, google_tagmanager, mobile_friendly, load_time, website_size";
  private $data_fields = "facebook_name, facebook_data, instagram_name, instagram_data";
}
?>
