<?php
	if ( ! defined( 'ABSPATH' ) ) {
		die;
	} // Cannot access pages directly.
	if ( ! class_exists( 'MPTBM_Details_Layout' ) ) {
		class MPTBM_Details_Layout {
			public function __construct() {
				add_action( 'mptbm_details_title', array( $this, 'details_title' ) );
			}
			public function details_title() {
				include( MPTBM_Function::template_path( 'layout/title_details_page.php' ) );
			}
		}
		new MPTBM_Details_Layout();
	}