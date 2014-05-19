<?php

/**
 *
 *
 * @author Matt Gates <http://mgates.me>
 * @package
 */


class WC_Limited_Deals_Settings
{

	/**
	 *
	 *
	 * @param unknown $settings
	 * @return unknown
	 */
	public function settings( $settings )
	{
		$settings[] = array( 'name' => __( 'Limited Deals', 'woocommerce' ), 'type' => 'title', 'desc' => 'The following options are used to configure the Limited Deals extension.', 'id' => 'limited_deals' );

		$settings[] = array(
			'id'   => 'tp_hide_expired',
			'name' => __( 'Expired products', 'woocommerce_limited_deals' ),
			'desc' => __( 'Set visibility to hidden', 'woocommerce_limited_deals' ),
			'desc_tip' => __( 'The product will not be visible from the shop pages', 'woocommerce_limited_deals' ),
			'type' => 'checkbox',
			'checkboxgroup' => 'start'
		);

		$settings[] = array(
			'id'       => 'tp_disable_cart',
			'desc'     => __( 'Disable adding to cart', 'woocommerce_limited_deals' ),
			'desc_tip' => __( 'The add to cart button will be disabled and the product will be prevented from being purchased', 'woocommerce_limited_deals' ),
			'type'     => 'checkbox',
			'checkboxgroup' => 'end'
		);

		$settings[] = array(
			'id'       => 'tp_amount_sold',
			'name'     => __( 'Product page', 'woocommerce_limited_deals' ),
			'desc'     => __( 'Show amount sold', 'woocommerce_limited_deals' ),
			'desc_tip' => __( 'If unchecked, -n/a- will be shown instead of a quantity', 'woocommerce_limited_deals' ),
			'type'     => 'checkbox',
			'checkboxgroup' => 'start'
		);

		$settings[] = array(
			'id'   => 'tp_default_banner',
			'desc' => __( 'Show CSS3 banner by default', 'woocommerce_limited_deals' ),
			'type' => 'checkbox',
			'checkboxgroup' => 'end'
		);

		$settings[] = array(
			'id'   => 'tp_default_color',
			'name' => __( 'Ribbon color', 'woocommerce_limited_deals' ),
			'desc' => __( 'Default color is <code>#FCEDC4</code>', 'woocommerce_limited_deals' ),
			'default' => '#FCEDC4',
			'type' => 'color',
		);

		$settings[] = array(
			'id'   => 'tp_text_color',
			'name' => __( 'Main text color', 'woocommerce_limited_deals' ),
			'desc' => __( 'Default color is <code>#444444</code>', 'woocommerce_limited_deals' ),
			'default' => '#444444',
			'type' => 'color',
		);

		$settings[] = array(
			'id'   => 'tp_subtext_color',
			'name' => __( 'Subtext color', 'woocommerce_limited_deals' ),
			'desc' => __( 'Default color is <code>#CF4D4D</code>', 'woocommerce_limited_deals' ),
			'default' => '#CF4D4D',
			'type' => 'color',
		);


		$settings[] = array( 'type' => 'sectionend', 'id' => 'limited_deals' );

		return $settings;
	}


}
