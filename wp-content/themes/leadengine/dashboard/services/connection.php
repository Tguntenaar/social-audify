<?php

class connection {
  /*
   * Connects to the database.
   */
  public $dbwp;
  
  public function __construct() {
    $this->dbwp = new wpdb('admin_daan', 'gAvjej-cepjyf-gehsy5', 'admin_daan', 'localhost');
    $this->dbwp->show_errors();
  }

  public function get_all_recent($user_id, $limit = 12) {
    return $this->dbwp->get_results($this->dbwp->prepare(
      "SELECT 'audit' as type, Audit.id as id, Audit.name as name, Client.name as client_name, Audit.create_date as date, view_time
         FROM Audit INNER JOIN (Select * from Client where user_id = %d) AS Client ON client_id = Client.id 
      UNION 
       SELECT 'report' as type, Report.id as id, Report.name as name, Client.name as client_name, Report.create_date as date, view_time
         FROM Report INNER JOIN (Select * from Client where user_id = %d) AS Client ON client_id = Client.id 
      ORDER BY date DESC LIMIT %d", $user_id, $user_id, $limit));
  }
}
?>