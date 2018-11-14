<?php
/**
 * RuPost for WooCommerce
 *
 * @package   woo-rupost
 * @author    Mikhail Kobzarev
 * @link      https://github.com/mihdan/woo-rupost
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
if( is_plugin_active(  'woocommerce/woocommerce.php' ) ) {
	function wc_ru_post_shipping_method_init() {
		if ( ! class_exists( 'WC_RU_Post_Shipping_Method' ) ) {
			class WC_RU_Post_Shipping_Method extends WC_Shipping_Method {
				/**
				 * Constructor for your shipping class
				 *
				 * @access public
				 * @return void
				 */
				public function __construct() {
					$this->id                 = 'ru_post_shipping_method'; // Id for your shipping method. Should be uunique.
					$this->method_title       = __( 'Доставка почтой России' );  // Title shown in admin
					$this->method_description = __( 'Доставка почтой России описание' ); // Description shown in admin
					$this->enabled            = 'yes'; // This can be added as an setting but for this example its forced enabled
					$this->title              = 'Почта России'; // This can be added as an setting but for this example its forced.
					$this->init();
				}
				/**
				 * Init your settings
				 *
				 * @access public
				 * @return void
				 */
				function init() {
					// Load the settings API
					$this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
					$this->init_settings(); // This is part of the settings API. Loads settings you previously init.
					// Save settings in admin if you have any defined
					add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
				}
				/**
				 * calculate_shipping function.
				 *
				 * @access public
				 * @param mixed $package
				 * @return void
				 */
				public function calculate_shipping( $package ) {
					$rate = array(
						'id' => $this->id,
						'label' => $this->title,
						'cost' => '10.99',
						'calc_tax' => 'per_item'
					);
					// Register the rate
					$this->add_rate( $rate );
				}
			}
		}
	}
	add_action( 'woocommerce_shipping_init', 'wc_ru_post_shipping_method_init' );

	function add_ru_post_shipping_method( $methods ) {
		$methods['ru_post_shipping_method'] = 'WC_RU_Post_Shipping_Method';
		return $methods;
	}
	add_filter( 'woocommerce_shipping_methods', 'add_ru_post_shipping_method' );
}

// eof;
