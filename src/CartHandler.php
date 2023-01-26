<?php

namespace WooScaleOptions;

class CartHandler {
  function __construct() {
    add_action( 'wp_loaded', [$this, 'maybe_add_multiple_products_to_cart'], 15 );
  }

  /**
   * Maybe add multiple products to cart.
   * 
   * @return void
   */
  function maybe_add_multiple_products_to_cart() {

    /**
     * Make sure WC is installed, and add-to-cart qauery arg exists, and contains at least one comma.
     */
    if ( ! class_exists( 'WC_Form_Handler' ) || empty( $_REQUEST['add-to-cart'] ) || false === strpos( $_REQUEST['add-to-cart'], ',' ) ) {
      return;
    }
  
    /**
     * Remove WooCommerce's hook, as it's useless (doesn't handle multiple products).
     */
    remove_action( 'wp_loaded', array( 'WC_Form_Handler', 'add_to_cart_action' ), 20 );

    $product_ids = explode( ',', $_REQUEST['add-to-cart'] );
    $count       = count( $product_ids );
    $number      = 0;
  
    foreach ( $product_ids as $product_id ) {
      if ( ++$number === $count ) {

        /**
         * Final item, let's send it back to WooCommerce's add_to_cart_action method for handling.
         */
        $_REQUEST['add-to-cart'] = $product_id;

        return \WC_Form_Handler::add_to_cart_action();
      }

      $product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $product_id ) );
      $was_added_to_cart = false;

      $adding_to_cart    = wc_get_product( $product_id );

      if ( ! $adding_to_cart ) {
        continue;
      }

      // only works for simple atm
      if ( $adding_to_cart->is_type( 'simple' ) ) {

        $quantity          = empty( $_REQUEST['quantity'] ) ? 1 : wc_stock_amount( $_REQUEST['quantity'] );
        $passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );

        if ( $passed_validation && false !== WC()->cart->add_to_cart( $product_id, $quantity ) ) {
          wc_add_to_cart_message( array( $product_id => $quantity ), true );
        }

      }
    }
  }
}