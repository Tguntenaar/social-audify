<?php

class client {
  private $service;
  private $sql_data;


  public function __construct($service, $sql_client) {
    error_reporting(E_ALL);
    ini_set("display_errors", 1);

    $this->service = $service;
    $this->sql_data = $sql_client;
  }


  public function __get($name) {
    return $this->sql_data->$name;
  }


  public function update_all($name, $facebook, $instagram, $website, $mail, $ad_id) {
    $this->name = isset($name) ? $name : $this->name;
    $this->facebook = isset($facebook) ? $facebook : $this->facebook;
    $this->instagram = isset($instagram) ? $instagram : $this->instagram;
    $this->website = isset($website) ? $website : $this->website;
    $this->mail = isset($mail) ? $mail : $this->mail;
    $this->ad_id = isset($ad_id) ? $ad_id : $this->ad_id;

    $this->service->insert($this->id, $this->name, $this->facebook, $this->instagram, $this->website, $this->mail, $this->ad_id);
    return 1;
  }

  public function update($field_name, $field_value) {
    $this->service->update($this->id, $field_name, $field_value);
  }


  public function delete() {
    return $this->service->delete($this->id);
  }
}
?>
