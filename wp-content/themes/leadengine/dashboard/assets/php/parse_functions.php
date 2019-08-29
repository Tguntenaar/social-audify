<?php
  /**
   * This file is includes in processclient.php and functions.php
   */
  function get_fb_name($url) {
    preg_match("'(?:(?:http|https):\/\/)?(?:www.)?facebook.com\/(?:(?:\w)*#!\/)?(?:(?:pages\/)|(?:pg\/))?([\w\-]*)?|([\w\-]*)'", $url, $matches);

    $match_index = count($matches) == 3 ? 2 : 1;
    preg_match("'(\d)*$'", $matches[$match_index], $second_matches);

    if($second_matches[0] != "") {
      return $second_matches[0];
    }
    if(count($matches) == 3) {
      return $matches[0];
    }
    return $matches[1];
  }

  function get_insta_name($url) {
    preg_match("'(?:^|[^\w])(?:@)?([A-Za-z0-9_](?:(?:[A-Za-z0-9_]|(?:\.(?!\.))){0,28}(?:[A-Za-z0-9_]))?)'", $url, $matches);
    return $matches[1];
  }


?>