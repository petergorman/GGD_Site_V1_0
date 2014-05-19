<?php

/**
 *
 *
 * @author Matt Gates <http://mgates.me>
 * @package
 */


class WC_Limited_Deals_Queries extends WC_Limited_Deals
{

	/**
	 *
	 *
	 * @param unknown $_product
	 * @return unknown
	 */
	public function get_product_orders( $_product )
	{
		global $wpdb;

		// I wish all this didn't have to be so long ;_;
		if ( function_exists( 'get_product' ) ) {
			$product_ids = $_product->get_children() ? implode( ', ', $_product->get_children() ) : $_product->id;
			$line_items = $wpdb->get_results( ( "
					SELECT      ID
					FROM        {$wpdb->prefix}posts
					WHERE       ID
					IN          (
						SELECT      order_id
						FROM        {$wpdb->prefix}woocommerce_order_items
						WHERE       order_item_id
						IN          (
							SELECT      order_item_id
							FROM        {$wpdb->prefix}woocommerce_order_itemmeta
							WHERE       meta_key = '_product_id'
							OR          meta_key = '_variation_id'
							AND         meta_value IN (" . $product_ids . ")
						)
					)
					AND         post_type = 'shop_order'
					AND         post_date >= '" . date( 'Y-m-d H:i:s', parent::$from ) . "'
					AND         post_date <= '" . date( 'Y-m-d H:i:s', parent::$to ) . "'
				" ) );
		} else {

			add_filter( 'posts_where', array( 'WC_Limited_Deals_Queries', 'filter_where' ) );
			$line_items = query_posts( array(
					'post_type'  => 'shop_order',
					'meta_query' => array(
						'relation' => 'OR',
						array(
							'key'     => '_order_items',
							'compare' => 'LIKE',
							'value'   => ':"' . $_product->id . '";',
						),
						array(
							'key'     => '_order_items',
							'compare' => 'LIKE',
							'value'   => ':' . $_product->id . ';',
						),
					)
				) );
			remove_filter( 'posts_where', array( 'WC_Limited_Deals_Queries', 'filter_where' ) );

		}

		return $line_items;
	}


	/**
	 *
	 *
	 * @param unknown $where (optional)
	 * @return unknown
	 */
	public function filter_where( $where = '' )
	{
		$where .= " AND post_date >= '".date( 'Y-m-d H:i:s', parent::$from )."' AND post_date <= '".date( 'Y-m-d H:i:s', parent::$to )."'";
		return $where;
	}


	/**
	 *
	 *
	 * @param unknown $_product
	 * @return unknown
	 */
	public function get_product_sold_qty( $_product )
	{
		$line_items = WC_Limited_Deals_Queries::get_product_orders( $_product );

		$qty = 0;
		foreach ( $line_items as $item ) {
			$order      = new WC_Order( $item->ID );
			$products   = $order->get_items();
			$order_date = $order->order_date;

			foreach ( $products as $product ) {
				$search = function_exists( 'get_product' ) ? 'product_id' : 'id';
				if ( !empty( $product[$search] ) && $product[$search] == $_product->id ) {
					$qty += $product['qty'];
				}
			}
		}

		return $qty;
	}


}
