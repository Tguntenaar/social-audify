<?php
  // print errors
  ini_set("display_errors", 1);
  error_reporting(E_ALL);

  if (isset($_GET['request'])) {
    require_once(dirname(__FILE__) . "/services/connection.php");
    require_once(dirname(__FILE__) . "/models/report.php");
    require_once(dirname(__FILE__) . "/models/audit.php");
    require_once(dirname(__FILE__) . "/controllers/report_controller.php");
    require_once(dirname(__FILE__) . "/controllers/audit_controller.php");
    include '../../../../wp-load.php';

    $type_name_id = $_GET['request'];
    $connect = new connection;
    $audit_controller = new audit_controller($connect);
    $report_controller = new report_controller($connect);

    /*
     * TODO is een naam alleen letters (of met [\]^_`) en een id alleen cijfers?
     * TODO naam en id moeten worden encrypt in de de url en hier worden
     * decrypt.
     * TODO pattern matching kan allemaal in 1 keer.
     */

    $pattern = '/^(audit|report)\-(.*)-([0-9]+)(\/*)$/';
    $match = array();

    preg_match($pattern, $type_name_id, $match);
    $id = (int)$match[3];
    // if ($match[1] == 'report') {
    //   if ($report = $report_controller->get($id)) { // TODO: report service get is nog niet goed 
    //     $report->update('view_time', date('Y-m-d'));
    //   }
    // } else if ($match[1] == 'audit') {
    //   if ($audit = $audit_controller->get($id)) {
    //     $audit->update('view_time', date('Y-m-d'));
    //   }
    // } 
    $slug = strtolower('https://'.getenv("HTTP_HOST").'/'.$match[0]);

    header('Location:'.$slug."/?view", true, 303);
  } else {
    header('Location: /404', true, 404);
  }
  exit();
?>
