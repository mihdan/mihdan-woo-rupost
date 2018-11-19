<?php
/**
 * RuPost for WooCommerce
 *
 * @package   woo-rupost
 * @author    Mikhail Kobzarev
 * @link      https://github.com/mihdan/mihdan-woo-rupost
 * @link      https://code.tutsplus.com/tutorials/create-a-custom-shipping-method-for-woocommerce--cms-26098
 * @link      https://github.com/appwilio/russianpost-sdk
 * @link      https://github.com/woocommerce/woocommerce/wiki/Shipping-Method-API
 * @copyright Copyright (c) 2017
 * @license   GPL-2.0+
 * @wordpress-plugin
 */

/**
 * Plugin Name: Mihdan: WooCommerce RU Post
 * Plugin URI: https://github.com/mihdan/mihdan-woo-rupost
 * Description: Плагин добавляет доставку Почтой России в WooCommerce
 * Version: 1.0
 * Author: Mikhail Kobzarev
 * Author URI: https://www.kobzarev.com/
 * License: GNU General Public License v2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: woo-rupost
 * GitHub Plugin URI: https://github.com/mihdan/mihdan-woo-rupost
 * GitHub Branch:     master
 * Requires WP:       4.6
 * Requires PHP:      5.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

/**
 * Check if WooCommerce is active
 */
if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	function wc_ru_post_shipping_method_init() {
		require_once __DIR__ . '/classes/class-wc-ru-post-shipping-method.php';
	}
	add_action( 'woocommerce_shipping_init', 'wc_ru_post_shipping_method_init' );

	function add_ru_post_shipping_method( $methods ) {
		$methods['ru_post_shipping_method'] = 'WC_RU_Post_Shipping_Method';
		return $methods;
	}
	add_filter( 'woocommerce_shipping_methods', 'add_ru_post_shipping_method' );
}

// eof;
