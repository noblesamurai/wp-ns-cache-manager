<?php
/**
 * Plugin Name: Cache Manager
 * Description: Allow you to selectively disable cache on individual pages.
 * Author: Noble Samurai
 * Version: 1.0
 * Author URI: http://www.noblesamurai.com/
 */

class SamuraiCacheManager
{
  // init everything
  public static function init() {
    add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_box' ) );
    add_action( 'save_post', array( __CLASS__, 'save_meta_data' ) );
    add_action( 'the_post', array( __CLASS__, 'disable_cache' ) );
  }

  public static function add_meta_box() {
    add_meta_box(
      'ns-cache-manager',                       // id
      __( 'Cache Manager' ),                    // title
      array( __CLASS__, 'display_meta_box' ));  // callback
  }

  public static function display_meta_box( $post ) {
    $disabled = get_post_meta( $post->ID, 'ns_cache_disabled', true );
    ?>
      <label>
        <input type="checkbox" name="ns_cache_disabled" value="yes" <?php checked( true, $disabled ); ?> />
        Disable caching on this page.
      </label>
    <?php
  }

  public static function save_meta_data( $post_id ) {
    if ( isset( $_REQUEST['ns_cache_disabled'] ) ) {
      add_post_meta( $post_id, 'ns_cache_disabled', true, true );
    } else {
      delete_post_meta( $post_id, 'ns_cache_disabled' );
    }
  }

  public static function disable_cache( $post ) {
    $disabled = get_post_meta( $post->ID, 'ns_cache_disabled', true );
    if ( $disabled ) {
      if ( function_exists( 'batcache_cancel' ) ) batcache_cancel();          // batcache
      if ( ! defined( 'DONOTCACHEPAGE' ) ) define( 'DONOTCACHEPAGE', true );  // w3 total cache
    }
  }
}

SamuraiCacheManager::init();
