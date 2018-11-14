<?php
/**
 * RuPost for WooCommerce
 *
 * @package   woo-rupost
 * @author    Mikhail Kobzarev
 * @link      https://github.com/mihdan/mihdan-woo-rupost
 * @link      https://code.tutsplus.com/tutorials/create-a-custom-shipping-method-for-woocommerce--cms-26098
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
				public function __construct( $instance_id = 0 ) {
					$this->instance_id        = absint( $instance_id );
					$this->id                 = 'ru_post_shipping_method'; // Id for your shipping method. Should be uunique.
					$this->method_title       = __( 'Доставка почтой России' );  // Title shown in admin
					$this->method_description = __( 'Плагин позволяет автоматически рассчитать стоимость доставка "Почтой России" на странице оформления заказа' );
					$this->enabled            = 'yes'; // This can be added as an setting but for this example its forced enabled
					$this->title              = 'Почта России'; // This can be added as an setting but for this example its forced.
//					$this->availability       = 'including'; // Доступно для стран ниже
//					$this->countries          = array(
//						'RU', // Russia
//						'CA', // Canada
//						'DE', // Germany
//						'GB', // United Kingdom
//						'IT', // Italy
//						'ES', // Spain
//						'HR', // Croatia
//					);
					$this->supports           = array(
						'shipping-zones',
						'instance-settings',
					);

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

				function init_form_fields() {
					$this->form_fields = array(

						'enabled' => array(
							'title'       => __( 'Включён', 'tutsplus' ),
							'type'        => 'checkbox',
							'description' => __( 'Включить данный метод доставки', 'tutsplus' ),
							'default'     => 'yes'
						),

						'title' => array(
							'title'       => __( 'Заголовок', 'tutsplus' ),
							'type'        => 'text',
							'description' => __( 'Заголовок, отображаемый на сайте для данного метода доставки', 'tutsplus' ),
							'default'     => __( 'Почта России', 'tutsplus' )
						),

					);
				}

				public function init_settings() {
					$this->instance_form_fields  = array(
						'title' => array(
							'title' 		=> __( 'Title', 'rpaefw-post-calc' ),
							'type' 			=> 'text',
							'default'		=> __( 'Russian Post', 'rpaefw-post-calc' ),
						),
						'from' => array(
							'title' 		=> __( 'Оrigin Postcode', 'rpaefw-post-calc' ),
							'description' 	=> __( '6-digit code of the sender.', 'rpaefw-post-calc' ),
							'type' 			=> 'number',
						),
						'addcost' => array(
							'title' 		=> __( 'Сost', 'rpaefw-post-calc' ),
							'description' 	=> __( 'Additional flat rate for shipping method. This may be the average value of the package or the cost of fuel, spent on the road to the post;)', 'rpaefw-post-calc' ),
							'type' 			=> 'number',
							'default'		=> 0,
						),
						'type' => array(
							'title' 		=> __( 'Type', 'rpaefw-post-calc' ),
							'type' 			=> 'select',
							'default' 		=> 'ЦеннаяПосылка',
							'options'		=> array(
								'ПростаяБандероль'           => __( 'Simple wrapper', 'rpaefw-post-calc' ),
								'ЗаказнаяБандероль'          => __( 'Custom wrapper', 'rpaefw-post-calc' ),
								'ЗаказнаяБандероль1Класс'    => __( 'Custom wrapper 1 class', 'rpaefw-post-calc' ),
								'ЦеннаяБандероль'            => __( 'Valued wrapper', 'rpaefw-post-calc' ),
								'ПростаяПосылка'             => __( 'Simple Parcel', 'rpaefw-post-calc' ),
								'ЦеннаяПосылка'              => __( 'Valued parcel', 'rpaefw-post-calc' ),
								'ЦеннаяАвиаБандероль'        => __( 'Valued avia wrapper', 'rpaefw-post-calc' ),
								'ЦеннаяАвиаПосылка'          => __( 'Valued avia parcel', 'rpaefw-post-calc' ),
								'ЦеннаяБандероль1Класс'      => __( 'Valued wrapper 1 class', 'rpaefw-post-calc' ),
								'EMS'                        => __( 'EMS', 'rpaefw-post-calc' ),

								'МждМешокМ'                  =>	'Международный мешок М',
								'МждМешокМАвиа'              =>	'Международный мешок М авиа',
								'МждМешокМЗаказной'          =>	'Международный мешок М заказной',
								'МждМешокМАвиаЗаказной'      =>	'Международный мешок М авиа заказной',
								'МждБандероль'               => 'Международная бандероль',
								'МждБандерольАвиа'           => 'Международная авиабандероль',
								'МждБандерольЗаказная'       => 'Международная бандероль заказная',
								'МждБандерольАвиаЗаказная'   => 'Международная авиабандероль заказная',
								'МждМелкийПакет' 		     =>	'Международный мелкий пакет',
								'МждМелкийПакетАвиа'         =>	'Международный мелкий пакет авиа',
								'МждМелкийПакетЗаказной'     =>	'Международный мелкий пакет заказной',
								'МждМелкийПакетАвиаЗаказной' =>	'Международный мелкий пакет авиа заказной',
								'МждПосылка'                 => 'Международная посылка',
								//'МждПосылкаАвиа'             => 'Международная авиапосылка',
								'EMS_МждДокументы'           => 'ЕMS международное - документы',
								'EMS_МждТовары'              =>	'ЕMS международное - товары',
							)
						),

						'fixedpackvalue' => array(
							'title' 		=> __( 'Max. Fixed Package Value', 'rpaefw-post-calc' ),
							'description' 	=> __( 'You can set max. fixed value for some types of departure. By default value equals sum of the order.', 'rpaefw-post-calc'),
							'type' 			=> 'number',
						),

						// Packaging
						'addpackweight' => array(
							'title' 		=> __( 'Packaging', 'rpaefw-post-calc' ),
							'description' 	=> __( 'Weight of the one packaging in grams. This weight will be added to the total weight of the order.', 'rpaefw-post-calc'),
							'type' 			=> 'number',
							'default'		=> 0,
						),
						'addpackcost' => array(
							'description' 	=> __( 'Cost of the one packaging. This cost will be added to the final amount of delivery.', 'rpaefw-post-calc'),
							'type' 			=> 'number',
							'default'		=> 0,
						),

						'fixedvalue_disable' => array(
							'title' 		=> __( 'Min. cost of order', 'rpaefw-post-calc' ),
							'description' 	=> __( 'Disable this method if the cost of the order is less than inputted value. Leave this field empty to allow any order cost.', 'rpaefw-post-calc'),
							'type' 			=> 'number',
						),

						'overweight_disable' => array(
							'title' 		=> __( 'Do not allow overweight', 'rpaefw-post-calc' ),
							'type' 			=> 'checkbox',
							'label' 		=> __( 'Disable this method in case of overweight.', 'rpaefw-post-calc' ),
							'description' 	=> __( 'Hide this method if package weight is heavier than the allowed weight for a chosen type of departure. By default, if there is overweight the package will be split into two or more packages.', 'rpaefw-post-calc' ),
							'default' 		=> 'no',
						),

						// Message options
						'overweight' => array(
							'title' 		=> __( 'Notice of overweight', 'rpaefw-post-calc' ),
							'type' 			=> 'checkbox',
							'label' 		=> __( 'Show a notice about overweight.', 'rpaefw-post-calc' ),
							'description' 	=> __( 'Show a message to the customer if the total weight of the order exceeds the permitted weight for specific type of delivery', 'rpaefw-post-calc' ),
							'default' 		=> 'no',
						),
						'overweighttext' => array(
							'type' 			=> 'textarea',
							'default'		=> 'превышен максимально допустимый вес. В случае выбора данного метода Ваш заказ будет разбит на несколько отправлений.',
						),

						'time' => array(
							'title' 		=> __( 'Delivery Time', 'rpaefw-post-calc' ),
							'type' 			=> 'checkbox',
							'label' 		=> __( 'Show time of delivery.', 'rpaefw-post-calc' ),
							'description' 	=> __( 'Displayed next to the title. For international shipments, it works only for EMS - international.', 'rpaefw-post-calc' ),
							'default' 		=> 'no',
						),
					);
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
