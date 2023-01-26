<?php

namespace WooScaleOptions\Options;

abstract class AbstractOption {
  abstract function get_html();
  abstract function get_label();
  abstract function get_type();
}