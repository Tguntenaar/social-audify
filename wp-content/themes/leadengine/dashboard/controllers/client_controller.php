<?php

class client_controller {

  public function __construct($connection) {
    include(dirname(__FILE__)."/../services/client_service.php");
    $this->service = new client_service($connection);
  }

  function get($client_id) {
    $sql_client = $this->service->get($client_id);
    if (!$sql_client) {
      return;// TODO: what to do? check voor current user access
    }
    return new client($this->service, $sql_client[0]);
  }

  function get_all($months = NULL, $user_id = NULL) {
    $user = $user_id == NULL ? get_current_user_id() : $user_id;
    $sql_clients = $this->service->get_all($user, date('Y-m-1', strtotime("-{$months} month")));

    $return_clients = array();
    foreach($sql_clients as $sql_client) {
      array_unshift($return_clients, new client($this->service, $sql_client));
    }

    return $return_clients;
  }

  function get_amount($date = NULL, $user_id = NULL) {
    $user = $user_id == NULL ? get_current_user_id() : $user_id;
    return $this->service->get_amount($user, $date)[0]->count;
  }

  public function create($name, $fb, $ig, $wb, $mail, $ad_id = NULL) {
    return $this->service->create(get_current_user_id(), $name, $fb, $ig, $wb, $mail, $ad_id);
  }
}
?>