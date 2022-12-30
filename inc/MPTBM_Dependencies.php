<?php
	if ( ! defined( 'ABSPATH' ) ) {
		die;
	} // Cannot access pages directly.
	if ( ! class_exists( 'MPTBM_Dependencies' ) ) {
		class MPTBM_Dependencies {
			public function __construct() {
				add_action( 'init', array( $this, 'language_load' ) );
				$this->load_file();
				add_action( 'wp_enqueue_scripts', array( $this, 'global_enqueue' ), 90 );
				add_action( 'admin_enqueue_scripts', array( $this, 'global_enqueue' ), 90 );
				add_action( 'admin_head', array( $this, 'js_constant' ), 5 );
				add_action( 'wp_head', array( $this, 'js_constant' ), 5 );
			}
			public function language_load(): void {
				$plugin_dir = basename( dirname( __DIR__ ) ) . "/languages/";
				load_plugin_textdomain( 'mptbm_plugin', false, $plugin_dir );
			}
			private function load_file(): void {
				require_once MPTBM_PLUGIN_DIR . '/inc/MPTBM_Function.php';
				require_once MPTBM_PLUGIN_DIR . '/inc/MPTBM_Query.php';
				require_once MPTBM_PLUGIN_DIR . '/inc/MPTBM_Layout.php';
				require_once MPTBM_PLUGIN_DIR . '/inc/MPTBM_Style.php';
				require_once MPTBM_PLUGIN_DIR . '/inc/MPTBM_Super_Slider.php';
				require_once MPTBM_PLUGIN_DIR . '/Admin/MPTBM_Admin.php';
				require_once MPTBM_PLUGIN_DIR . '/Frontend/MPTBM_Frontend.php';
			}
			public function global_enqueue() {
				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'jquery-ui-core' );
				wp_enqueue_script( 'jquery-ui-datepicker' );
				wp_localize_script( 'jquery', 'mptbm_ajax', array( 'mptbm_ajax_url' => admin_url( 'admin-ajax.php' ) ) );
				wp_enqueue_style( 'mptbm_font_awesome', '//cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css', array(), '5.15.4' );
				wp_enqueue_script( 'mptbm_map', 'https://maps.googleapis.com/maps/api/js?libraries=places&amp;language=en&amp;key=AIzaSyD61CGRsenVDXkRMrBzxQnVTtL7EZz0k_c', array( 'jquery' ), time(), true );
				wp_enqueue_style( 'mptbm-jquery-ui-style', MPTBM_PLUGIN_URL . '/assets/helper/css/jquery-ui.css' );
				//wp_enqueue_style( 'mptbm_date_range_picker', MPTBM_PLUGIN_URL . '/assets/date_range_picker/date_range_picker.min.css', array(), '1' );
				//wp_enqueue_script( 'mptbm_date_range_picker', MPTBM_PLUGIN_URL . '/assets/date_range_picker/date_range_picker.js', array( 'jquery', 'moment' ), '1', true );
				wp_enqueue_style( 'mp_plugin_global', MPTBM_PLUGIN_URL . '/assets/helper/mp_style/mp_style.css', array(), time() );
				wp_enqueue_script( 'mp_plugin_global', MPTBM_PLUGIN_URL . '/assets/helper/mp_style/mp_script.js', array( 'jquery' ), time(), true );
				do_action( 'mptbm_global_enqueue' );
			}
			public function js_constant() {
				?>
				<script type="text/javascript">
							let mptbm_ajax_url = "<?php echo admin_url( 'admin-ajax.php' ); ?>";
							let mptbm_currency_symbol = "<?php echo get_woocommerce_currency_symbol(); ?>";
							let mptbm_currency_position = "<?php echo get_option( 'woocommerce_currency_pos' ); ?>";
							let mptbm_currency_decimal = "<?php echo wc_get_price_decimal_separator(); ?>";
							let mptbm_currency_thousands_separator = "<?php echo wc_get_price_thousand_separator(); ?>";
							let mptbm_num_of_decimal = "<?php echo get_option( 'woocommerce_price_num_decimals', 2 ); ?>";
							let mptbm_empty_image_url = "<?php echo esc_attr( MPTBM_PLUGIN_URL . '/assets/helper/images/no_image.png' ); ?>";
							let mptbm_date_format = "<?php echo esc_attr( MPTBM_Function::get_general_settings( 'date_format', 'D d M , yy' ) ); ?>";
							let mptbm_lat_lng = {lat: 23.81234828905659, lng: 90.41069652669002};
				</script>
				<?php
			}
		}
		new MPTBM_Dependencies();
	}