<?php

namespace WooScaleOptions;

/**
 * Class for enqueueing plugin assets (CSS and JS).
 */
class Assets {
  function __construct() {
    add_action( 'wp_enqueue_scripts', [$this, 'enqueue_assets'] );
  }

  /**
   * Enqueue plugin assets.
   * 
   * @return void
   */
  function enqueue_assets() {
    wp_enqueue_style( 'wso-global', WOOSCALE_OPTIONS_PLUGIN_DIR_URL . 'build/index.css', NULL, filemtime( WOOSCALE_OPTIONS_PLUGIN_DIR_PATH . "build/index.css" ) );
    wp_enqueue_script( 'wso', WOOSCALE_OPTIONS_PLUGIN_DIR_URL . 'build/index.js', ['jquery'], filemtime( WOOSCALE_OPTIONS_PLUGIN_DIR_PATH . "build/index.js" ), true );
  }
}