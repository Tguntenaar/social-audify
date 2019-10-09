<?php

class user {
  private $service;
  private $sql_data;


  public function __construct($service, $sql_client) {
    $this->service = $service;
    $this->sql_data = $sql_client;
  }


  public function __get($name) {
    return $this->sql_data->$name;
  }


  // $type is String 'audit' || 'report'
  public function get_visibility($type) {
    return $this->service->get_visibility_preference($this->id, $type);
  }

  public function toggle_visibility($field, $type) {
    return $this->service->toggle_config_visibility($this->id, $field, $type);
  }


  public function update($table, $field_name, $value) {
    return $this->service->update($this->id, $field_name, $value, $table);
  }

  public function update_list($table, $list_values) {
    return $this->service->update_list($this->id, $list_values, $table);
  }

  public function delete() {
    return $this->service->delete($this->id);
  }
}
?>
