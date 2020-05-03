<?php
/**
 * Plugin Name: Pagar.me Split Payment for WooCommerce
 * Description: Allow you to define partners to split payment with using Pagar.me gateway.
 * Version: 1.0.0
 * Author: Raphael Batagini
 * Author URI: https://www.linkedin.com/in/raphael-batagini/
 * Text Domain: pagarme-split-payment
 * Domain Path: /i18n/languages/
 *
 * @package PagarmeSplitPayment
 */

defined( 'ABSPATH' ) || exit;
define('PLUGIN_NAME', 'Pagar.me Split Payment');

require "vendor/autoload.php";

class PagarmeSplitWooCommerce {
    public static function run()
    {
        \Carbon_Fields\Carbon_Fields::boot();
        (new \PagarmeSplitPayment\Cpts\CustomPostTypePartner())->create();
        (new \PagarmeSplitPayment\Cpts\CustomPostTypeProduct())->create();
    }
}

PagarmeSplitWooCommerce::run();
