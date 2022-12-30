<?php
	if ( ! defined( 'ABSPATH' ) ) {
		die;
	} // Cannot access pages directly.
	if ( ! class_exists( 'MPTBM_General_Settings' ) ) {
		class MPTBM_General_Settings {
			public function __construct() {
				add_action( 'add_mptbm_settings_tab_content', [ $this, 'general_settings' ], 10, 1 );
				add_action( 'mptbm_settings_save', [ $this, 'save_general_settings' ], 10, 1 );
			}
			public function general_settings( $post_id ) {
				$name           = MPTBM_Function::get_post_info( $post_id, 'mptbm_name' );
				$model          = MPTBM_Function::get_post_info( $post_id, 'mptbm_model' );
				$engine         = MPTBM_Function::get_post_info( $post_id, 'mptbm_engine' );
				$interior_color = MPTBM_Function::get_post_info( $post_id, 'mptbm_interior_color' );
				$power          = MPTBM_Function::get_post_info( $post_id, 'mptbm_power' );
				$fuel_type      = MPTBM_Function::get_post_info( $post_id, 'mptbm_fuel_type' );
				$length         = MPTBM_Function::get_post_info( $post_id, 'mptbm_length' );
				$exterior_color = MPTBM_Function::get_post_info( $post_id, 'mptbm_exterior_color' );
				$transmission   = MPTBM_Function::get_post_info( $post_id, 'mptbm_transmission' );
				$extras         = MPTBM_Function::get_post_info( $post_id, 'mptbm_extras' );
				?>
				<div class="tabsItem" data-tabs="#mptbm_general_info">
					<h5><?php esc_html_e( 'General Information Settings', 'mptbm_plugin' ); ?></h5>
					<div class="divider"></div>
					<table>
						<tbody>
						<tr>
							<th><?php esc_html_e( 'Name', 'mptbm_plugin' ); ?></th>
							<th colspan="2">
								<label>
									<input class="formControl" type="text" name="mptbm_name" value="<?php echo esc_attr( $name ); ?>" placeholder="<?php esc_html_e( 'Name', 'mptbm_plugin' ); ?>"/>
								</label>
							</th>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Model', 'mptbm_plugin' ); ?></th>
							<th colspan="2">
								<label>
									<input class="formControl" type="text" name="mptbm_model" value="<?php echo esc_attr( $model ); ?>" placeholder="<?php esc_html_e( 'Model', 'mptbm_plugin' ); ?>"/>
								</label>
							</th>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Engine', 'mptbm_plugin' ); ?></th>
							<th colspan="2">
								<label>
									<input class="formControl" type="text" name="mptbm_engine" value="<?php echo esc_attr( $engine ); ?>" placeholder="<?php esc_html_e( 'Engine', 'mptbm_plugin' ); ?>"/>
								</label>
							</th>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Interior Color', 'mptbm_plugin' ); ?></th>
							<th colspan="2">
								<label>
									<input class="formControl" type="text" name="mptbm_interior_color" value="<?php echo esc_attr( $interior_color ); ?>" placeholder="<?php esc_html_e( 'Interior Color', 'mptbm_plugin' ); ?>"/>
								</label>
							</th>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Power', 'mptbm_plugin' ); ?></th>
							<th colspan="2">
								<label>
									<input class="formControl" type="text" name="mptbm_power" value="<?php echo esc_attr( $power ); ?>" placeholder="<?php esc_html_e( 'Power', 'mptbm_plugin' ); ?>"/>
								</label>
							</th>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Fuel Type', 'mptbm_plugin' ); ?></th>
							<th colspan="2">
								<label>
									<input class="formControl" type="text" name="mptbm_fuel_type" value="<?php echo esc_attr( $fuel_type ); ?>" placeholder="<?php esc_html_e( 'Fuel Type', 'mptbm_plugin' ); ?>"/>
								</label>
							</th>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Length', 'mptbm_plugin' ); ?></th>
							<th colspan="2">
								<label>
									<input class="formControl" type="text" name="mptbm_length" value="<?php echo esc_attr( $length ); ?>" placeholder="<?php esc_html_e( 'Length', 'mptbm_plugin' ); ?>"/>
								</label>
							</th>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Exterior Color', 'mptbm_plugin' ); ?></th>
							<th colspan="2">
								<label>
									<input class="formControl" type="text" name="mptbm_exterior_color" value="<?php echo esc_attr( $exterior_color ); ?>" placeholder="<?php esc_html_e( 'Exterior Color', 'mptbm_plugin' ); ?>"/>
								</label>
							</th>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Transmission', 'mptbm_plugin' ); ?></th>
							<th colspan="2">
								<label>
									<input class="formControl" type="text" name="mptbm_transmission" value="<?php echo esc_attr( $transmission ); ?>" placeholder="<?php esc_html_e( 'Transmission', 'mptbm_plugin' ); ?>"/>
								</label>
							</th>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Extras', 'mptbm_plugin' ); ?></th>
							<th colspan="2">
								<label>
									<input class="formControl" type="text" name="mptbm_extras" value="<?php echo esc_attr( $extras ); ?>" placeholder="<?php esc_html_e( 'Extras', 'mptbm_plugin' ); ?>"/>
								</label>
							</th>
						</tr>
						</tbody>
					</table>
				</div>
				<?php
			}
			public function save_general_settings($post_id){
				if ( get_post_type( $post_id ) == MPTBM_Function::get_cpt_name() ) {
					$name           = MPTBM_Function::get_submit_info( 'mptbm_name' );
					update_post_meta( $post_id, 'mptbm_name', $name );
					$model          = MPTBM_Function::get_submit_info( 'mptbm_model' );
					update_post_meta( $post_id, 'mptbm_model', $model );
					$engine         = MPTBM_Function::get_submit_info( 'mptbm_engine' );
					update_post_meta( $post_id, 'mptbm_engine', $engine );
					$interior_color = MPTBM_Function::get_submit_info( 'mptbm_interior_color' );
					update_post_meta( $post_id, 'mptbm_interior_color', $interior_color );
					$power          = MPTBM_Function::get_submit_info( 'mptbm_power' );
					update_post_meta( $post_id, 'mptbm_power', $power );
					$fuel_type      = MPTBM_Function::get_submit_info( 'mptbm_fuel_type' );
					update_post_meta( $post_id, 'mptbm_fuel_type', $fuel_type );
					$length         = MPTBM_Function::get_submit_info( 'mptbm_length' );
					update_post_meta( $post_id, 'mptbm_length', $length );
					$exterior_color = MPTBM_Function::get_submit_info( 'mptbm_exterior_color' );
					update_post_meta( $post_id, 'mptbm_exterior_color', $exterior_color );
					$transmission   = MPTBM_Function::get_submit_info( 'mptbm_transmission' );
					update_post_meta( $post_id, 'mptbm_transmission', $transmission );
					$extras         = MPTBM_Function::get_submit_info( 'mptbm_extras' );
					update_post_meta( $post_id, 'mptbm_extras', $extras );
				}
			}
		}
		new MPTBM_General_Settings();
	}