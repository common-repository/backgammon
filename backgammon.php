<?php
/*
 * Plugin Name: backgammon
 * Plugin URI:  https://wordpress.org/plugins/backgammon
 * Description: This plugin provides a backgammon board embedded in your wordpress site, using widget or shortcode [backgammon_playboard]
 * Author:      Frank Koenen
 * Version:     2.1.3
 */

@define('FHK_BACKGAMMON_VERSION', '2.1.3');
@define('IS_FHK_AJAX', ( @$_SERVER['SCRIPT_NAME'].'x' == '/wp-admin/admin-ajax.phpx' && @$_GET['action'].'x' != 'x' ));
@define('IS_FHK_CRON', ( @$_SERVER['PHP_SELF'].'x' == '/wp-cron.phpx' ));

add_action('wp_before_admin_bar_render', array('backgammon_fhk_object','wp_before_admin_bar_render'));
add_action('wp_dashboard_setup', array('backgammon_fhk_object','add_dashboard_widgets'));
add_action('admin_menu', array('backgammon_fhk_object','administrator_menu'),99);
add_action('init', array('backgammon_fhk_object','InitVisitor1'),1);

if ( (int)IS_FHK_AJAX == 1 ) {
  $n = 'heartbeatupdate'; add_action('wp_ajax_backgammon_fhk_' . $n, array('backgammon_fhk_ajax',$n)); add_action('wp_ajax_nopriv_backgammon_fhk_' . $n, array('backgammon_fhk_ajax',$n)); unset($n);
  include_once WP_PLUGIN_DIR .'/backgammon/ajax.php';
}

include_once WP_PLUGIN_DIR . '/backgammon/widget.php';

class backgammon_fhk_object {
  public static function wp_before_admin_bar_render() {
    if ( ! current_user_can( 'administrator' ) ) return;
    global $wp_admin_bar;
    $wp_admin_bar->add_menu( array(
      'parent' => 'site-name',
      'id' => 'backgammon_fhk-admin-pulldown',
      'title' => '<img style="display:inline-block;vertical-align:middle" src="http://www.simplybg.com/favicon.ico">' . __('Backgammon'),
      'href' => admin_url('admin.php?page=backgammon_fhk_admin'),
      'meta' => array('class' => 'backgammon_fhk-admin',)
    ));
  }

  public static function administrator_menu() {
    wp_enqueue_style('backgammon_fhk_admin_css', plugins_url() . '/backgammon/adminmain.css', array(), FHK_BACKGAMMON_VERSION, 'all');
    $parentmenu = 'backgammon_fhk_admin';
    add_menu_page('Backgammon', '<span class="backgammon-fhk-admin-menuitem">Backgammon</span>', 'administrator', $parentmenu, 'backgammon_fhk_admin_page_one', 'http://www.simplybg.com/favicon.ico', 99);
    add_submenu_page( $parentmenu, 'Main Settings', 'Main Settings', 'administrator', $parentmenu . '_mainsettings', 'backgammon_fhk_admin_page_mainsettings');
    add_submenu_page( $parentmenu, 'Board Settings', 'Board Settings', 'administrator', $parentmenu . '_boardsettings', 'backgammon_fhk_admin_page_boardsettings');
  }

  public static function add_dashboard_widgets() {
    wp_add_dashboard_widget('backgammon_fhk_admin_dashboard_widget', 'Backgammon', 'backgammon_fhk_admin_dashboard_widget_function');
  }

  public static function InitVisitor1() { # backend/PHP things to do at start of a visitor page load.
    $version = ( 1 == 1 ) ? FHK_BACKGAMMON_VERSION : false; # false ::= a version number is automatically used equal to current installed WordPress version.
    $media = 'all'; # one of: 'all', 'screen', 'handheld', or 'print'
    wp_enqueue_style('backgammon_fhk_css', plugins_url() . '/backgammon/styles.css', array(), $version, $media);
    wp_enqueue_script('backgammon_fhk_js', plugins_url() . '/backgammon/backgammon.js', array(), $version, ('in_footer'=='in_footer') );
    if ( is_user_logged_in() === true ) { }
  }

}

function backgammon_fhk_admin_dashboard_widget_function() {
  ob_start(); ?>
  <ul>
    <li><a href="/wp-admin/admin.php?page=backgammon_fhk_admin">Backgammon Board</a><br></li>
  </ul>
  <?php $html = ob_get_contents();ob_end_clean();
  echo $html;
}

function backgammon_fhk_admin_page_one() {
  ?>
  <h1>Backgammon</h1>
  <ul>
    <li><a href="/wp-admin/admin.php?page=backgammon_fhk_admin_mainsettings">Main Settings</a></li>
    <li><a href="/wp-admin/admin.php?page=backgammon_fhk_admin_boardsettings">Board Settings</a></li>
  </ul>
  <span></span>
  <a title="SimplyBG.com, Online Backgammon With Friends" href="//www.simplybg.com"><img src="//www.simplybg.com/images/topnavleftcornerlogo.png" style="width:200px"></a>
  <?php
}

function backgammon_fhk_admin_page_boardsettings() {
  ?>
  <h1>Backgammon Board Settings</h1>

  <p>
    No board settings required.
  </p>

  <label>Some examples of how to use the shortcode</label>:
  <pre>
    [backgammon_playboard]
    [backgammon_playboard style="width:778px;height:634px" bgboardsize="75"]
      <span style="font-size:10px"><i>bgboardsize</i> is one of 75, 50, 40 or 25</span>
  </pre>
  <br><br>
  <?php
}

