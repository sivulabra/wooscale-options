<?php

namespace WooScaleOptions\Options;

abstract class Option {
  abstract function get_html();
  abstract function get_label();
  abstract function get_type();
}