<?php

namespace WooScaleOptions\Options\Select;

use WooScaleOptions\Options\Select\Item;
use WooScaleOptions\Options\Option;

class Select extends Option {
  function __construct( $args ) {
    $this->data = [
      "type" => "select",
      "label" => $args["label"],
      "items" => $args["items"],
    ];
  }

  /**
   * Get item HTML as string.
   * 
   * @return string
   */
  function get_html() {
    $label = $this->get_label();
    $lower_case_label = str_replace( ' ', '', mb_strtolower( $label ) );
    $items = $this->get_items();

    $items_html = "";
    foreach( $items as $item ) {
      $items_html .= sprintf(
        '%s',
        $item->get_html()
      );
    }

    $html = sprintf(
      '
      <label
        for="wso-option-%s"
        required
      >
        %s
      </label>
      <select
        id="wso-option wso-select-option wso-option-%s"
        data-label="%s"
        data-type="select"
        name="wso-option-%s"
        required
      >
        <option value>Valitse</option>
        %s
      </select>
      ',
      esc_html( $lower_case_label ),
      esc_html( $label ),
      esc_html( $lower_case_label ),
      esc_html( $label ),
      esc_html( $lower_case_label ),
      $items_html
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
   * Get items.
   * 
   * @return array
   */
  function get_items() {
    return $this->get_prop( 'items' );
  }

}