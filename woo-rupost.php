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
 * Plugin Name: Mihdan: Yandex Turbo Feed
 * Plugin URI: https://github.com/mihdan/woo-rupost
 * Description: Плагин генерирует фид для сервиса Яндекс Турбо
 * Version: 1.0.1
 * Author: Mikhail Kobzarev
 * Author URI: https://www.kobzarev.com/
 * License: GNU General Public License v2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: woo-rupost
 * GitHub Plugin URI: https://github.com/mihdan/woo-rupost
 * GitHub Branch:     master
 * Requires WP:       4.6
 * Requires PHP:      5.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Woo_Ru_Post' ) ) {
	class Woo_Ru_Post {}
}