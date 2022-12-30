<?php
	/*
	* @Author 		magePeople
	* Copyright: 	mage-people.com
	*/
	if ( ! defined( 'ABSPATH' ) ) {
		die;
	} // Cannot access pages directly.
	if ( ! class_exists( 'MPTBM_Shortcodes' ) ) {
		class MPTBM_Shortcodes {
			public function __construct() {
				add_shortcode( 'mptbm_booking', array( $this, 'mptbm_booking' ) );
			}
			public function mptbm_booking() {
				ob_start();
				do_action( 'mptbm_transport_search' );
				return ob_get_clean();
			}
		}
		new MPTBM_Shortcodes();
	}