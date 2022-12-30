<?php
	if ( ! defined( 'ABSPATH' ) ) {
		die;
	} // Cannot access pages directly.
	if ( ! class_exists( 'MPTBM_Save' ) ) {
		class MPTBM_Save {
			public function __construct() {
				add_action( 'save_post', array( $this, 'save_settings' ), 99, 1 );
			}
			public function save_settings($post_id){
				if ( ! isset( $_POST['mptbm_transportation_type_nonce'] ) || ! wp_verify_nonce( $_POST['mptbm_transportation_type_nonce'], 'mptbm_transportation_type_nonce' ) && defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE && ! current_user_can( 'edit_post', $post_id ) ) {
					return;
				}
				do_action( 'mptbm_settings_save', $post_id );
			}
		}
		new MPTBM_Save();
	}