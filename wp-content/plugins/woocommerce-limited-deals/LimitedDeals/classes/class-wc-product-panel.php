<?php

/**
 *
 *
 * @author Matt Gates <http://mgates.me>
 * @package
 */


class WC_Limited_Deals_Product_Panel
{

	/**
	 * Show the limited deal settings on a product's General tab
	 */
	public function write_panel()
	{
		global $post, $woocommerce;

		wp_enqueue_script( 'datetimepicker-js', plugins_url( 'assets/js/jquery-ui-timepicker-addon.js', dirname( __FILE__ ) ), 'jquery' );

		// WC 2.1
		if ( version_compare( str_replace('-bleeding', '', WOOCOMMERCE_VERSION), '2.1', '>=' ) ) {
			wp_enqueue_script( 'limited-deals-js', plugins_url( 'assets/js/product-panel-2.1.js', dirname( __FILE__ ) ), 'datetimepicker-js' );
		} else {
			wp_enqueue_script( 'limited-deals-js', plugins_url( 'assets/js/product-panel.js', dirname( __FILE__ ) ), 'datetimepicker-js' );
		}

		wp_enqueue_style( 'datetimepicker-css', plugins_url( 'assets/css/jquery-ui-timepicker-addon.css', dirname( __FILE__ ) ) );

		$show_if = apply_filters( 'wc_limited_deals_show_if', array(
			'show_if_simple',
			'show_if_variable',
			'show_if_external',
		) );

		echo '</div>';

		echo '<div class="options_group ' . implode( ' ', $show_if ). '" id="limited-deals-options">';

		woocommerce_wp_checkbox( array(
				'id'            => '_limited',
				'wrapper_class' => '',
				'label'         => __( 'Limited deal', 'woocommerce_limited_deals' ),
				'description'   => __( 'Only offer this product between the scheduled date. Afterwhich, the product cannot be purchased.', 'woocommerce_limited_deals' ),
			) );

		woocommerce_wp_text_input( array(
				'id'          => '_limited_dates_from',
				'label'       => __( 'Enable purchase on', 'woocommerce_limited_deals' ),
				'value'       => ( $date = get_post_meta( $post->ID, '_limited_dates_from', true ) ) ? date_i18n( 'Y-m-d H:i', $date ) : '',
				'placeholder' => 'YYYY-MM-DD HH:MM',
				'desc_tip'    => true,
				'description' => __( 'The time is in local time. Make sure you set your local time in WP Settings > General > Timezone', 'woocommerce_limited_deals' ),
			) );

		woocommerce_wp_text_input( array(
				'id'          => '_limited_dates_to',
				'label'       => __( 'Disable purchase on', 'woocommerce_limited_deals' ),
				'value'       => ( $date = get_post_meta( $post->ID, '_limited_dates_to', true ) ) ? date_i18n( 'Y-m-d H:i', $date ) : '',
				'placeholder' => 'YYYY-MM-DD HH:MM',
				'desc_tip'    => true,
				'description' => __( 'The time is in local time. Make sure you set your local time in WP Settings > General > Timezone', 'woocommerce_limited_deals' ),
			) );

		woocommerce_wp_text_input( array(
				'id'          => '_limited_deals_url',
				'label'       => __( 'Redirect to URL after sale', 'woocommerce_limited_deals' ),
				'placeholder' => 'http://google.com/',
				'desc_tip'    => true,
				'type'        => 'url',
				'description' => __( 'If you leave this blank the add to cart button will be hidden. By entering a URL here, the add to cart button will display, but it will redirect to the URL specified in this field.', 'woocommerce_limited_deals' ),
			) );

		woocommerce_wp_text_input( array(
				'id'          => '_limited_deals_button_text',
				'label'       => __( 'Add to cart button text', 'woocommerce_limited_deals' ),
				'placeholder' => __( 'Deal has expired!', 'woocommerce_limited_deals' ),
				'desc_tip'    => true,
				'description' => __( 'Once the sale is over, the add to cart button text will change to this.', 'woocommerce_limited_deals' ),
			) );
	}


	/**
	 *
	 *
	 * @param unknown $post_id
	 */
	public function write_panel_save( $post_id )
	{
		$limited   = !empty( $_POST['_limited'] )                     ? 'yes' : 'no';
		$date_from = !empty( $_POST['_limited_dates_from'] )          ? strtotime( woocommerce_clean( $_POST['_limited_dates_from'] ) ) : '';
		$date_to   = !empty( $_POST['_limited_dates_to'] )            ? strtotime( woocommerce_clean( $_POST['_limited_dates_to'] ) ) : '';
		$url       = !empty( $_POST['_limited_deals_url'] )           ? woocommerce_clean( $_POST['_limited_deals_url'] ) : '';
		$text      = !empty( $_POST['_limited_deals_button_text'] )   ? woocommerce_clean( $_POST['_limited_deals_button_text'] ) : '';

		update_post_meta( $post_id, '_limited_dates_from', $date_from );
		update_post_meta( $post_id, '_limited_dates_to', $date_to );
		update_post_meta( $post_id, '_limited', $limited );
		update_post_meta( $post_id, '_limited_deals_url', $url );
		update_post_meta( $post_id, '_limited_deals_button_text', $text );

		$current_time = current_time( 'timestamp' );
		if ( ( $date_from && $date_to ) && ( $current_time < $date_from || $current_time > $date_to ) ) {

			// Set visibility to hidden if product should not be available
			$hide = get_option( 'tp_hide_expired' );
			if ( $hide == 'yes' ) {
				update_post_meta( $post_id, '_visibility', 'hidden' );
			}

		}
	}


}
