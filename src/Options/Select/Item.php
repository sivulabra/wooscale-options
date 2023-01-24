<?php

namespace WooScaleOptions\Options\Select;

class Item {
  function __construct( $args ) {
    $this->data = [
      "label" => $args["label"],
      "index" => $args["index"],
      "price" => $args["price"]
    ];
  }

  /**
   * Get item HTML.
   * 
   * @return string
   */
  function get_html() {
    $label = $this->get_label();
    $lower_case_label = str_replace( ' ', '', mb_strtolower( $label ) );
    $index = $this->get_index();
    $price = $this->get_price();

    $html = sprintf(
      '<option data-price="%s" data-label="%s" value="%s-%d">%s (+%s €)</option>',
      esc_html( round( $price, 2 ) ),
      esc_html( $label ),
      esc_html( $lower_case_label ),
      esc_html( $index ),
      esc_html( $label ),
      esc_html( round( $price, 2 ) )
    );

    return $html;
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
   * Get label.
   * 
   * @return string
   */
  function get_label() {
    return $this->get_prop( 'label' );
  }

  /**
   * Get index.
   * 
   * @return int
   */
  function get_index() {
    return $this->get_prop( 'index' );
  }

  /**
   * Get index.
   * 
   * @return float
   */
  function get_price() {
    return $this->get_prop( 'price' );
  }

}