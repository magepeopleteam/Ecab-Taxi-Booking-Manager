<?php
	// Template Name: Default Theme
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}
	$post_id = $post_id ?? get_the_id();
?>
	<div class="mpStyle mptbm_default_theme">
		<div class="mpContainer">
			<div class="_infoLayout_mT">
				<?php do_action( 'mptbm_details_title' ); ?>
			</div>
			<div class="dLayout_xs">
				<?php do_action( 'mptbm_super_slider', $post_id, 'mptbm_slider_images' ); ?>
			</div>
			<?php do_action( 'mptbm_transport_search_form',$post_id ); ?>
		</div>
	</div>
<?php do_action( 'mptbm_after_details_page' ); ?>