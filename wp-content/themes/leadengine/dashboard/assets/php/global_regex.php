<?php
  // Patterns
  $name_regex = '[a-zA-Z0-9][a-zA-Z0-9 ]{2,25}';
  // TODO: naam mag niet alleen uit spaties bestaan..

  $fb_regex = '(?:(?:http|https):\/\/)?(?:www.)?facebook.com\/(?:(?:\w)*#!\/)?(?:(?:pages\/)|(?:pg\/))?|([\w\-\.]*)';
  $ig_regex = '(?:^|[^\w])(?:@)?([A-Za-z0-9_](?:(?:[A-Za-z0-9_]|(?:\.(?!\.))){0,28}(?:[A-Za-z0-9_]))?)';
  $website_regex = '^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$';

  function get_fb_name($url) {
    preg_match("'(?:(?:http|https):\/\/)?(?:www.)?facebook.com\/(?:(?:\w)*#!\/)?(?:(?:pages\/)|(?:pg\/))?|([\w\-\.]*)'", $url, $matches);

    $match_index = count($matches) == 3 ? 2 : 1;
    preg_match("'(\d)*$'", $matches[$match_index], $second_matches);

    if ($second_matches[0] != "") {
      return $second_matches[0];
    }
    if (count($matches) == 3) {
      return $matches[0];
    }
    return $matches[1];
  }

  function get_insta_name($url) {
    preg_match("'(?:^|[^\w])(?:@)?([A-Za-z0-9_](?:(?:[A-Za-z0-9_]|(?:\.(?!\.))){0,28}(?:[A-Za-z0-9_]))?)'", $url, $matches);
    return count($matches) > 1 ? $matches[1] : "";
  }
?>