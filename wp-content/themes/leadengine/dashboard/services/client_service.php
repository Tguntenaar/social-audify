<?php
class client_service extends connection {
  /*
   * Uses the exsisting database connection.
   */
  public function __construct($connection) {
    $this->dbwp = $connection->dbwp;
  }


  public function create($id, $name, $fb, $ig, $wb, $mail, $ad_id) {
    $this->dbwp->insert('Client',
      array('user_id' => $id,
        'name' => $name,
        'facebook' => $fb,
        'instagram' => $ig,
        'website' => $wb,
        'mail' => $mail,
        'ad_id' => $ad_id,
        'create_date' => date('Y-m-d H:i:s')
      ));
    return $this->dbwp->insert_id;
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
}
?>