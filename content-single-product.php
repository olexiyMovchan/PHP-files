<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * Override this template by copying it to yourtheme/woocommerce/content-single-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<?php
	/**
	 * woocommerce_before_single_product hook
	 *
	 * @hooked wc_print_notices - 10
	 */
	 do_action( 'woocommerce_before_single_product' );

	 if ( post_password_required() ) {
	 	echo get_the_password_form();
	 	return;
	 }
?>
<?php

// get product ID
global $product;
$id = $product->id;

$id_text = 'product_ids';

// get coupon ID could be used to this product
$results = $wpdb->get_results(
	$wpdb->prepare(
		"
    SELECT post_id
	FROM wp_postmeta
	WHERE meta_value = %d
	AND meta_key = %s
    ",
		$id,'product_ids'
	)
);

$array = json_decode(json_encode($results), true);

$coupon_id = $array[0]['post_id'];


// get coupon amount
$results_amount = $wpdb->get_results(
	$wpdb->prepare(
		"
    SELECT meta_value
	FROM wp_postmeta
	WHERE meta_key = %s
	AND post_id = %d
    ",
		'coupon_amount',$coupon_id
	)
);

$array1 = json_decode(json_encode($results_amount), true);

$coupon_amount = $array1[0]['meta_value'];

// get coupon title form its ID
$coupon = $wpdb->get_results(
	$wpdb->prepare(
		"
    SELECT post_title
	FROM wp_posts
	WHERE ID = %d
    ",
		$coupon_id
	)
);

$array2 = json_decode(json_encode($coupon), true);

$coupon_title = $array2[0]['post_title'];


?>

<script>
	var $ = jQuery;
	var couponTitle = '<?php if($coupon_title){ echo $coupon_title; } ?>';
	var couponAmount = '<?php if($coupon_amount){ echo $coupon_amount; } ?>';

	if( couponTitle!=='' && typeof couponTitle!='undefined' && couponAmount!=='' && typeof couponAmount!='undefined'){

		if ( $('#couponCode').length > 0 ){
			$('#couponCode').text(couponTitle);
		}

		if ( $('#couponAmount').length > 0 ){
			$('#couponAmount').text(couponAmount);
		}

		setTimeout(function(){
			$('.above-header').slideDown('slow');
		}, 3000);


		$('.offer-coupone .activate-coupon').on('click', function(e){
			e.preventDefault();

			// set storage variable
			if(typeof(Storage) !== "undefined") {
				sessionStorage.coupon = '';
				sessionStorage.coupon = couponTitle;
			}

			$('.single_add_to_cart_button').click();
		});

	}

</script>


<div itemscope itemtype="<?php echo woocommerce_get_product_schema(); ?>" id="product-<?php the_ID(); ?>" <?php post_class(); ?>>

	<!-- Image Block -->
	<div class="col-md-8 col-lg-8 flippingbook-box">
		<div class="row">
			<?php the_content(); ?>
		</div>

		<div id="single-product-meta-box-copy" class="single-product-meta-box visible-xs visible-sm"></div>
		<div id="single-product-price-box-copy" class="single-product-data-box visible-xs visible-sm"></div>
		<div id="single-product-button-box-copy" class="single-product-button-box visible-xs visible-sm"></div>

		<div class="row">
			<div class="col-md-12 topic-areas">
				<div class="row">
					<?php woocommerce_template_single_excerpt();?>
				</div>
			</div>
		</div>
	</div>
	<!-- / Image Block -->

	<!-- Description Block -->
	<div class="col-md-4 col-lg-4">
		<div class="row">
			<div class="single-product-data-box" id="hidden-data-box">
				<div class="single-product-price-box" id="single-product-price-box">
					<?php woocommerce_template_single_price(); ?>
					<span class="product-currency-changer">
					<?php echo do_shortcode('[woocs]');?>
				</span>
				</div>
				<div class="single-product-button-box single-product-data-box-original" id="single-product-button-box">
					<div class="widget-block-single-prod">
						<?php if(!function_exists('dynamic_sidebar') || !dynamic_sidebar('breadcrumb-area')) ?>
					</div>
					<?php woocommerce_template_single_add_to_cart(); ?>
				</div>
				<div class="single-product-meta-box" id="single-product-meta-box">
					<?php woocommerce_template_single_meta(); ?>
					<p class="single-product-type">
						<span class="single-prod-meta-header">Type:&nbsp;</span>
						<?php echo get_post_meta( $post->ID, '_text_field_type', true ); ?>
					</p>
					<p class="single-product-access">
						<span class="single-prod-meta-header">Course Access:&nbsp;</span>
						<?php echo get_post_meta( $post->ID, '_text_field_access', true ); ?>
					</p>
					<div class="single-product-video-links">
						<?php

						global $product;
						$attachment_ids = $product->get_gallery_attachment_ids();

						foreach( $attachment_ids as $attachment_id )
						{
							// echo $Original_image_url = wp_get_attachment_url( $attachment_id );
							$link_href = get_post_meta( $post->ID, '_text_field_video', true );

							echo "<a href='".$link_href."' target='_blank' class=''>".wp_get_attachment_image($attachment_id, 'full')."</a>";
						}

						//echo get_post_meta( $post->ID, '_text_field_video', true ); ?>
						<p>Video of the teacher</p>
					</div>
					<?php
						$downloads = $product->get_files();

						foreach( $downloads as $key => $each_download ) {
							echo '<a class="single-prod-download-link" href="'.$each_download["file"].'" target="_blank" download>Download PDF</a>';
						}
					?>

				</div>

			</div>
		</div>
	</div>
	<!-- / Description Block -->

	<meta itemprop="url" property="url" content="<?php the_permalink(); ?>" />

</div><!-- #product-<?php the_ID(); ?> -->

<?php do_action( 'woocommerce_after_single_product' ); ?>
