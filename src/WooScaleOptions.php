<?php

use WooScaleOptions\API;
use WooScaleOptions\Assets;
use WooScaleOptions\CartHandler;
use WooScaleOptions\Accessories\Accessory;
use WooScaleOptions\Options\Select\Select;
use WooScaleOptions\Options\Select\Item;
use WooScaleOptions\Options\Number\Number;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class WooScaleOptions {
  
  /**
   * Class constructor.
   */
  function __construct() {

    /**
     * Autoload composer.
     */
    require_once WOOSCALE_OPTIONS_PLUGIN_DIR_PATH . '/vendor/autoload.php';

    /**
     * ACF not activated, abort.
     */
    if ( ! class_exists( 'ACF' ) ) {
      return;
    }

    /**
     * WooCommerce not activated, abort.
     */
		if ( ! defined( 'WC_VERSION' ) ) {
			return;
		}

    /**
     * Plugin container and totals on single product page.
     */
    add_action( 'woocommerce_before_add_to_cart_button', [$this, 'display_container_start'], 5 );
    add_action( 'woocommerce_before_add_to_cart_button', [$this, 'display_options'], 6 );
    add_action( 'woocommerce_before_add_to_cart_button', [$this, 'display_accessories'], 7 );
    add_action( 'woocommerce_before_add_to_cart_button', [$this, 'display_totals'], 9 );
    add_action( 'woocommerce_before_add_to_cart_button', [$this, 'display_container_end'], 10 );

    /**
     * Validate options fields.
     */
    add_filter( 'woocommerce_add_to_cart_validation', [$this, 'validate_options_fields'], 10, 3 );

    /**
     * Calculate total price.
     */
    add_action( 'woocommerce_before_calculate_totals', [$this, 'calculate_total_price'], 10, 1 );

    /**
     * Add the custom data to the cart object and display it where needed.
     */
    add_filter( 'woocommerce_add_cart_item_data', [$this, 'add_options_field_data_to_cart'], 10, 4 );
    add_filter( 'woocommerce_cart_item_name', [$this, 'add_cart_item_name'], 10, 3 );

    /**
     * 
     */
    add_action( 'woocommerce_checkout_create_order_line_item', [$this, "add_custom_data_to_order"], 10, 4 );

    /**
     * Instantiate classes.
     */
    // $api = new API();
    $assets = new Assets();
    $cart_handler = new CartHandler();

  }

  function display_container_start() {
    ?>
    <div id="wso-container" class="wso-container">
    <?php
  }

  function display_options() {
    $options = $this->get_options();
    if ( ! isset( $options ) || empty( $options ) ) {
      return;
    }

    ?>
    <div class="wso-options">
      <h3 class="wso-title">Valitse vaihtoehdoista</h3>
      <div id="wso-options" class="wso-options-loop">
      <?php
      foreach( $options as $option ) {
        echo $option->get_html();
      }
      ?>
      </div>
    </div>
    <?php
  }

  function display_accessories() {
    $accessories = $this->get_accessories();
    if ( ! isset( $accessories ) || empty ( $accessories ) ) {
      return;
    }

    ?>
    <div class="wso-accessories">
      <h3 class="wso-title">
        Valitse lisävarusteet
      </h3>
      <div id="wso-accessories" class="wso-accessories-loop">
      <?php
      foreach ( $accessories as $accessory ) {
        ?>
        <div
          data-product-id="<?php echo esc_html( $accessory->get_product_id() ); ?>"
          data-product-name="<?php echo esc_html( $accessory->get_name() ) ?>"
          data-product-price="<?php echo esc_html( $accessory->get_price() ); ?>"
          data-selected="false"
          class="wso-accessory"
        >
          <img src="<?php echo esc_html( $accessory->get_thumbnail_url() ); ?>" width="64" height="64">
          <div class="wso-accessory-info">
            <span class="wso-accessory-name"><?php echo esc_html( $accessory->get_name() ); ?></span>
            <p class="wso-accessory-desc"><?php echo esc_html( $accessory->get_description() ); ?></p>
          </div>
          <div class="wso-accessory-price"><span><?php echo esc_html( $accessory->get_price() ); ?> €</span></div>
        </div>
        <?php
      }
      ?>
      </div>
    </div>
    <?php
  }

  function display_container_end() {
    ?>
    </div>
    <?php
  }

  /**
   * Validate options fields when adding to cart.
   * 
   * @param bool $passed
   * @param int $product_id
   * @param int $quantity
   * @return bool True if passed, false otherwise.
   */
  function validate_options_fields( $passed, $product_id, $quantity ) {
    $options = $this->get_options( $product_id );

    if ( ! isset( $options ) || empty( $options ) ) {
      return $passed;
    }

    foreach ( $options as $option ) {
      $label = $option->get_label();
      $lower_case_label = str_replace( ' ', '', mb_strtolower( $label ) );

      /**
       * Check that this value exists in $_POST.
       */
      if ( ! isset( $_POST['wso-option-' . $lower_case_label] ) ) {
        $passed = false;
        wc_add_notice( 'Valitse "' . esc_html( $label ) . '" lisätäksesi tuotteen koriin.' );
      }

      $length_of_value_string = strlen( strval( $_POST['wso-option-' . $lower_case_label] ) );

      if ( $length_of_value_string === 0 ) {
        $passed = false;
        wc_add_notice( 'Valitse "' . esc_html( $label ) . '" lisätäksesi tuotteen koriin.' );
      }
    }
    return $passed;
  }

  /**
   * Add the options as item data to the cart object.
   * 
   * @return array
   */
  function add_options_field_data_to_cart( $cart_item_data, $product_id, $variation_id, $quantity ) {
    $options = $this->get_options( $product_id );

    if ( ! isset( $options ) || empty( $options ) ) {
      return $cart_item_data;
    }

    foreach ( $options as $option ) {
      $label = $option->get_label();
      $lower_case_label = str_replace( ' ', '', mb_strtolower( $label ) );
      $selected_value = $_POST['wso-option-' . $lower_case_label];

      if ( ! empty( $selected_value ) ) {
        $type = $option->get_type();
        $product = wc_get_product( $product_id );

        /**
         * Handle select type option.
         */
        if ( $type === "select" ) {

          // Get the index of the item in the array of items.
          $index = (int) substr( $selected_value, -1 );
  
          // Get the option's price.
          $option_price = $option->get_items()[$index - 1]->get_price();
  
          // Set the new price.
          $new_price = isset( $cart_item_data['wso_total_price'] )
            ? $cart_item_data['wso_total_price'] + $option_price
            : $product->get_price() + $option_price;
          $cart_item_data['wso_total_price'] = $new_price;
  
          // Save data about the option to cart item data.
          $cart_item_data['wso_options'][] = [
            'option_label' => $label,
            'value_label' => $option->get_items()[$index - 1]->get_label(),
            'price' => $option_price
          ];
        }

        /**
         * Handle number type option.
         */
        else if ( $type === "number" ) {

          // Get the option price per unit and use it to calculate the total.
          $option_price_per_unit = $option->get_price_per_unit();
          $option_price_total = $option_price_per_unit * intval( $selected_value );

          // Set the new price.
          $new_price = isset( $cart_item_data['wso_total_price'] )
            ? $cart_item_data['wso_total_price'] + $option_price_total
            : $product->get_price() + $option_price_total;
          $cart_item_data['wso_total_price'] = $new_price;

          // Save data about the option to cart item data.
          $cart_item_data['wso_options'][] = [
            'option_label' => $label,
            'value_label' => "{$selected_value} kpl",
            'price' => $option_price_total
          ];
        }
      }
    }

    return $cart_item_data;
  }


  /**
   * Display selected value and the price increment on cart and checkout pages.
   * 
   * @return string Name of the cart item.
   */
  function add_cart_item_name( $name, $cart_item, $cart_item_key ) {
    if ( isset( $cart_item['wso_options'] ) ) {
      $name .= "<div class='wso-cart'>";

      /**
       * Get product price without accessories or options.
       */
      $product = $cart_item['data'];
      if ( $product->is_on_sale() ) {
        $product_price = $product->get_sale_price();
      } else {
        $product_price = $product->get_regular_price();
      }
      $name .= sprintf(
        '<p class="wso-cart-item">Tuote: <strong>%s</strong> (%s €)</p>',
        esc_html( $product->get_name() ),
        esc_html( $product_price )
      );
      foreach( $cart_item['wso_options'] as $option ) {
        $name .= sprintf(
          '<p class="wso-cart-item">%s: <strong>%s</strong> (+%s €)</p>',
          esc_html( $option['option_label'] ),
          esc_html( $option['value_label'] ),
          esc_html( $option['price'] )
        );
      }

      $name .= "</div>";
    }

    return $name;
  }

  /**
   * Update the price in the cart.
   * 
   * @return void
   */
  function calculate_total_price( $cart_obj ) {
    if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
      return;
    }

    foreach( $cart_obj->get_cart() as $key => $value ) {
      if ( isset( $value['wso_total_price'] ) ) {
        $price = $value['wso_total_price'];
        $value['data']->set_price( ( $price ) );
      }
    }
  }

  /**
   * Display totals table.
   * 
   * @return void
   */
  function display_totals() {
    global $post;

    $options = $this->get_options();
    if ( ! isset( $options ) && empty( $options ) ) {
      return;
    }

    $product = wc_get_product( $post->ID );
    $name = $product->get_name();
    $id = $product->get_id();
    $price = $product->get_price();

    ?>
    <div
      id="wso-totals"
      class="wso-totals"
      data-product-name="<?php echo esc_html( $name ); ?>"
      data-product-id="<?php echo esc_html( $id ); ?>"
      data-price="<?php echo esc_html( $price ); ?>"
    ></div>
    <?php
  }

  /**
   * Get accessories.
   * 
   * @return array
   */
  function get_accessories() {
    $accessories_field = get_field( 'accessories' );
    if ( ! isset( $accessories_field ) || empty( $accessories_field ) ) {
      return [];
    }
    $accessories = [];
    foreach ( $accessories_field as $accessory ) {
      $product = wc_get_product( $accessory["product"] );

      /**
       * Extract values.
       */
      $name = $product->get_name();
      $description = wp_trim_words( $product->get_short_description(), 12 );
      $price = $product->get_price();
      if ( ! $product->get_image_id() ) {
        $thumbnail_url = wc_placeholder_img_src();
      } else {
        $thumbnail_url = wp_get_attachment_image_url( $product->get_image_id(), 'thumbnail' );
      }
      $product_id = $product->get_id();

      $accessories[] = new Accessory(
        [
          "name" => $name,
          "description" => $description,
          "price" => $price,
          "thumbnail_url" => $thumbnail_url,
          "product_id" => $product_id
        ]
      );
    }
    return $accessories;
  }

  /**
   * Get options.
   * 
   * @param int $product_id
   * @return array
   */
  function get_options( $product_id = NULL ) {
    if ( $product_id ) {
      $options_field = get_field( 'options', $product_id );
    } else {
      $options_field = get_field( 'options' );
    }

    if ( ! isset( $options_field ) || empty( $options_field ) ) {
      return [];
    }

    $options = [];
    foreach ( $options_field as $option ) {
      $type = $option["type"];
      $label = $option["label"];

      /**
       * Type: select
       */
      if ( $type === "select" ) {
        $items = [];
        foreach ( $option["items"] as $index => $item ) {
          $item_args = [
            "label" => $item["label"],
            "index" => $index + 1,
            "price" => $item["price"]
          ];
          $items[] = new Item( $item_args );
        }

        $options[] = new Select(
          [
            "label" => $label,
            "items" => $items
          ]
        );
      } 
      
      /**
       * Type: number
       */
      else if ( $type === "number" ) {
        $price_per_unit = $option["number"]["price"];
        $default = $option["number"]["default"];
        $options[] = new Number( [
          "label" => $label,
          "price_per_unit" => $price_per_unit,
          "default" => $default
        ] );
      }
    }

    return $options;
  }

  function add_custom_data_to_order( $item, $cart_item_key, $values, $order ) {
    foreach ( $item as $cart_item_key => $values ) {
      if ( isset( $values['wso_options'] ) ) {
        $custom_metas = $values['wso_options'];
        foreach ( $custom_metas as $custom_meta ) {
          $item->add_meta_data( $custom_meta["option_label"], $custom_meta["value_label"] . " (+ {$custom_meta["price"]} €)", true );
        }
      }
    }
  }

}