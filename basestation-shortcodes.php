<?php
/*
Plugin Name: Base Station Shortcodes
Plugin URI: http://www.johnparris.com/wordpress-plugins/basestation-shortcodes/
Description: Shortcodes for displaying Foundation elements in the Base Station theme
Version: 1.0
Author: John Parris
Author URI: http://www.johnparris.com
License: GPL2
*/

/*  Copyright 2012 John Parris */

/* Prevent direct access */
if ( ! defined( 'ABSPATH' ) )
  die ( 'What\'chu talkin\' \'bout, Willis?' );


if ( ! class_exists( 'BaseStation_Shortcodes' ) ):

class BaseStation_Shortcodes {

  function __construct() {
    add_action( 'init', array( $this, 'add_shortcodes' ) );

    /* Allow shortcodes in widgets */
    add_filter('widget_text', 'do_shortcode');
  }


  /**
   * Add our shortcodes
   *
   * @since 1.0
   */
  function add_shortcodes() {
    add_shortcode( 'alert',          array( $this, 'basestation_alert' ) );
    add_shortcode( 'button',         array( $this, 'basestation_button' ) );
    add_shortcode( 'featured-posts', array( $this, 'basestation_featured_posts_shortcode' ) );
    add_shortcode( 'label',          array( $this, 'basestation_label' ) );
    add_shortcode( 'loginform',      array( $this, 'basestation_login_form' ) );
    add_shortcode( 'panel',          array( $this, 'basestation_panel' ) );
  }


  /**
   * Alerts
   *
   * @since 1.0
   * Types are 'secondary', 'success', and 'alert'. If type is not specified, a default color is displayed. Specify a heading text. See example.
   * Example: [alert type="success" heading="Congrats!"]You won the lottery![/alert]
   */
  function basestation_alert( $atts, $content = null ) {
    extract( shortcode_atts( array(
      'type'    => '',
      'heading' => ''
      ), $atts ) );

    return '<div class="alert-box '.$type.'"><a href="#" class="close">&times;</a><strong>'. do_shortcode( $heading ) .'</strong><p> ' . do_shortcode( $content ) . '</p></div>';
  }



  /**
   * Buttons
   *
   * @since 1.0
   * [button] shortcode. Options for type= are "primary", "info", "success", "warning", "danger", and "inverse".
   * Options for size are tiny, small, medium and large. If none is specified it defaults to medium size.
   * Example: [button type="info" size="large" link="http://yourlink.com"]Button Text[/button]
   */
  function basestation_button( $atts, $content = null ) {
    extract( shortcode_atts( array(
      'link' => '#',
      'type' => '',
      'size' => 'medium',
      'style' => ''
      ), $atts) );

    if ( empty( $type ) ) {
      $type = "button";
    } else {
      $type = "button " . $type;
    }

    if ( $size == "medium" ) {
      $size = "";
    } else {
      $size = ' '.$size.'';
    }

    if ( !empty( $style ) ) {
      $style = ' '.$style.'';
    }

    return '<a class="'.$type.''.$size.''.$style.'" href="'.$link.'">' . do_shortcode( $content ) . '</a>';
  }




