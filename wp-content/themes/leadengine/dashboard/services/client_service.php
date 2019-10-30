<?php
class client_service extends connection {
  /*
   * Uses the exsisting database connection.
   */
  public function __construct($connection) {
    $this->dbwp = $connection->dbwp;
  }


  public function create($data) {
    $this->dbwp->insert('Client', $data);
    return $this->dbwp->insert_id;
  }

  public function create_multiple($data_list) {
    $data_string = implode(', ', $data_list);
    return $this->dbwp->query(
      "INSERT INTO Client ($this->common_fields) VALUES $data_string");
  }


  public function get($id) {
    return $this->dbwp->get_results($this->dbwp->prepare(
      "SELECT * FROM Client WHERE id = %d", $id));
  }


  public function get_all($user_id) {
      return $this->dbwp->get_results($this->dbwp->prepare(
        "SELECT * FROM Client WHERE user_id = %d ORDER BY create_date", $user_id));
  }


  public function get_amount($user_id, $date) {
    if (!isset($date)) {
      return $this->dbwp->get_results($this->dbwp->prepare(
          "SELECT COUNT(id) AS count FROM Client WHERE user_id = %d", $user_id));
    }
    return $this->dbwp->get_results($this->dbwp->prepare(
        "SELECT COUNT(id) AS count FROM Client WHERE user_id = %d AND create_date >= %s", $user_id, $date));
  }


  public function update($id, $field_name, $field_value) {
    return $this->dbwp->update('Client',
      array($field_name => $field_value), array('id' => $id));
  }


  public function insert($id, $name, $fb, $ig, $wb, $mail, $ad_id) {
    $this->dbwp->update('Client',
    array('name'  => $name,
          'facebook' => $fb,
          'instagram' => $ig,
          'website' => $wb,
          'mail' => $mail,
          'ad_id' => $ad_id
    ),
    array('id' => $id));
  }


  public function delete($id) {
    return $this->dbwp->delete('Client', array('id' => $id));
  }

  public function delete_multiple($id, $client_ids) {
    return $this->dbwp->query(
      "DELETE FROM Client WHERE user_id = $id AND id IN ($client_ids)");
  }


  private $common_fields = "user_id, name, facebook, instagram, website, mail, create_date";
}
?>