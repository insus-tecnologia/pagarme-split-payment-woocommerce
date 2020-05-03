<?php
/**
 * Plugin Name: Pagar.me Split Payment for WooCommerce
 * Description: Allow you to define partners to split payment with using Pagar.me gateway.
 * Version: 1.0.0
 * Author: Raphael Batagini
 * Author URI: https://www.linkedin.com/in/raphael-batagini/
 * Text Domain: pagarme-split-woocommerce
 * Domain Path: /i18n/languages/
 *
 * @package PagarmeSplitWooCommerce
 */

defined( 'ABSPATH' ) || exit;

require "vendor/autoload.php";

(new \PagarmeSplitPayment\Cpts\CustomPostTypePartner())->run();