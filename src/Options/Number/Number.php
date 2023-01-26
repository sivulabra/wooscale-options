<?php

namespace WooScaleOptions\Options\Number;

use WooScaleOptions\Options\AbstractOption;

class Number extends AbstractOption {
  function __construct( $args ) {
    $this->data = [
      "type" => "number",
      "label" => $args["label"],
      "price_per_unit" => $args["price_per_unit"],
      "default" => $args["default"],
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
    $default = $this->get_default();
    $price_per_unit = $this->get_price_per_unit();    

    $html = sprintf(
      '
      <label
        for="wso-option-%s"
        required
      >
        %s
      </label>
      <input
        type="number"
        id="wso-option wso-number-option wso-option-%s"
        data-type="number"
        data-label="%s"
        data-price-per-unit="%f"
        min="0"
        step="1"
        value="%d"
        name="wso-option-%s"
        required
      />
      ',
      esc_html( $lower_case_label ),
      esc_html( $label ),
      esc_html( $lower_case_label ),
      esc_html( $label ),
      esc_html( $price_per_unit ),
      esc_html( $default ),
      esc_html( $lower_case_label ),
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
   * Get type.
   * 
   * @return string
   */
  function get_type() {
    return $this->get_prop( 'type' );
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
   * Get price per unit.
   * 
   * @return float
   */
  function get_price_per_unit() {
    return $this->get_prop( 'price_per_unit' );
  }

  /**
   * Get default.
   * 
   * @return string
   */
  function get_default() {
    return $this->get_prop( 'default' );
  }

}