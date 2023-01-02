<?php
	if ( ! defined( 'ABSPATH' ) ) {
		die;
	} // Cannot access pages directly.
	if ( ! class_exists( 'MPTBM_Function' ) ) {
		class MPTBM_Function {
			public function __construct() {
				add_filter( 'use_block_editor_for_post_type', [ $this, 'disable_gutenberg' ], 10, 2 );
			}
			//************************************//
			public function disable_gutenberg( $current_status, $post_type ) {
				$user_status = self::get_general_settings( 'disable_block_editor', 'yes' );
				if ( $post_type === self::get_cpt_name() && $user_status == 'yes' ) {
					return false;
				}
				return $current_status;
			}
			//************************************//
			public static function get_post_info( $tour_id, $key, $default = '' ) {
				$data = get_post_meta( $tour_id, $key, true ) ?: $default;
				return self::data_sanitize( $data );
			}
			public static function data_sanitize( $data ) {
				$data = maybe_unserialize( $data );
				if ( is_string( $data ) ) {
					$data = maybe_unserialize( $data );
					if ( is_array( $data ) ) {
						$data = self::data_sanitize( $data );
					} else {
						$data = sanitize_text_field( $data );
					}
				} elseif ( is_array( $data ) ) {
					foreach ( $data as &$value ) {
						if ( is_array( $value ) ) {
							$value = self::data_sanitize( $value );
						} else {
							$value = sanitize_text_field( $value );
						}
					}
				}
				return $data;
			}
			public static function submit_sanitize( $key, $default = '' ) {
				$data = $_POST[ $key ] ?? $default;
				$data = stripslashes( strip_tags( $data ) );
				return self::data_sanitize( $data );
			}
			public static function get_submit_info( $key, $default = '' ) {
				$data = $_POST[ $key ] ?? $default;
				return self::data_sanitize( $data );
			}
			//***********Template********************//
			public static function all_details_template() {
				$template_path = get_stylesheet_directory() . '/ttbm_templates/themes/';
				$default_path  = TTBM_PLUGIN_DIR . '/templates/themes/';
				$dir           = is_dir( $template_path ) ? glob( $template_path . "*" ) : glob( $default_path . "*" );
				$names         = array();
				foreach ( $dir as $filename ) {
					if ( is_file( $filename ) ) {
						$file           = basename( $filename );
						$name           = str_replace( "?>", "", strip_tags( file_get_contents( $filename, false, null, 24, 16 ) ) );
						$names[ $file ] = $name;
					}
				}
				$name = [];
				foreach ( $names as $key => $value ) {
					$name[ $key ] = $value;
				}
				return apply_filters( 'ttbm_template_list_arr', $name );
			}
			public static function details_template_path(): string {
				$tour_id       = get_the_id();
				$template_name = self::get_post_info( $tour_id, 'ttbm_theme_file', 'default.php' );
				$file_name     = 'themes/' . $template_name;
				$dir           = TTBM_PLUGIN_DIR . '/templates/' . $file_name;
				if ( ! file_exists( $dir ) ) {
					$file_name = 'themes/default.php';
				}
				return self::template_path( $file_name );
			}
			public static function template_path( $file_name ): string {
				$template_path = get_stylesheet_directory() . '/ttbm_templates/';
				$default_dir   = TTBM_PLUGIN_DIR . '/templates/';
				$dir           = is_dir( $template_path ) ? $template_path : $default_dir;
				$file_path     = $dir . $file_name;
				return locate_template( array( 'ttbm_templates/' . $file_name ) ) ? $file_path : $default_dir . $file_name;
			}
			//*********Date and Time**********************//
			public static function datetime_format( $date, $type = 'date-time-text' ) {
				$date_format = get_option( 'date_format' );
				$time_format = get_option( 'time_format' );
				$wp_settings = $date_format . '  ' . $time_format;
				$timezone    = wp_timezone_string();
				$timestamp   = strtotime( $date . ' ' . $timezone );
				if ( $type == 'date-time' ) {
					$date = wp_date( $wp_settings, $timestamp );
				} elseif ( $type == 'date-text' ) {
					$date = wp_date( $date_format, $timestamp );
				} elseif ( $type == 'date' ) {
					$date = wp_date( $date_format, $timestamp );
				} elseif ( $type == 'time' ) {
					$date = wp_date( $time_format, $timestamp, wp_timezone() );
				} elseif ( $type == 'day' ) {
					$date = wp_date( 'd', $timestamp );
				} elseif ( $type == 'month' ) {
					$date = wp_date( 'M', $timestamp );
				} elseif ( $type == 'date-time-text' ) {
					$date = wp_date( $wp_settings, $timestamp, wp_timezone() );
				} else {
					$date = wp_date( $type, $timestamp );
				}
				return $date;
			}
			public static function date_format(): string {
				$format      = self::get_general_settings( 'date_format', 'D d M , yy' );
				$date_format = 'Y-m-d';
				$date_format = $format == 'yy/mm/dd' ? 'Y/m/d' : $date_format;
				$date_format = $format == 'yy-dd-mm' ? 'Y-d-m' : $date_format;
				$date_format = $format == 'yy/dd/mm' ? 'Y/d/m' : $date_format;
				$date_format = $format == 'dd-mm-yy' ? 'd-m-Y' : $date_format;
				$date_format = $format == 'dd/mm/yy' ? 'd/m/Y' : $date_format;
				$date_format = $format == 'mm-dd-yy' ? 'm-d-Y' : $date_format;
				$date_format = $format == 'mm/dd/yy' ? 'm/d/Y' : $date_format;
				$date_format = $format == 'd M , yy' ? 'j M , Y' : $date_format;
				$date_format = $format == 'D d M , yy' ? 'D j M , Y' : $date_format;
				$date_format = $format == 'M d , yy' ? 'M  j, Y' : $date_format;
				return $format == 'D M d , yy' ? 'D M  j, Y' : $date_format;
			}
			//*************Price*********************************//
			public static function get_price( $post_id, $distance = 1000, $duration = 3600 ): string {
				$price_based = MPTBM_Function::get_post_info( $post_id, 'mptbm_price_based' );
				if ( $price_based == 'distance' ) {
					$price = MPTBM_Function::get_post_info( $post_id, 'mptbm_km_price' ) * $distance / 1000;
				} elseif ( $price_based == 'duration' ) {
					$price = MPTBM_Function::get_post_info( $post_id, 'mptbm_hour_price' ) * $duration / 3600;
				} else {
					$price = '';
				}
				return $price;
			}
			public static function get_wc_price( $post_id ): string {
				$price = self::get_price( $post_id );
				return self::wc_price( $post_id, $price );
			}
			public static function get_extra_service_price_by_name( $post_id, $service_name ) {
				$extra_services = self::get_post_info( $post_id, 'mptbm_extra_service_data', array() );
				$price          = '';
				if ( sizeof( $extra_services ) > 0 ) {
					foreach ( $extra_services as $service ) {
						if ( $service['service_name'] == $service_name ) {
							return $service['service_price'];
						}
					}
				}
				return $price;
			}
			public static function price_convert_raw( $price ) {
				$price = wp_strip_all_tags( $price );
				$price = str_replace( get_woocommerce_currency_symbol(), '', $price );
				$price = str_replace( wc_get_price_thousand_separator(), '', $price );
				$price = str_replace( wc_get_price_decimal_separator(), '.', $price );
				return max( $price, 0 );
			}
			public static function wc_price( $post_id, $price, $args = array() ): string {
				$num_of_decimal = get_option( 'woocommerce_price_num_decimals', 2 );
				$args           = wp_parse_args( $args, array(
					'qty'   => '',
					'price' => '',
				) );
				$_product       = self::get_post_info( $post_id, 'link_wc_product', $post_id );
				$product        = wc_get_product( $_product );
				$qty            = '' !== $args['qty'] ? max( 0.0, (float) $args['qty'] ) : 1;
				$tax_with_price = get_option( 'woocommerce_tax_display_shop' );
				if ( '' === $price ) {
					return '';
				} elseif ( empty( $qty ) ) {
					return 0.0;
				}
				$line_price   = (float) $price * (int) $qty;
				$return_price = $line_price;
				if ( $product->is_taxable() ) {
					if ( ! wc_prices_include_tax() ) {
						$tax_rates = WC_Tax::get_rates( $product->get_tax_class() );
						$taxes     = WC_Tax::calc_tax( $line_price, $tax_rates );
						if ( 'yes' === get_option( 'woocommerce_tax_round_at_subtotal' ) ) {
							$taxes_total = array_sum( $taxes );
						} else {
							$taxes_total = array_sum( array_map( 'wc_round_tax_total', $taxes ) );
						}
						$return_price = $tax_with_price == 'excl' ? round( $line_price, $num_of_decimal ) : round( $line_price + $taxes_total, $num_of_decimal );
					} else {
						$tax_rates      = WC_Tax::get_rates( $product->get_tax_class() );
						$base_tax_rates = WC_Tax::get_base_tax_rates( $product->get_tax_class( 'unfiltered' ) );
						if ( ! empty( WC()->customer ) && WC()->customer->get_is_vat_exempt() ) { // @codingStandardsIgnoreLine.
							$remove_taxes = apply_filters( 'woocommerce_adjust_non_base_location_prices', true ) ? WC_Tax::calc_tax( $line_price, $base_tax_rates, true ) : WC_Tax::calc_tax( $line_price, $tax_rates, true );
							if ( 'yes' === get_option( 'woocommerce_tax_round_at_subtotal' ) ) {
								$remove_taxes_total = array_sum( $remove_taxes );
							} else {
								$remove_taxes_total = array_sum( array_map( 'wc_round_tax_total', $remove_taxes ) );
							}
							// $return_price = round( $line_price, $num_of_decimal);
							$return_price = round( $line_price - $remove_taxes_total, $num_of_decimal );
						} else {
							$base_taxes   = WC_Tax::calc_tax( $line_price, $base_tax_rates, true );
							$modded_taxes = WC_Tax::calc_tax( $line_price - array_sum( $base_taxes ), $tax_rates );
							if ( 'yes' === get_option( 'woocommerce_tax_round_at_subtotal' ) ) {
								$base_taxes_total   = array_sum( $base_taxes );
								$modded_taxes_total = array_sum( $modded_taxes );
							} else {
								$base_taxes_total   = array_sum( array_map( 'wc_round_tax_total', $base_taxes ) );
								$modded_taxes_total = array_sum( array_map( 'wc_round_tax_total', $modded_taxes ) );
							}
							$return_price = $tax_with_price == 'excl' ? round( $line_price - $base_taxes_total, $num_of_decimal ) : round( $line_price - $base_taxes_total + $modded_taxes_total, $num_of_decimal );
						}
					}
				}
				$return_price   = apply_filters( 'woocommerce_get_price_including_tax', $return_price, $qty, $product );
				$display_suffix = get_option( 'woocommerce_price_display_suffix' ) ? get_option( 'woocommerce_price_display_suffix' ) : '';
				return wc_price( $return_price ) . ' ' . $display_suffix;
			}

			//*******************************//
			public static function get_image_url( $post_id = '', $image_id = '', $size = 'full' ) {
				if ( $post_id ) {
					$image_id = self::get_post_info( $post_id, 'mptbm_list_thumbnail' );
					$image_id = $image_id ?: get_post_thumbnail_id( $post_id );
				}
				return wp_get_attachment_image_url( $image_id, $size );
			}
			//*******************************//
			public static function get_manual_start_location(){
				$all_location=[];
				$all_posts   = MPTBM_Query::query_transport_list( 'manual' );
				if ( $all_posts->found_posts > 0 ) {
					$posts = $all_posts->posts;
					foreach ( $posts as $post ) {
						$post_id    = $post->ID;
						$manual_prices  = MPTBM_Function::get_post_info( $post_id, 'mptbm_manual_price_info', [] );
						if(sizeof($manual_prices)>0){
							foreach ( $manual_prices as $manual_price ) {
								$start_location = array_key_exists( 'start_location', $manual_price ) ? $manual_price['start_location'] : '';
								if($start_location){
									$all_location[]=	$start_location;
								}
							}
						}
					}
				}
				return array_unique($all_location);
			}
			//*******************************//
			public static function get_faq( $tour_id ) {
				return self::get_post_info( $tour_id, 'mptbm_faq', array() );
			}
			public static function get_why_choose_us( $tour_id ) {
				return self::get_post_info( $tour_id, 'mptbm_why_choose_us_texts', array() );
			}
			//*******************************//
			public static function get_taxonomy( $name ) {
				return get_terms( array( 'taxonomy' => $name, 'hide_empty' => false ) );
			}
			//************************//
			public static function all_tax_list(): array {
				global $wpdb;
				$table_name = $wpdb->prefix . 'wc_tax_rate_classes';
				$result     = $wpdb->get_results( "SELECT * FROM $table_name" );
				$tax_list   = [];
				foreach ( $result as $tax ) {
					$tax_list[ $tax->slug ] = $tax->name;
				}
				return $tax_list;
			}
			//************************//
			public static function get_settings( $key, $option_name, $default = '' ) {
				$options = get_option( $option_name );
				return self::get_mptbm_settings( $options, $key, $default );
			}
			public static function get_mptbm_settings( $options, $key, $default = '' ) {
				if ( isset( $options[ $key ] ) && $options[ $key ] ) {
					$default = $options[ $key ];
				}
				return $default;
			}
			public static function get_general_settings( $key, $default = '' ) {
				$options = get_option( 'mptbm_general_settings' );
				return self::get_mptbm_settings( $options, $key, $default );
			}
			public static function get_style_settings( $key, $default = '' ) {
				$options = get_option( 'mptbm_style_settings' );
				return self::get_mptbm_settings( $options, $key, $default );
			}
			//***************************//
			public static function get_map_api() {
				$options = get_option( 'mptbm_general_settings' );
				$default = '';
				if ( isset( $options['ttbm_gmap_api_key'] ) && $options['ttbm_gmap_api_key'] ) {
					$default = $options['ttbm_gmap_api_key'];
				}
				return $default;
			}
			//*****************//
			public static function get_cpt_name(): string {
				return 'mptbm_rent';
			}
			public static function get_name() {
				return self::get_general_settings( 'label', 'Transportation' );
			}
			public static function get_slug() {
				return self::get_general_settings( 'slug', 'transportation' );
			}
			public static function get_icon() {
				return self::get_general_settings( 'icon', 'dashicons-car' );
			}
			public static function get_category_label() {
				return self::get_general_settings( 'category_label', 'Category' );
			}
			public static function get_category_slug() {
				return self::get_general_settings( 'category_slug', 'transportation-category' );
			}
			public static function get_organizer_label() {
				return self::get_general_settings( 'organizer_label', 'Organizer' );
			}
			public static function get_organizer_slug() {
				return self::get_general_settings( 'organizer_slug', 'transportation-organizer' );
			}
			//***********************//
			public static function esc_html( $string ): string {
				$allow_attr = array(
					'input'    => [
						'type'               => [],
						'class'              => [],
						'id'                 => [],
						'name'               => [],
						'value'              => [],
						'size'               => [],
						'placeholder'        => [],
						'min'                => [],
						'max'                => [],
						'checked'            => [],
						'required'           => [],
						'disabled'           => [],
						'readonly'           => [],
						'step'               => [],
						'data-default-color' => [],
						'data-price'         => [],
					],
					'p'        => [ 'class' => [] ],
					'img'      => [ 'class' => [], 'id' => [], 'src' => [], 'alt' => [], ],
					'fieldset' => [
						'class' => []
					],
					'label'    => [
						'for'   => [],
						'class' => []
					],
					'select'   => [
						'class'      => [],
						'name'       => [],
						'id'         => [],
						'data-price' => [],
					],
					'option'   => [
						'class'    => [],
						'value'    => [],
						'id'       => [],
						'selected' => [],
					],
					'textarea' => [
						'class' => [],
						'rows'  => [],
						'id'    => [],
						'cols'  => [],
						'name'  => [],
					],
					'h2'       => [ 'class' => [], 'id' => [], ],
					'a'        => [ 'class' => [], 'id' => [], 'href' => [], ],
					'div'      => [
						'class'                 => [],
						'id'                    => [],
						'data-ticket-type-name' => [],
					],
					'span'     => [
						'class' => [],
						'id'    => [],
						'data'  => [],
					],
					'i'        => [
						'class' => [],
						'id'    => [],
						'data'  => [],
					],
					'table'    => [
						'class' => [],
						'id'    => [],
						'data'  => [],
					],
					'tr'       => [
						'class' => [],
						'id'    => [],
						'data'  => [],
					],
					'td'       => [
						'class' => [],
						'id'    => [],
						'data'  => [],
					],
					'thead'    => [
						'class' => [],
						'id'    => [],
						'data'  => [],
					],
					'tbody'    => [
						'class' => [],
						'id'    => [],
						'data'  => [],
					],
					'th'       => [
						'class' => [],
						'id'    => [],
						'data'  => [],
					],
					'svg'      => [
						'class'   => [],
						'id'      => [],
						'width'   => [],
						'height'  => [],
						'viewBox' => [],
						'xmlns'   => [],
					],
					'g'        => [
						'fill' => [],
					],
					'path'     => [
						'd' => [],
					],
					'br'       => array(),
					'em'       => array(),
					'strong'   => array(),
				);
				return wp_kses( $string, $allow_attr );
			}
		}
		new MPTBM_Function();
	}