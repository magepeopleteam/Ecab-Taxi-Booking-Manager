<?php
	/**
	 * Plugin Name: Hire a car or taxi booking manager
	 * Plugin URI: http://mage-people.com
	 * Description: A Complete Transportation Solution for WordPress by MagePeople.
	 * Version: 1.0.0
	 * Author: MagePeople Team
	 * Author URI: http://www.mage-people.com/
	 * Text Domain: mptbm_plugin
	 * Domain Path: /languages/
	 * WC requires at least: 3.0.9
	 * WC tested up to: 5.0
	 */
	if ( ! defined( 'ABSPATH' ) ) {
		die;
	} // Cannot access pages directly.
	if ( ! class_exists( 'MPTBM_Plugin' ) ) {
		class MPTBM_Plugin {
			public function __construct() {
				$this->load_plugin();
			}
			private function load_plugin(): void {
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
				if ( ! defined( 'MPTBM_PLUGIN_DIR' ) ) {
					define( 'MPTBM_PLUGIN_DIR', dirname( __FILE__ ) );
				}
				if ( ! defined( 'MPTBM_PLUGIN_URL' ) ) {
					define( 'MPTBM_PLUGIN_URL', plugins_url() . '/' . plugin_basename( dirname( __FILE__ ) ) );
				}
				if ( self::check_woocommerce()==1 ) {
					add_action( 'activated_plugin', array( $this, 'activation_redirect' ), 90, 1 );
					register_activation_hook( __FILE__, array( $this, 'on_activation_page_create' ) );
					require_once MPTBM_PLUGIN_DIR . '/inc/MPTBM_Dependencies.php';
				}
				else {
					require_once MPTBM_PLUGIN_DIR . '/Admin/MPTBM_Quick_Setup.php';
					add_action( 'activated_plugin', array( $this, 'activation_redirect_setup' ), 90, 1 );
				}
			}
			public function activation_redirect( $plugin ) {
				if ( $plugin == plugin_basename( __FILE__ ) ) {
					exit( wp_redirect( admin_url( 'edit.php?post_type=mptbm_rent&page=mptbm_quick_setup' ) ) );
				}
			}
			public function activation_redirect_setup( $plugin ) {
				if ( $plugin == plugin_basename( __FILE__ ) ) {
					exit( wp_redirect( admin_url( 'admin.php?post_type=mptbm_rent&page=mptbm_quick_setup' ) ) );
				}
			}
			public function on_activation_page_create(): void {
				if ( ! $this->get_page_by_slug( 'transport_booking' ) ) {
					$transport_booking = array(
						'post_type'    => 'page',
						'post_name'    => 'transport_booking',
						'post_title'   => 'Transport Booking',
						'post_content' => '[mptbm_booking]',
						'post_status'  => 'publish',
					);
					wp_insert_post( $transport_booking );
				}
			}
			public function get_page_by_slug( $slug ) {
				if ( $pages = get_pages() ) {
					foreach ( $pages as $page ) {
						if ( $slug === $page->post_name ) {
							return $page;
						}
					}
				}
				return false;
			}
			public static function check_woocommerce(): int {
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
				$plugin_dir = ABSPATH . 'wp-content/plugins/woocommerce';
				if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
					return 1;
				} elseif ( is_dir( $plugin_dir ) ) {
					return 2;
				} else {
					return 0;
				}
			}
		}
		new MPTBM_Plugin();
	}