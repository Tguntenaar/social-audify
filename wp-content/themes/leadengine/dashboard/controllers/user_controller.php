<?php
class user_controller {

  public function __construct($connection = NULL) {
    error_reporting(E_ALL);
    ini_set("display_errors", 1);

    include(dirname(__FILE__)."/../services/user_service.php");
    $this->service = new user_service($connection);
  }

  public function create($user_id, $name, $email) {
    if (!$this->service->get($user_id)) {
      $this->service->create($user_id, $name, $email);
      $this->service->create_config($user_id);
      $this->service->create_mail_config($user_id);
      $this->service->create_stat_visibility($user_id);
    }
  }

  function get($id) {
    $sql_user = $this->service->get($id);

    if(isset($sql_user[0])) {
        return new user($this->service, $sql_user[0]);
    } else {
        return NULL;
    }
  }


  function get_all() {
    $sql_users = $this->service->get_all();

    $return_users = array();
    foreach($sql_users as $sql_user) {
      array_unshift($return_users, new user($this->service, $sql_user));
    }

    return $return_users;
  }

  function get_amount() {
    return $this->service->get_amount()[0]->count;
  }
}
?>
