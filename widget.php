<?php

class backgammon_fhk extends WP_Widget {

  function __construct() {
    parent::__construct('backgammon_fhk', 'Backgammon Play Board', array( 'description' => 'Head-to-Head Backgammon Play Manager' ) );
  }

  function form($instance) {
    $title = ( $instance ) ? esc_html( $instance[ 'title' ] ) : __( 'Backgammon', 'text_domain' ); if ( $title.'x' == 'x' ) $title = __( 'Backgammon', 'text_domain' );
    ?>
    <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /><br>
    <span>This widget also works on shortcode [backgammon_playboard]</span>
    <?php
  }

  function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $instance['title'] = strip_tags($new_instance['title']);
    return $instance;
  }

  function widget($args, $instance) { extract($args);
    echo backgammon_fhk_shortcode::shortcode(null,null,'backgammon_playboard');
  }
}

class backgammon_fhk_shortcode {

  public static function shortcode($atts, $content=null, $code='') { # code: backgammon_playboard, $atts ::= [(str)style,(int)bgboardsize]
    extract( shortcode_atts( array('bgboardsize' => 75, 'style' => ''), $atts ) ); # default $atts
    $sitename = get_option('backgammon_fhk_sitename', 'unknown');
    $secretkey = get_option('backgammon_fhk_secretkey', 'unknown');
    include_once WP_PLUGIN_DIR .'/backgammon/nonce.lib'; $o = new noncelib(array('duration'=> 300, 'name' => $sitename, 'uniqkey' => $secretkey, 'usesession' => false));
    list($N,$hash) = $o->getarray($sitename,$secretkey);
    extract( shortcode_atts(array('style' => 'width:800px;height:800px'), $atts ));

    ob_start(); ?>
    <section class="fhk_backgammon_plugin">
      <img style="cursor:pointer" src="@@BANNER@@" onclick="window.fhk_backgammon_jso.clicktostart(this,event)" />
    </section>
    <?php $html = ob_get_contents();ob_end_clean(); 

    $html = str_replace('@@VER@@', FHK_BACKGAMMON_VERSION, $html);
    $html = str_replace('@@AAA@@', $_SERVER['SERVER_ADDR'], $html);
    $html = str_replace('@@BGBS@@', (int)$bgboardsize, $html);
    $html = str_replace('@@HASH@@', $hash, $html);
    $html = str_replace('@@STYLE@@', $style, $html);
    $html = str_replace('@@BANNER@@', WP_CONTENT_URL . '/plugins/backgammon/banner.png', $html);

    return $html;

  }

  public static function shortcode_play_direct($atts, $content=null, $code='') { # code: backgammon_fhk_playboard
    return '<iframe src="//www.simplybg.com/play" data-isremoteplayboard="' . $_SERVER['SERVER_ADDR'] . '" ></iframe>';
  }
}

add_action( 'widgets_init', function(){ register_widget('backgammon_fhk'); });
add_shortcode('backgammon_playboard', array('backgammon_fhk_shortcode','shortcode') );
add_shortcode('backgammon_fhk_playboard', array('backgammon_fhk_shortcode','shortcode_play_direct') );

