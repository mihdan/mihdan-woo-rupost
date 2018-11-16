<?php
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
			$this->supports           = array(
				'shipping-zones', //make your shipping method available for the shipping zones
				'settings', //use this for separate settings page
				'instance-settings', //add options for to edit the settings of your shipping method for a single zone
				'instance-settings-modal', // редактировать метод доставки в модалке.
			);
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

			$this->title   = $this->get_option( 'title' );
			$this->enabled = $this->get_option( 'enabled' );

			$this->init();

			//print_r($this->settings['title']);


		}
		/**
		 * Init your settings
		 *
		 * @access public
		 * @return void
		 */
		function init() {
			// Load the settings API
			$this->init_form_fields();
			$this->init_settings();
			$this->init_instance_form_fields();

			add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
		}

		function init_form_fields() {
			$this->form_fields = array(

				'enabled' => array(
					'title'       => __( 'Включён', '' ),
					'type'        => 'checkbox',
					'description' => __( 'Включить данный метод доставки', '' ),
					'default'     => 'yes'
				),

				'title' => array(
					'title'       => __( 'Заголовок', '' ),
					'type'        => 'text',
					'description' => __( 'Заголовок, отображаемый на сайте для данного метода доставки', '' ),
					'default'     => __( 'Почта России9', '' ),
				),
				'authorization_token' => array(
					'title'       => __( 'Токен авторизации приложения', '' ),
					'type'        => 'text',
					'description' => __( 'По токену API отпределяет приложение, проводит первоначальную проверку и <br />выделяет квоты на использование сервиса.', '' ),
					'placeholder' => 'Токен можно узнать в настройках личного кабинета',
				),
				'authorization_key' => array(
					'title'       => __( 'Ключ авторизации пользователя', '' ),
					'type'        => 'text',
					'description' => __( 'Не сохраняйте и не храните ваш пароль к API в текстовых файлах, <br />так как это может привести к его хищению и/или компрометации', '' ),
					'placeholder' => 'Генерится по формуле: base64(login:password)',
				),
			);
		}

		public function init_instance_form_fields() {
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
		public function calculate_shipping( $package = array() ) {
			$rate = array(
				'id' => $this->id,
				'label' => $this->title,
				'cost' => '100.99',
				//'calc_tax' => 'per_item'
			);
			// Register the rate
			$this->add_rate( $rate );
		}
	}
}

// eof;
