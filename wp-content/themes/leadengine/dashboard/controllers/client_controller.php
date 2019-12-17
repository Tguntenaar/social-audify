<?php

class client_controller {

  public function __construct($connection) {
    include(dirname(__FILE__)."/../services/client_service.php");
    $this->service = new client_service($connection);
  }
  
  function create($name, $fb, $ig, $wb, $mail, $ad_id = NULL) {
    $data = array('user_id' => get_current_user_id(),
      'name' => $name,
      'facebook' => $fb,
      'instagram' => $ig,
      'website' => $wb,
      'mail' => $mail,
      'ad_id' => $ad_id,
      'create_date' => date('Y-m-d H:i:s')
    );
    return $this->service->create($data);
  }

  function create_multiple($id, $clients) {
    $data_list = array();
    foreach ($clients as $client) {
      array_push($data_list, "({$id}, '{$client['name']}', '{$client['fb']}', ".
        "'{$client['ig']}', '{$client['wb']}', '{$client['mail']}', '".date('Y-m-d H:i:s')."')");
    }
    // return $this->service->create_multiple($data_list);
    // for testing
    return $data_list;
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

  public function delete_multiple($id, $clients) {
    $clients_string = implode(', ', $clients);
    return $this->service->delete_multiple($id, $clients_string);
  }
}
?>