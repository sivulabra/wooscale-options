<?php

namespace WooScaleOptions;

use WP_Error;

class API {
  function __construct() {
    $this->version = '1';
    $this->namespace = 'wooscale-options/v' . $this->version;

    add_action( 'rest_api_init', [$this, 'register_endpoints'] );
  }

  /**
   * Register custom WP REST API endpoints.
   * 
   * @return void
   */
  function register_endpoints() {
    register_rest_route( $this->namespace, 'add-to-cart', [
      [
        'methods' => 'POST',
        'callback' => [$this, 'add_to_cart'],
        'permission_callback' => '__return_true'
      ]
    ] );
  }

  /**
   * Handle add-to-cart endpoint.
   * 
   * @param WP_REST_Request $request
   */
  function add_to_cart( $request ) {
    if ( ! $request->has_param( 'ids' ) ) {
      return new WP_Error( 400, "You didn't specify any product IDs." );
    }

    $unsanitized_ids = explode( ',', $request->get_param( "ids" ) );
    $ids = [];
    foreach( $unsanitized_ids as $unsanitized_id ) {
      if ( ! is_numeric( $unsanitized_id ) ) {
        return new WP_Error( 400, "Invalid product IDs. Please provide a comma-separated list of product IDs as numbers only." );
      }
      $sanitized_id = sanitize_text_field( $unsanitized_id );
      $ids[] = intval( $sanitized_id );
    }

    foreach( $ids as $id ) {
      WC()->cart->add_to_cart( $id );
    }

    return [
      "success" => true,
      "added" => $ids
    ];
  }
}