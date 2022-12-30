<?php
	if ( ! defined( 'ABSPATH' ) ) {
		die;
	} // Cannot access pages directly.
	if ( ! class_exists( 'MPTBM_Admin' ) ) {
		class MPTBM_Admin {
			public function __construct() {
				$this->load_file();
				add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ), 90 );
			}
			private function load_file(): void {
				require_once MPTBM_PLUGIN_DIR . '/Admin/MAGE_Setting_API.php';
				require_once MPTBM_PLUGIN_DIR . '/Admin/MPTBM_Settings_Global.php';
				require_once MPTBM_PLUGIN_DIR . '/Admin/MPTBM_Hidden_Product.php';
				require_once MPTBM_PLUGIN_DIR . '/Admin/MPTBM_CPT.php';
				require_once MPTBM_PLUGIN_DIR . '/Admin/MPTBM_Quick_Setup.php';
				require_once MPTBM_PLUGIN_DIR . '/Admin/MPTBM_Save.php';
				require_once MPTBM_PLUGIN_DIR . '/Admin/MPTBM_Settings.php';
				require_once MPTBM_PLUGIN_DIR . '/Admin/settings/MPTBM_General_Settings.php';
				require_once MPTBM_PLUGIN_DIR . '/Admin/settings/MPTBM_Price_Settings.php';
				require_once MPTBM_PLUGIN_DIR . '/Admin/settings/MPTBM_Extra_Service.php';
				require_once MPTBM_PLUGIN_DIR . '/Admin/settings/MPTBM_Gallery_Settings.php';
			}
			public function admin_enqueue() {
				wp_enqueue_editor();
				//admin script
				wp_enqueue_script( 'jquery-ui-sortable' );
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_script( 'wp-color-picker' );
				wp_enqueue_script( 'select2.min', MPTBM_PLUGIN_URL . '/assets/helper/js/select2.min.js', array( 'jquery' ) );
				wp_enqueue_style( 'select2.min', MPTBM_PLUGIN_URL . '/assets/helper/css/select2.min.css' );
				wp_enqueue_script( 'codemirror', MPTBM_PLUGIN_URL . '/assets/helper/js/codemirror.min.js', array( 'jquery' ), null );
				wp_enqueue_style( 'codemirror', MPTBM_PLUGIN_URL . '/assets/helper/css/codemirror.css' );
				// custom
				wp_enqueue_script( 'mp_admin_settings', MPTBM_PLUGIN_URL . '/assets/admin/mp_admin_settings.js', array( 'jquery' ), time(), true );
				wp_enqueue_style( 'mp_admin_settings', MPTBM_PLUGIN_URL . '/assets/admin/mp_admin_settings.css', array(), time() );
				wp_enqueue_script( 'mptbm_admin', MPTBM_PLUGIN_URL . '/assets/admin/mptbm_admin.js', array( 'jquery' ), time(), true );
				wp_enqueue_style( 'mptbm_admin', MPTBM_PLUGIN_URL . '/assets/admin/mptbm_admin.css', array(), time() );
				do_action( 'ttbm_admin_script' );
			}
		}
		new MPTBM_Admin();
	}