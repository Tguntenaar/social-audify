<?php
class logger {

  public function __construct($phpLocation = "runtime_error.log", $jsLocation = "js_error.log") {
    $this->stdOutput = dirname(__FILE__) . "/..";

    // Set php.ini settings if default settings are found.
    if (ini_get('display_errors') === '1') {
      ini_set('display_startup_errors', '0');
      ini_set('display_errors', '0');

      ini_set('log_errors', '1');
      ini_set('track_errors', '1');
      ini_set('report_memleaks', '0');
      error_reporting(E_ALL);
    }

    if (ini_get('error_log') !== "{$this->stdOutput}/php_error.log") {
      ini_set('error_log', "{$this->stdOutput}/php_error.log");
    }

    $this->runtimeFile = "{$this->stdOutput}/{$phpLocation}";
    $this->jsFile = "{$this->stdOutput}/{$jsLocation}";
  }

  public function printRuntime($user, $msg, $stacktrace = "") {
    $str = "[{$this->getDate()}] - {$user} : {$msg}\n";
    $str .= ($stacktrace !== "") ? ": {$stacktrace}" : "";
    file_put_contents($this->runtimeFile, $str, FILE_APPEND | LOCK_EX);
  }

  public function printJs($user, $msg, $stacktrace = "") {
    $str = "\n[{$this->getDate()}] - {$user} : {$msg}";
    $str .= $stacktrace !== "" ? ": {$stacktrace}" : "";
    file_put_contents($this->jsFile, $str, FILE_APPEND | LOCK_EX);
  }

  function getDate() {
    return date('d-M-Y H:i:s T');
  }
}
?>
