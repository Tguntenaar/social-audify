<?php
class Regex {

  function valid_fb($url) {
    // Url contains form of facebook.com/ or /pages
    preg_match("/$this->fb_contains_url/", $url, $matches);
    if (count($matches) > 0) {
      return false;
    }
    // Url contains valid possible id
    preg_match("/$this->fb_contains_id/", $url, $matches);
    return count($matches) == 0;
  }

  function valid_ig($url) {
    // Url contains form of instagram.com/ or @
    preg_match("/$this->ig_contains_url/", $url, $matches);
    return count($matches) == 0;
  }

  function valid_wb($url) {
    // TODO : nog extra check op websites...
    // preg_match("/$this->wb_is_valid/", $url, $matches);
    // return count($matches) == 0;
    return true;
  }

  // Patterns
  public $name = '[a-zA-Z0-9][a-zA-Z0-9 ]{2,25}';

  public $fb_contains_id = '(?:[A-Za-z0-9_.]+)(?:\-)([0-9]{14,17})$';
  public $fb_contains_url = '(?:(?:http|https):\/\/)?(?:www.)?facebook.com\/|pages\/|pg\/';

  public $ig_contains_url = '(?:(?:http|https):\/\/)?(?:www.)?instagram.com\/|\@';

  public $wb = '^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$';
  public $email = '';
}
?>