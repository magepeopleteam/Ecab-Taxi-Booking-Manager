<?php
	/*
	* @Author 		magePeople
	* Copyright: 	mage-people.com
	*/
	if ( ! defined( 'ABSPATH' ) ) {
		die;
	} // Cannot access pages directly.
	if ( ! class_exists( 'MPTBM_Frontend' ) ) {
		class MPTBM_Frontend {
			public function __construct() {
				$this->load_file();
				//add_filter( 'single_template', array( $this, 'load_single_template' ) );
				add_action( 'wp_enqueue_scripts', array( $this, 'frontend_enqueue' ), 90 );
			}
			private function load_file(): void {
				require_once MPTBM_PLUGIN_DIR . '/Frontend/MPTBM_Shortcodes.php';
				require_once MPTBM_PLUGIN_DIR . '/Frontend/MPTBM_Transport_Search.php';
				require_once MPTBM_PLUGIN_DIR . '/Frontend/MPTBM_Woocommerce.php';
			}
			public function load_single_template( $template ): string {
				global $post;
				if ( $post->post_type && $post->post_type == 'transport_booking') {
					$template = MPTBM_Function::template_path( 'single_page/transport_booking.php' );
				}
				return $template;
			}
			public function frontend_enqueue() {
				wp_enqueue_script( 'jquery-ui-accordion' );
				wp_enqueue_style( 'mptbm_owl_style', MPTBM_PLUGIN_URL . '/assets/owl/owl.carousel.min.css' );
				wp_enqueue_style( 'mptbm_owl_style_default', MPTBM_PLUGIN_URL . '/assets/owl/owl.theme.default.min.css' );
				wp_enqueue_script( 'mptbm_owl', MPTBM_PLUGIN_URL . '/assets/owl/owl.carousel.min.js', array( 'jquery' ), '', true );
				wp_enqueue_style( 'mptbm_filter_pagination', MPTBM_PLUGIN_URL . '/assets/frontend/filter_pagination.css', array(), time() );
				wp_enqueue_script( 'mptbm_filter_pagination', MPTBM_PLUGIN_URL . '/assets/frontend/filter_pagination.js', array( 'jquery' ), time(), true );
				wp_enqueue_style( 'mptbm_style', MPTBM_PLUGIN_URL . '/assets/frontend/mptbm_style.css', array(), time() );
				wp_enqueue_script( 'mptbm_script', MPTBM_PLUGIN_URL . '/assets/frontend/mptbm_script.js', array( 'jquery' ), time(), true );
				wp_enqueue_script( 'mptbm_price_calculation', MPTBM_PLUGIN_URL . '/assets/frontend/mptbm_price_calculation.js', array( 'jquery' ), time(), true );
				do_action( 'mptbm_registration_enqueue' );
				do_action( 'mptbm_frontend_script' );
			}
		}
		new MPTBM_Frontend();
	}