function backgammon_fhk_admin_page_mainsettings() {

  if ( $_POST['backgammon_fhk_admin_post_keysave'].'x' == 'yesx' && str_replace(' ','',$_POST['sitename']).'x' != 'x' && str_replace(' ','',$_POST['secretkey']).'x' != 'x' ) {
    update_option('backgammon_fhk_sitename', strtolower(str_replace(' ','',$_POST['sitename'])));
    update_option('backgammon_fhk_secretkey', str_replace(' ','',$_POST['secretkey']));
  }

  if ( $_POST['backgammon_fhk_admin_post_clear'].'x' == 'yesx' ) {
    update_option('backgammon_fhk_sitename', '');
    update_option('backgammon_fhk_secretkey', '');
  }

  $sitename = get_option('backgammon_fhk_sitename', 'unknown');
  $secretkey = get_option('backgammon_fhk_secretkey', 'unknown');
  $lm = get_option('backgammon_fhk_admin_lastmessage_admin', 'Your site, with id "' . htmlspecialchars(str_replace(' ','',$sitename)) . '", has not been established as a <i>Trusted-site</i> with SimplyBG. To learn how to enable your site as a <i>Trusted-site</i>, contact <a href="//www.simplybg.com/contact.html?wpsitetrustsetup=1">simplybg@feweb.net</a><br>Please include you server IP address with your message: ' . $_SERVER['SERVER_ADDR']);
  $ts = false;
  include_once WP_PLUGIN_DIR .'/backgammon/nonce.lib'; $o = new noncelib(array('duration'=> 300, 'name' => $sitename, 'uniqkey' => $secretkey, 'usesession' => false));
  list($N,$hash) = $o->getarray($sitename,$secretkey);

  $context = stream_context_create(array('http'=>array(
    'method' => 'POST',
    'header' => 'Content-type: application/x-www-form-urlencoded',
    'content' => http_build_query(array('nonce'=>$hash,'op'=>'ooglyboogly','args'=>null)),
    'timeout' => 5,
  )));
  $kk = ( $secretkey.'x' == 'unknownx' ) ? '(no key currently defined)' : '(incorrect key)';
  ob_start(); $result = @file_get_contents('http://www.simplybg.com/remoteplayboard.html?sitename=' . urlencode($sitename), false, $context, -1, 5000); $x = ob_get_contents();ob_end_clean(); 
  if ( substr($result,0,11).'x' != 'booglyooglyx' ) $lm = ( ( $result.'x' != 'x' ) ? $result : 'Cannot determine current trust connection status. The SimplyBG server may not be reachable at this time. Please try again shortly.' );
  else {
    list($a,$b) = explode(' ',trim($result),2);
    $lm = '<span>Trusted-site has been established with SimplyBg.com. Your site-ident is <b>' . esc_html($b) . '</b></span><br><span style="font-size:smaller">(<i>Note: if you plan to revise your domainname or IP address, please be sure to notify before hand to ensure uninterrupted service.</i>)</span>';
    $kk = '(current key is correct)';
    $ts = true;
  }
  
  ?>

  <h1>Backgammon Main Settings</h1>

  <br>
  <div style="margin:5px;display:block;border-top:1px solid black;height:1px;width:97%"></div>
  <br>
  <h2>Trust Settings:</h2>

  <p>
  <?php if ( $ts !== true ) { ?>
    The backgammon plugin is fully functional without additional configurations. As an option, you can choose to have your site setup as a <i>Trusted-site</i> with SimplyBG.
    <br>
    When your site is trusted, players can be identified using your website to authenticat users, eliminating the validation steps required of opponents starting matches otherwise.
    <br>
    Additional custom configurations are made available to <i>Trusted-site</i>s. To learn how to enable your site as a <i>Trusted-site</i>, contact <a href="//www.simplybg.com/contact.html?wpsitetrustsetup=1">simplybg@feweb.net</a>
    <br>
    Please include you server IP address with your message: <?php echo $_SERVER['SERVER_ADDR']; ?>
  <?php } ?>
  </p>

  <form method="post" name="backgammon_fhk_admin_keysave_form">
    <label style="display:inline-block;text-align:right;width:120px" for="sitename">Site Ident:</label><input type="text" name="sitename" value="<?php echo esc_attr($sitename); ?>"/><br>
    <label style="display:inline-block;text-align:right;width:120px" for="secretkey">Secret Key:</label><input type="password" name="secretkey" /> <?php echo $kk; ?>
    <input type="hidden" name="backgammon_fhk_admin_post_clear" id="backgammon_fhk_admin_post_clear" value="no" /><button style="margin-left:100px;font-size:10px" onclick="document.getElementById('backgammon_fhk_admin_post_clear').value='yes';this.form.submit();return false;">clear key</button>
    <br>
    <input type="hidden" name="backgammon_fhk_admin_post_keysave" value="yes" /><input class="button" type="submit" value="Update" />
  </form>

  <?php
  echo '<br><span>' . str_replace("\n",'<br>',$lm) . '</span>';
  $lm = update_option('backgammon_fhk_admin_lastmessage_admin', $lm);
}

