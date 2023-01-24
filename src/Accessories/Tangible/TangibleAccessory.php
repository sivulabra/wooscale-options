<?php

namespace WooScaleOptions\Accessories\Tangible;

use WooScaleOptions\Accessories\Accessory;

class TangibleAccessory extends Accessory {
  function __construct( $args ) {
    $this->data = [
      "name" => $args["name"],
      "description" => $args["description"],
      "thumbnail" => $args["thumbnail"],
      "price" => $args["price"]
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
   * Get thumbnail
   * 
   * @return string
   */
  function get_thumbnail() {
    return $this->get_prop( 'thumbnail' );
  }

  /**
   * Get price.
   * 
   * @return float
   */
  function get_price() {
    return $this->get_prop( 'price' );
  }
}