  /**
   * Featured Posts Carousel
   *
   * @since 1.0
   * [featured-posts] shortcode. Options are tag, max, width, and height. Defaults: tag="featured" max="3" width="745" height="350".
   * Example: [featured-posts tag="featured" max="3"] This will feature up to 3 posts tagged "featured".
   */
  function basestation_featured_posts_shortcode( $atts, $content = null ) {
    extract( shortcode_atts( array(
      'tag'    => '',
      'max'    => '',
      'width'  => '',
      'height' => '' ),
    $atts) );

    if ( empty( $tag ) ) {
      $tag = "featured";
    } else {
      $tag = ''. $tag .'';
    }

    if ( empty( $max ) ) {
      $max = "3";
    } else {
      $max = ''. $max .'';
    }

    if ( empty( $width ) ) {
      $width = "745";
    } else {
      $width = ''.$width.'';
    }

    if ( empty( $height ) ) {
      $height = "350";
    } else {
      $height = ''.$height.'';
    }

    $featuredquery = 'posts_per_page=' . absint( $max );
    $featuredquery .= '&tag=' . $tag;
    $featured_query_shortcode = new WP_Query( $featuredquery );

    if ( $featured_query_shortcode->have_posts() ) { ?>
      <!-- Featured listings -->
      <div style="width:<?php echo $width;?>px; max-width: 100%">
      <div class="row">
      <div class="twelve columns">
        <div id="featured-carousel-shortcode" class="carousel slide">

            <?php while ( $featured_query_shortcode->have_posts() ) : $featured_query_shortcode->the_post(); ?>

            <div class="item">
              <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php echo get_the_post_thumbnail( ''. $featured_query_shortcode->post->ID .'', array($width, $height), array('title' => "" )); ?></a>
              <div class="carousel-caption">
                <h4><?php the_title(); ?></h4>
              </div><!-- .carousel-caption -->
            </div><!-- .item -->

          <?php endwhile; ?>

        </div><!-- #featured-carousel-shortcode -->
      </div><!-- .twelve -->
      </div><!-- .row -->
      </div>
      <div class="clear">&nbsp;</div>
      <script type="text/javascript">
          jQuery(window).load(function() {
          jQuery("#featured-carousel-shortcode").orbit({
            animation: 'fade',
            pauseOnHover: true,
            startClockOnMouseOut: true,
            startClockOnMouseOutAfter: true,
            bullets: true
          });
        });
      </script>
      <?php } // if(have_posts()) ?>
      <!-- End featured listings -->
  <?php wp_reset_query();
  }



  /**
   * Labels
   *
   * @since 1.0
   * [label] shortcode.
   * Options for type= are "secondary", "success", and "alert". If a type of not specified, default is used.
   * Options for style= are "radius" and "round". If no style is specified, a regular rectangle style is used.
   * Example: [label type="alert" style="radius"]Label text[/label]
   */
  function basestation_label( $atts, $content = null ) {
    extract( shortcode_atts( array( 'type' => '', 'style' => '' ), $atts ) );
    $output = '';
    if ($type != '') $output .= ' '.$type.'';
    if ($style != '') $output .= ' '.$style.'';
    return '<span class="label'.$output.'">' . do_shortcode( $content ) . '</span>';
  }




  /**
   * Login form shortcode
   *
   * @since 1.0
   * [loginform] shortcode. Options are redirect="http://your-url-here.com". If redirect is not set, it returns to the current page.
   * Example: [loginform redirect="http://www.site.com"]
   */
  function basestation_login_form( $atts, $content = null ) {
    extract( shortcode_atts( array(
      'redirect' => ''
      ), $atts ) );

    if ( !is_user_logged_in() ) {
      if( $redirect ) {
        $redirect_url = $redirect;
      } else {
        $redirect_url = get_permalink();
      }
      $form = wp_login_form(array('echo' => false, 'redirect' => $redirect_url ));
      return $form;
    }
  }




  /**
   * Panels
   *
   * @since 1.0
   * [panel] shortcode. Columns defaults to 'six'. You can specify columns in the shortcode.
   * Example: [panel columns="four"]Your panel text here.[/panel]
   */
  function basestation_panel( $atts, $content = null ) {
    extract( shortcode_atts( array( 'columns' => 'six' ), $atts ) );
    $gridsize = 'twelve';
    $span = '"columns ';
    if ( $columns != "twelve" ) {
      $span .= ''.$columns.'"';
      $spanfollow = $gridsize - $columns;
      return '<div class="row"><div class='.$span.'><div class="panel"><p>' . do_shortcode( $content ) . '</p></div></div><div class="'.$spanfollow.' columns">&nbsp;</div></div><div class="clear"></div>'; }
    else {
      $span .= ''.$columns.'"';
      return '<div class="row"><div class='.$span.'><div class="panel"><p>' . do_shortcode( $content ) . '</p></div></div></div><div class="clear"></div>';
    }
  }



} //class

$basestation_shortcodes = new BaseStation_Shortcodes();
endif;



/* Load the update checker */
require 'extensions/update-checker.php';
$BaseStationShortcodesUpdateChecker = new PluginUpdateChecker(
    'http://www.johnparris.com/deliver/wordpress/plugins/basestation-shortcodes/latest-version.json',
    __FILE__,
    'basestation-shortcodes'
);