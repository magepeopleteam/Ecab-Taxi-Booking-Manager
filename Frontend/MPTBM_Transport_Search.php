<?php
	/*
* @Author 		magePeople
* Copyright: 	mage-people.com
*/
	if ( ! defined( 'ABSPATH' ) ) {
		die;
	} // Cannot access pages directly.
	if ( ! class_exists( 'MPTBM_Transport_Search' ) ) {
		class MPTBM_Transport_Search {
			public function __construct() {
				add_action( 'mptbm_transport_search', [ $this, 'transport_search' ], 10, 1 );
				add_action( 'wp_ajax_get_mptbm_map_search_result', [ $this, 'get_mptbm_map_search_result' ] );
				add_action( 'wp_ajax_nopriv_get_mptbm_map_search_result', [ $this, 'get_mptbm_map_search_result' ] );
				add_action( 'wp_ajax_get_mptbm_end_place', [ $this, 'get_mptbm_end_place' ] );
				add_action( 'wp_ajax_nopriv_get_mptbm_end_place', [ $this, 'get_mptbm_end_place' ] );
			}
			public function transport_search( $params ) {
				$price_based = $params['price_based'] ?: 'distance';
				?>
				<div class="mpStyle ">
					<div class="mpRow mptbm_map_form_area dLayout_xs">
						<div class="col_6  mpForm">
							<input type="hidden" name="mptbm_price_based" value="<?php echo esc_attr( $price_based ); ?>"/>
							<label class="fdColumn">
								<input type="hidden" id="mptbm_map_start_date" value=""/>
								<span class="fas fa-calendar-alt"><?php esc_html_e( ' Select Date', 'mptbm_plugin' ); ?></span>
								<input type="text" class="formControl date_type" placeholder="<?php esc_html_e( ' Select Date', 'mptbm_plugin' ); ?>" value=""/>
							</label>
							<label class="fdColumn">
								<span class="far fa-clock"><?php esc_html_e( ' Select Time', 'mptbm_plugin' ); ?></span>
								<select id="mptbm_map_start_time" class="formControl">
									<option selected><?php esc_html_e( 'Please Select Time', 'mptbm_plugin' ); ?></option>
									<option value="9:00"><?php esc_html_e( '9.00 AM', 'mptbm_plugin' ); ?></option>
									<option value="9:15"><?php esc_html_e( '9.15 AM', 'mptbm_plugin' ); ?></option>
									<option value="9:30"><?php esc_html_e( '9.30 AM', 'mptbm_plugin' ); ?></option>
									<option value="9:45"><?php esc_html_e( '9.45 AM', 'mptbm_plugin' ); ?></option>
									<option value="10:00"><?php esc_html_e( '10.00 AM', 'mptbm_plugin' ); ?></option>
								</select>
							</label>
							<label class="fdColumn">
								<span class="fas fa-map-marker-alt"><?php esc_html_e( ' Start Location', 'mptbm_plugin' ); ?></span>
								<?php if ( $price_based == 'manual' ) { ?>
									<?php $all_start_locations = MPTBM_Function::get_manual_start_location(); ?>
									<select id="mptbm_manual_start_place" class="formControl mptbm_map_start_place">
										<option selected disabled><?php esc_html_e( ' Select start Location', 'mptbm_plugin' ); ?></option>
										<?php if ( sizeof( $all_start_locations ) > 0 ) {
											foreach ( $all_start_locations as $start_location ) {
												?>
												<option value="<?php echo esc_attr( $start_location ); ?>"><?php echo esc_html( $start_location ); ?></option>
												<?php
											}
										} ?>
									</select>
									<?php //echo '<pre>';print_r();echo '</pre>'; ?>
								<?php } else { ?>
									<input type="text" id="mptbm_map_start_place" class="formControl mptbm_map_start_place" placeholder="<?php esc_html_e( ' Enter start Location', 'mptbm_plugin' ); ?>" value=""/>
								<?php } ?>
							</label>
							<?php //echo '<pre>';print_r(MPTBM_Function::get_manual_end_location('DHAKA'));echo '</pre>'; ?>
							<label class="fdColumn mptbm_manual_end_place">
								<span class="fas fa-map-marker-alt"><?php esc_html_e( ' Destination Location', 'mptbm_plugin' ); ?></span>
								<?php if ( $price_based == 'manual' ) { ?>
									<select class="formControl mptbm_map_end_place">
										<option selected disabled><?php esc_html_e( ' Select Destination Location', 'mptbm_plugin' ); ?></option>
									</select>
									<?php //echo '<pre>';print_r();echo '</pre>'; ?>
								<?php } else { ?>
									<input type="text" id="mptbm_map_end_place" class="formControl mptbm_map_end_place" placeholder="<?php esc_html_e( ' Enter end Location', 'mptbm_plugin' ); ?>" value=""/>
								<?php } ?>
							</label>
							<div class="divider"></div>
							<button type="button" class="_themeButton_fullWidth" id="mptbm_get_vehicle"><?php esc_html_e( ' Search vehicle', 'mptbm_plugin' ); ?></button>
						</div>
						<div class="col_6 _pL">
							<div id="mptbm_map_area"></div>
						</div>
					</div>
					<div class="mptbm_map_search_result mT">
					</div>
				</div>
				<?php
			}
			public function get_mptbm_map_search_result() {
				$distance = $_COOKIE['mptbm_distance'] ?? '';
				$duration = $_COOKIE['mptbm_duration'] ?? '';
				if ( $distance && $duration ) {
					$start_place = MPTBM_Function::data_sanitize( $_POST['start_place'] );
					$end_place   = MPTBM_Function::data_sanitize( $_POST['end_place'] );
					$start_date  = MPTBM_Function::data_sanitize( $_POST['start_date'] );
					$start_time  = MPTBM_Function::data_sanitize( $_POST['start_time'] );
					$price_based = MPTBM_Function::data_sanitize( $_POST['price_based'] );
					$date        = $start_date . ' ' . $start_time;
					$all_posts   = MPTBM_Query::query_transport_list( $price_based );
					if ( $all_posts->found_posts > 0 ) {
						$posts = $all_posts->posts;
						?>
						<div class="all_filter_item">
							<div class="flexWrap modern">
								<?php
									foreach ( $posts as $post ) {
										$post_id    = $post->ID;
										$location_exit=MPTBM_Function::location_exit($post_id,$start_place,$end_place);
										if($location_exit){
										$product_id = MPTBM_Function::get_post_info( $post_id, 'link_wc_product' );
										$thumbnail  = MPTBM_Function::get_image_url( $post_id );
										$price      = MPTBM_Function::get_price( $post_id, $distance, $duration ,$start_place,$end_place);
										$wc_price   = MPTBM_Function::wc_price( $post_id, $price );
										$raw_price  = MPTBM_Function::price_convert_raw( $wc_price );
										?>
										<div class="filter_item mptbm_booking_item" data-placeholder>
											<div class="bg_image_area" data-href="<?php echo get_the_permalink( $post_id ); ?>" data-placeholder>
												<div data-bg-image="<?php echo esc_attr( $thumbnail ); ?>"></div>
											</div>
											<div class="fdColumn ttbm_list_details">
												<h5 data-href="<?php echo get_the_permalink( $post_id ); ?>"><?php echo get_the_title( $post_id ); ?></h5>
												<div class="divider"></div>
												<p><span class="fas fa-map-marker-alt"></span>&nbsp;&nbsp;<strong><?php esc_html_e( 'Start Location', 'mptbm_plugin' ); ?> : </strong><?php echo esc_html( $start_place ); ?></p>
												<p><span class="fas fa-map-marker-alt"></span>&nbsp;&nbsp;<strong><?php esc_html_e( 'Destination Location', 'mptbm_plugin' ); ?> : </strong> <?php echo esc_html( $end_place ); ?></p>
												<h2 class="textTheme" data-main-price="<?php echo esc_attr( $raw_price ); ?>"> <?php echo MPTBM_Function::esc_html( $wc_price ); ?></h2>
												<div class="dLayout_xs bgLight" data-collapse="#mptbm_collape_show_info_<?php echo esc_attr( $post_id ); ?>">
													<ul class="list_inline_two">
														<li class="justifyBetween"><h6><?php esc_html_e( 'Engine', 'mptbm_plugin' ); ?> : </h6> <?php echo MPTBM_Function::get_post_info( $post_id, 'mptbm_engine' ); ?></li>
														<li class="justifyBetween"><h6><?php esc_html_e( 'Length', 'mptbm_plugin' ); ?> : </h6><?php echo MPTBM_Function::get_post_info( $post_id, 'mptbm_length' ); ?></li>
														<li class="justifyBetween"><h6><?php esc_html_e( 'Interior Color', 'mptbm_plugin' ); ?> : </h6><?php echo MPTBM_Function::get_post_info( $post_id, 'mptbm_interior_color' ); ?></li>
														<li class="justifyBetween"><h6><?php esc_html_e( 'Exterior Color', 'mptbm_plugin' ); ?> : </h6><?php echo MPTBM_Function::get_post_info( $post_id, 'mptbm_exterior_color' ); ?></li>
														<li class="justifyBetween"><h6><?php esc_html_e( 'Power', 'mptbm_plugin' ); ?> : </h6><?php echo MPTBM_Function::get_post_info( $post_id, 'mptbm_power' ); ?></li>
														<li class="justifyBetween"><h6><?php esc_html_e( 'Transmission', 'mptbm_plugin' ); ?> : </h6><?php echo MPTBM_Function::get_post_info( $post_id, 'mptbm_transmission' ); ?></li>
														<li class="justifyBetween"><h6><?php esc_html_e( 'Fuel Type', 'mptbm_plugin' ); ?> : </h6><?php echo MPTBM_Function::get_post_info( $post_id, 'mptbm_fuel_type' ); ?></li>
														<li class="justifyBetween"><h6><?php esc_html_e( 'Extras', 'mptbm_plugin' ); ?> : </h6><?php echo MPTBM_Function::get_post_info( $post_id, 'mptbm_extras' ); ?></li>
													</ul>
												</div>
												<div class="divider"></div>
												<form method="post" action="">
													<input type="hidden" name="mptbm_post_id" value="<?php echo esc_attr( $post_id ); ?>"/>
													<input type="hidden" name="mptbm_start_place" value="<?php echo esc_attr( $start_place ); ?>"/>
													<input type="hidden" name="mptbm_end_place" value="<?php echo esc_attr( $end_place ); ?>"/>
													<input type="hidden" name="mptbm_date" value="<?php echo esc_attr( $date ); ?>"/>
													<div class="justifyBetween">
														<button type="button"
															  class="_themeButton_xs w_150"
															  data-collapse-target="#mptbm_collape_show_info_<?php echo esc_attr( $post_id ); ?>"
															  data-open-text="<?php esc_html_e( 'Show Info', 'mptbm_plugin' ); ?>"
															  data-close-text="<?php esc_html_e( 'Less Info', 'mptbm_plugin' ); ?>"
															  data-open-icon="fa-angle-down"
															  data-close-icon="fa-angle-up"
														>
															<span class="fas fa-angle-down" data-icon></span>&nbsp;&nbsp;
															<span data-text><?php esc_html_e( 'Show Info', 'mptbm_plugin' ); ?></span>
														</button>
														<button type="button" class="dButton_xs w_150" data-collapse-target="#mptbm_collape_select_<?php echo esc_attr( $post_id ); ?>"><span><?php esc_html_e( 'Select', 'mptbm_plugin' ); ?></span></button>
													</div>
													<div data-collapse="#mptbm_collape_select_<?php echo esc_attr( $post_id ); ?>">
														<?php $extra_services = MPTBM_Function::get_post_info( $post_id, 'mptbm_extra_service_data', array() ); ?>
														<div class="mptbm_extra_service_area" data-placeholder>
															<table class="noShadow">
																<tbody>
																<?php foreach ( $extra_services as $service ) { ?>
																	<?php
																	$service_icon      = array_key_exists( 'service_icon', $service ) ? $service['service_icon'] : '';
																	$service_name      = array_key_exists( 'service_name', $service ) ? $service['service_name'] : '';
																	$service_price     = array_key_exists( 'service_price', $service ) ? $service['service_price'] : 0;
																	$service_price     = MPTBM_Function::wc_price( $post_id, $service_price );
																	$service_price_raw = MPTBM_Function::price_convert_raw( $service_price );
																	$description       = array_key_exists( 'extra_service_description', $service ) ? $service['extra_service_description'] : '';
																	?>
																	<tr>
																		<th>
																			<h4>
																				<?php if ( $service_icon ) { ?>
																					<span class="<?php echo esc_attr( $service_icon ); ?>"></span>
																				<?php } ?>
																				<?php echo MPTBM_Function::esc_html( $service_name ); ?>
																			</h4>
																			<?php
																				if ( $description ) {
																					$word_count = str_word_count( $description );
																					if ( $word_count > 16 ) {
																						$message      = implode( " ", array_slice( explode( " ", $description ), 0, 16 ) );
																						$more_message = implode( " ", array_slice( explode( " ", $description ), 16, $word_count ) );
																						$name_text    = preg_replace( "/[{}()<>+ ]/", '_', $service_name ) . '_' . $post_id;
																						?>
																						<p style="margin-top: 5px;">
																							<small>
																								<?php echo esc_html( $message ); ?>
																								<span data-collapse='#<?php echo esc_attr( $name_text ); ?>'><?php echo esc_html( $more_message ); ?></span>
																								<span class="load_more_text" data-collapse-target="#<?php echo esc_attr( $name_text ); ?>">
															<?php esc_html_e( 'view more ', 'mptbm_plugin' ); ?>
														</span>
																							</small>
																						</p>
																						<?php
																					} else {
																						?>
																						<p style="margin-top: 5px;"><small><?php echo esc_html( $description ); ?></small></p>
																						<?php
																					}
																				}
																			?>
																		</th>
																		<td class="textCenter"><?php echo MPTBM_Function::esc_html( $service_price ); ?></td>
																		<td>
																			<label class="_allCenter_fRight selectCheckbox">
																				<input type="hidden" name="mptbm_extra_service[]" value=""/>
																				<input type="checkbox" data-extra-service-price="<?php echo esc_attr( $service_price_raw ); ?>" value="<?php echo MPTBM_Function::esc_html( $service_name ); ?>"/>
																				<span class="customCheckbox"><?php esc_html_e( 'Select', 'mptbm_plugin' ); ?></span>
																			</label>
																		</td>
																	</tr>
																<?php } ?>
																</tbody>
															</table>
														</div>
														<button class="_dButton_fRight mptbm_book_now" type="button">
															<span class="fas fa-cart-plus"></span>
															<?php esc_html_e( 'Add to Cart', 'mptbm_plugin' ); ?>
														</button>
														<button type="submit" name="add-to-cart" value="<?php echo esc_html( $product_id ); ?>" class="dNone mptbm_add_to_cart">
															<?php esc_html_e( 'Add to Cart', 'mptbm_plugin' ); ?>
														</button>
													</div>
												</form>
											</div>
										</div>
										<?php
										}
									}
								?>
							</div>
						</div>
						<?php
					}
					//setcookie( "mptbm_distance", "", time() + 60, "/" );
					//setcookie( "mptbm_duration", "", time() + 60, "/" );
				}
				die();
			}
			public function get_mptbm_end_place() {
				$start_place   = MPTBM_Function::data_sanitize( $_POST['start_place'] );
				$price_based   = MPTBM_Function::data_sanitize( $_POST['price_based'] );
				$end_locations = MPTBM_Function::get_manual_end_location( $start_place );
				if ( sizeof( $end_locations ) > 0 ) {
					?>
					<span class="fas fa-map-marker-alt"><?php esc_html_e( ' Destination Location', 'mptbm_plugin' ); ?></span>
					<select class="formControl mptbm_map_end_place" id="mptbm_manual_end_place">
						<option selected disabled><?php esc_html_e( ' Select Destination Location', 'mptbm_plugin' ); ?></option>
						<?php
							foreach ( $end_locations as $location ) {
								?>
								<option value="<?php echo esc_attr( $location ); ?>"><?php echo esc_html( $location ); ?></option>
								<?php
							}
						?>
					</select>
					<?php
				} else {
					?><span class="fas fa-map-marker-alt"><?php esc_html_e( ' Can not find any Destination Location', 'mptbm_plugin' ); ?></span><?php
				}
				die();
			}
		}
		new MPTBM_Transport_Search();
	}