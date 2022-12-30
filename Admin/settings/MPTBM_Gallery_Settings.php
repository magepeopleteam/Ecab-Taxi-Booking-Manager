<?php
	if ( ! defined( 'ABSPATH' ) ) {
		die;
	} // Cannot access pages directly.
	if ( ! class_exists( 'MPTBM_Gallery_Settings' ) ) {
		class MPTBM_Gallery_Settings {
			public function __construct() {
				add_action( 'add_mptbm_settings_tab_content', [ $this, 'gallery_settings' ] );
				add_action( 'mptbm_settings_save', [ $this, 'save_gallery_settings' ], 10, 1 );
			}
			public function gallery_settings( $post_id ) {

				$display   = MPTBM_Function::get_post_info( $post_id, 'mptbm_display_slider', 'on' );
				$active    = $display == 'off' ? '' : 'mActive';
				$checked   = $display == 'off' ? '' : 'checked';
				$image_ids = MPTBM_Function::get_post_info( $post_id, 'mptbm_slider_images', array() );
				?>
				<div class="tabsItem" data-tabs="#mptbm_settings_gallery">
					<h5 class="dFlex">
						<span class="mR"><?php esc_html_e( 'On/Off Slider', 'mptbm_plugin' ); ?></span>
						<?php MPTBM_Layout::switch_button( 'mptbm_display_slider', $checked ); ?>
					</h5>
					<?php MPTBM_Settings::info_text( 'mptbm_display_slider' ); ?>
					<div class="divider"></div>
					<div data-collapse="#mptbm_display_slider" class="<?php echo esc_attr( $active ); ?>">
						<table>
							<tbody>
							<tr>
								<th><?php esc_html_e( 'Gallery Images ', 'mptbm_plugin' ); ?></th>
								<td colspan="3">
									<?php MPTBM_Layout::add_multi_image( 'mptbm_slider_images', $image_ids ); ?>
								</td>
							</tr>
							<tr>
								<td colspan="4"><?php MPTBM_Settings::info_text( 'mptbm_slider_images' ); ?></td>
							</tr>
							</tbody>
						</table>
					</div>
				</div>
				<?php
			}
			public function save_gallery_settings( $post_id ) {
				if ( get_post_type( $post_id ) == MPTBM_Function::get_cpt_name() ) {
					$slider = MPTBM_Function::get_submit_info( 'mptbm_display_slider' ) ? 'on' : 'off';
					update_post_meta( $post_id, 'mptbm_display_slider', $slider );
					$images     = MPTBM_Function::get_submit_info( 'mptbm_slider_images');
					$all_images = explode( ',', $images );
					update_post_meta( $post_id, 'mptbm_slider_images', $all_images );
				}
			}
		}
		new MPTBM_Gallery_Settings();
	}