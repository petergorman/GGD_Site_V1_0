<?php

/**
 *
 *
 * @author Matt Gates <http://mgates.me>
 * @package
 */


class WC_Limited_Deals_Shortcodes extends WC_Limited_Deals
{


	/**
	 *
	 *
	 * @param unknown $color
	 * @param unknown $dif   (optional)
	 * @return unknown
	 */
	function darken_color( $color, $dif = 20 )
	{
		$color = str_replace( '#', '', $color );

		if ( empty( $dif ) ) return '#' . $color;
		if ( strlen( $color ) != 6 ) { return '#000000'; }
		$rgb = '';

		for ( $x=0;$x<3;$x++ ) {
			$c = hexdec( substr( $color, ( 2*$x ), 2 ) ) - $dif;
			$c = ( $c < 0 ) ? 0 : dechex( $c );
			$rgb .= ( strlen( $c ) < 2 ) ? '0'.$c : $c;
		}

		return '#' . $rgb;
	}


	/**
	 *
	 *
	 * @param unknown $atts
	 * @return unknown
	 */
	public function product_info( $atts )
	{
		global $post;

		extract( shortcode_atts( array(
					'product_id'  => $post->ID,
					'show'        => 'price',
					'time_format' => '%H:%I:%S',
				), $atts ) );

		$return = self::get_product_info( $product_id );
		if ( !$return ) return false;

		extract( $return );

		switch ( $show ) {
			case 'price':
				$return = $price;
				break;
			case 'qty':
				$return = $qty;
				break;
			case 'sale_price':
				$return = $sale_price;
				break;
			case 'savings':
				$return = $savings;
				break;
			case 'time':
				$return = sprintf( '%s:%s:%s', $time['h'], $time['i'], $time['s'] );
				break;
			case 'days':
				$return = $time['days'];
				break;

			default:
				$return = $price;
				break;
		}

		return $return;
	}


	/**
	 *
	 *
	 * @param unknown $product_id (optional)
	 * @return unknown
	 */
	private function get_product_info( $product_id = '' )
	{
		global $wpdb;

		if ( ! parent::is_enabled( $product_id ) ) {
			return false;
		}

		if ( ! parent::is_available( $product_id ) ) {
			return false;
		}

		$_product = parent::$_product;
		$meta      = get_post_meta( $_product->id );
		$show_sold = get_option( 'tp_amount_sold' );

		if ( $show_sold != 'no' ) {
			$qty = WC_Limited_Deals_Queries::get_product_sold_qty( $_product );
		}

		$regular_price   = $_product->product_type == 'variable' ? $_product->min_variation_regular_price : $meta['_regular_price'][0];
		$sale_price      = $_product->product_type == 'variable' ? $_product->min_variation_price : $meta['_sale_price'][0];
		$savings_percent = parent::get_percent( $regular_price, $sale_price );
		$interval        = _Date_Diff::diff( current_time( 'timestamp' ), self::$to );

		$return = array(
			'savings'    => $savings_percent,
			'sale_price' => woocommerce_price( $sale_price ),
			'price'      => woocommerce_price( $regular_price ),
			'qty'        => $show_sold != 'no' ? $qty : '-n/a-',
			'time'       => $interval,
			'days'       => $interval['d'],
			'end_date'   => self::$to,
		);

		return $return;
	}


	/**
	 *
	 *
	 * @param unknown $atts
	 * @return unknown
	 */
	public function css_banner( $atts )
	{
		global $post;

		wp_register_style( 'limited-deals', plugins_url( '/assets/css/limited-deals-styles.css', dirname( __FILE__ ) ) );
		wp_register_script( 'jquery-countdown', plugins_url( '/assets/js/jquery.countdown.min.js', dirname( __FILE__ ) ) );

		wp_enqueue_script( 'jquery-countdown' );
		wp_enqueue_style( 'limited-deals' );

		extract( shortcode_atts( array( 'product_id' => $post->ID, ), $atts ) );

		$return = self::get_product_info( $product_id );
		if ( !$return ) return false;

		$color   = get_option( 'tp_default_color' );
		$subtext = get_option( 'tp_subtext_color' );
		$text    = get_option( 'tp_text_color' );

		$return['color']      = $color ? $color : 'FCEDC4';
		$return['text']       = $text ? $text : '444444';
		$return['subtext']    = $subtext ? $subtext : 'CF4D4D';
		$return['product_id'] = $product_id;

		ob_start();
		woocommerce_get_template( 'banner.php', $return, 'wc-limited-deals/', parent::$template_dir );
		return ob_get_clean();
	}

	public function do_shortcode()
	{
		echo do_shortcode( '[woocommerce_limited_deals_banner]' );
	}

}
