<?php

/**
 * Plugin Name:         WooCommerce - Limited Deals
 * Plugin URI:          http://shop.mgates.me/?p=96
 * Description:         Expiring products and display a beautiful CSS3 banner with time left or quantity sold!
 * Author:              Matt Gates
 * Author URI:          http://mgates.me
 *
 * Version:             2.1.2
 * Requires at least:   3.2.1
 * Tested up to:        3.5.1
 *
 * Text Domain:         woocommerce_limited_deals
 * Domain Path:         /LimitedDeals/languages/
 *
 * @category            Plugin
 * @copyright           Copyright © 2012 MGates LLC.
 * @author              Matt Gates
 * @package             WooCommerce
 */


if ( ! class_exists( 'MGates_Plugin_Updater' ) ) require_once 'LimitedDeals/classes/mg-includes/mg-functions.php';
if ( is_admin() ) new MGates_Plugin_Updater( __FILE__, '52418db263c687cc049c5600d018df8f' );

add_action( 'plugins_loaded', 'woocommerce_limiteddeals_load' );

/**
 *
 *
 * @return unknown
 */
function woocommerce_limiteddeals_load()
{
	if ( !is_woocommerce_activated() ) return false;

	load_plugin_textdomain( 'woocommerce_limited_deals', false, dirname( plugin_basename( __FILE__ ) ) . '/LimitedDeals/languages/' );

	class WC_Limited_Deals
	{

		public static $from, $to, $template_dir, $template_url, $_product;
		public static $available = array(), $enabled = array();

		/**
		 * Init
		 */
		public function __construct()
		{
			$this->title = __( 'Limited Deals', 'woocommerce_limited_deals' );

			self::$template_dir = dirname( __FILE__ ) . '/LimitedDeals/templates/';
			self::$template_url = plugin_dir_url( __FILE__ ) . 'LimitedDeals/templates/';

			$this->includes();
			$this->hooks();
		}


		/**
		 *
		 */
		public function includes()
		{
			require_once 'LimitedDeals/classes/class-date-diff.php';
			require_once 'LimitedDeals/classes/class-wc-settings.php';
			require_once 'LimitedDeals/classes/class-wc-product-panel.php';
			require_once 'LimitedDeals/classes/class-wc-queries.php';
			require_once 'LimitedDeals/classes/class-wc-shortcodes.php';
		}


		/**
		 *
		 */
		public function hooks()
		{
			add_filter( 'woocommerce_general_settings' , array( 'WC_Limited_Deals_Settings', 'settings' ) );

			add_action( 'woocommerce_before_single_product', array( $this, 'check_single' ) );
			add_action( 'woocommerce_checkout_process', array( $this, 'validate_checkout' ) );
			add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'validate_cart' ), 10, 3 );

			add_action( 'woocommerce_product_options_sku', array( 'WC_Limited_Deals_Product_Panel', 'write_panel' ) );
			add_action( 'woocommerce_process_product_meta', array( 'WC_Limited_Deals_Product_Panel', 'write_panel_save' ) );

			add_shortcode( 'woocommerce_limited_deals_banner', array( 'WC_Limited_Deals_Shortcodes', 'css_banner' ) );
			add_shortcode( 'woocommerce_limited_deals_info', array( 'WC_Limited_Deals_Shortcodes', 'product_info' ) );

			$show_banner = get_option( 'tp_default_banner' );
			if ( $show_banner == 'yes' ) {
				add_action( 'woocommerce_before_single_product', array( 'WC_Limited_Deals_Shortcodes', 'do_shortcode' ) );
			}
		}


		/**
		 *
		 */
		public function validate_checkout()
		{
			global $woocommerce;

			$titles = array();

			$cart = $woocommerce->cart->get_cart();
			foreach ( $cart as $product ) {
				if ( $this->is_enabled( $product['product_id'] ) && ! $this->is_available( $product['product_id'] ) ) {
					$titles[] = $product['data']->get_title();
				}
			}

			if ( !empty( $titles ) ) {
				$woocommerce->add_error( sprintf( __( 'Please remove the expired product(s) from your cart: %s' ), implode( ', ', $titles )  ) );
			}
		}


		/**
		 *
		 */
		public function check_single()
		{
			global $post;

			if ( $this->is_enabled( $post->id ) && !$this->is_available( $post->id ) ) {

				// Set visibility to hidden
				$hide = get_option( 'tp_hide_expired' );
				if ( $hide == 'yes' ) {
					update_post_meta( $post->ID, '_visibility', 'hidden' );
				}

				// Remove the add to cart button
				$disable = get_option( 'tp_disable_cart' );
				if ( $disable == 'yes' ) {
					remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
					add_action( 'woocommerce_single_product_summary', array( 'WC_Limited_Deals', 'custom_add_to_cart' ), 30 );
				}
			}
		}

		public function custom_add_to_cart()
		{
			global $product;

			$text = WC_Limited_Deals::get_custom_text( $product->id );
			$url = WC_Limited_Deals::get_custom_url( $product->id );

			if ( empty($text) && ! $url ) {
				return;
			}

			if ( ! $text ) {
				$text = __( 'Deal has expired!', 'woocommerce_limited_deals' );
			}

			if ( ! $url ) {
				$url = '#';
			}

			woocommerce_get_template( 'single-product/add-to-cart/external.php', array(
					'product_url' => $url,
					'button_text' => $text,
				) );
		}


		/**
		 * Make sure user doesn't try to add an expired product to cart
		 *
		 * @param unknown $add_to_cart
		 * @param unknown $product_id
		 * @param unknown $main_product_quantity
		 * @return unknown
		 */
		public function validate_cart( $add_to_cart, $product_id, $main_product_quantity )
		{
			global $woocommerce;

			// Not a limited deal product
			if ( !$_product = $this->is_enabled( $product_id ) ) {
				return $add_to_cart;
			}

			// Prevent adding to the cart
			$disable = get_option( 'tp_disable_cart' );
			if ( $disable == 'yes' && !$this->is_available( $product_id ) ) {
				$woocommerce->add_error( __( 'This product has expired.', 'woocommerce_limited_deals' ) );
				return false;
			}

			return $add_to_cart;
		}

		public function get_custom_text( $product_id = '' )
		{
			global $post;

			$product_id = $product_id ? $product_id : $post->ID;
			$text = get_post_meta( $product_id, '_limited_deals_button_text', true );

			return !empty( $text ) ? $text : false;
		}


		public function get_custom_url( $product_id = '' )
		{
			global $post;

			$product_id = $product_id ? $product_id : $post->ID;
			$url = get_post_meta( $product_id, '_limited_deals_url', true );

			return !empty( $url ) ? $url : false;
		}


		/**
		 *
		 *
		 * @param unknown $product_id (optional)
		 * @return unknown
		 */
		public function is_enabled( $product_id = '' )
		{
			global $post;

			$product_id = $product_id ? $product_id : $post->ID;

			if ( isset( self::$enabled[$product_id] ) ) {
				return self::$enabled[$product_id];
			}

			// Is product?
			$post_type  = get_post_type( $product_id );
			if ( !$post_type || $post_type != 'product' ) {
				self::$enabled[$product_id] = false;
				return false;
			}

			$_product = function_exists( 'get_product' ) ? get_product( $product_id ) :  new WC_Product( $product_id );
			$meta = get_post_meta( $product_id );

			// Is limited?
			$is_limited_deals = !empty( $meta['_limited'][0] ) && $meta['_limited'][0] == 'yes' ? 1 : 0;
			if ( !$is_limited_deals ) {
				self::$enabled[$product_id] = false;
				return false;
			}

			// Is on sale?
			if ( ! $_product->is_on_sale() ) {
				self::$enabled[$product_id] = false;
				return false;
			}

			// Has an end date?
			self::$to = $meta['_limited_dates_to'][0];
			self::$from = $meta['_limited_dates_from'][0];
			if ( !self::$to && !self::$from ) {
				self::$enabled[$product_id] = false;
				return false;
			}

			self::$enabled[$product_id] = true;
			self::$_product = $_product;

			return $_product;
		}


		/**
		 *
		 *
		 * @param unknown $product_id (optional)
		 * @return unknown
		 */
		public function is_available( $product_id = '' )
		{
			if ( isset( self::$available[$product_id] ) ) {
				return self::$available[$product_id];
			}

			$current_time = current_time( 'timestamp' );

			// Product is unavailable
			if ( $current_time < self::$from || $current_time > self::$to ) {
				self::$available[$product_id] = false;
				return false;
			}

			self::$available[$product_id] = true;
			return true;
		}


		/**
		 *
		 *
		 * @param unknown $regular_price
		 * @param unknown $sale_price
		 * @return unknown
		 */
		public function get_percent( $regular_price, $sale_price )
		{
			if ( strstr( $sale_price, '%' ) ) {
				$savings_percent = $sale_price;
				$sale_price = round( $regular_price * $sale_price );
			} else {
				$savings_percent = round( 100 - ( 100 * ( $sale_price / $regular_price ) ) ) . '%';
			}

			return $savings_percent;
		}


	}


	new WC_Limited_Deals();
}
