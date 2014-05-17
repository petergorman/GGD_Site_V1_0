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

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
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

<div itemscope itemtype="<?php echo woocommerce_get_product_schema(); ?>" id="product-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="row">
	
		<div class="col-md-3">
		<?php
			/**
			 * woocommerce_before_single_product_summary hook
			 *
			 * @hooked woocommerce_show_product_sale_flash - 10
			 */
			do_action( 'ggd_before_single_product_summary' );
		?>
		</div>
	</div>
	
	<div class="row">
		
		<div class="col-xs-12">
			<h3><?php the_field( "client_name" ); ?></h3>
			<?php
				/* Product Content */
				do_action( 'ggd_single_product_images' );
				do_action( 'ggd_single_product_titles' );
				do_action( 'ggd_single_product_price' );
				do_action( 'ggd_single_product_summary' );
			?>
		</div>
	</div>
	
	<div class="row">
		<div class="col-xs-12">
			<h3>Details</h3>
			<?php the_field( "deal_description" ); ?>
		</div>
	</div>
	
	<div class="row">
		<div class="col-xs-12">
			<h3>The Fine Print</h3>
			<?php the_field( "fine_print" ); ?>
		</div>
	</div>

	<?php
		/**
		 * woocommerce_after_single_product_summary hook
		 *
		 * @hooked woocommerce_output_product_data_tabs - 10
		 * @hooked woocommerce_output_related_products - 20
		 */
		do_action( 'woocommerce_after_single_product_summary' );
	?>

	<meta itemprop="url" content="<?php the_permalink(); ?>" />

</div><!-- #product-<?php the_ID(); ?> -->

<?php do_action( 'woocommerce_after_single_product' ); ?>
