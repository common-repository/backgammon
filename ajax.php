<?php

if ( ! class_exists('backgammon_fhk_ajax' ) ) { class backgammon_fhk_ajax {

  public static function heartbeatupdate() {
    return false; # disabled for now.
    global $wpdb; # get access to the database
    $d = json_decode(htmlspecialchars_decode(urldecode(stripslashes($_POST['ajaxdata']))),true);

    switch ( @$_GET['action'] ) {
      case 'backgammon_fhk_heartbeatupdate':
      #$response = array('s' => 1, 'ajaxdata' => $d);
      $response = array('s' => 2);
      break;
      default:
      $response = array('s' => 1);
      break;
    }
    wp_send_json($response);
  }

}};
