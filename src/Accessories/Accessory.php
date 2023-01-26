<?php

namespace WooScaleOptions\Accessories;

class Accessory {
  function __construct( $args ) {
    $this->data = [
      "name" => $args["name"],
      "description" => $args["description"],
      "price" => $args["price"],
      "thumbnail_url" => $args["thumbnail_url"],
      "product_id" => $args["product_id"]
    ];
  }

  /**
   * Get class property by name.
   * 
   * @return mixed
   */
  function get_prop( $name ) {
    return $this->data[$name];
  }

  /**
   * Get name.
   * 
   * @return string
   */
  function get_name() {
    return $this->get_prop( 'name' );
  }

  /**
   * Get description.
   * 
   * @return string
   */
  function get_description() {
    return $this->get_prop( 'description' );
  }

  /**
   * Get price.
   * 
   * @return float
   */
  function get_price() {
    return $this->get_prop( 'price' );
  }

  /**
   * Get thumbnail.
   * 
   * @return string
   */
  function get_thumbnail_url() {
    return $this->get_prop( 'thumbnail_url' );
  }

  /**
   * Get product ID.
   * 
   * @return string
   */
  function get_product_id() {
    return $this->get_prop( 'product_id' );
  }
}