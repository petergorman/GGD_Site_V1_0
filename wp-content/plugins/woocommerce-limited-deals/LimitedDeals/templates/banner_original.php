<?php

$day_single = __( 'day', 'woocommerce_limited_deals' );
$day_plural = __( 'days', 'woocommerce_limited_deals' );

$day_text = $days != 1 ? $day_plural : $day_single;
$current_time = date( "M j, Y H:i:s", current_time( 'timestamp' ) );
$end_time = date( "M j, Y H:i:s", $end_date );

?>

<script type="text/javascript" charset="utf-8">
  jQuery(function() {
	jQuery('div#clock-<?php echo $product_id; ?>').countdown({
		onExpiry: onExpire,
		format: "dHMS",
		until: new Date("<?php echo $end_time; ?>"),
		serverSync: new Date("<?php echo $current_time; ?>"),
		layout: '<div class="days"><span id="days-<?php echo $product_id; ?>">{dn}</span> <?php echo $day_text; ?></div>' +
				'<span id="hours-<?php echo $product_id; ?>">{hn}</span> : <span id="minutes-<?php echo $product_id; ?>">{mn}</span> : <span id="seconds-<?php echo $product_id; ?>">{sn}</span>'
	});

	function onExpire() {
		console.log('hi');
		jQuery('div#clock-<?php echo $product_id; ?>').fadeTo('slow', .5);
		setTimeout("location.reload(true);", 2000);
	}
  });
</script>

<style>
.lmtd-deal.ribbon { background: <?php echo WC_Limited_Deals_Shortcodes::darken_color( $color, 0 ); ?> }
.lmtd-deal.ribbon .lmtd-deal.ribbon-content:before, .lmtd-deal.ribbon .lmtd-deal.ribbon-content:after { border-color: <?php echo WC_Limited_Deals_Shortcodes::darken_color( $color, 45 ); ?> transparent transparent transparent; }
.lmtd-deal.ribbon:before, .lmtd-deal.ribbon:after { border: 1.5em solid <?php echo WC_Limited_Deals_Shortcodes::darken_color( $color, 10 ); ?>; }
.meta-content .meta-amount { color: <?php echo WC_Limited_Deals_Shortcodes::darken_color( $text, 15 ); ?>; }
.meta-content .meta-title { color: <?php echo WC_Limited_Deals_Shortcodes::darken_color( $text, 0 ); ?>; }
.meta-content .meta-sub-title { color: <?php echo WC_Limited_Deals_Shortcodes::darken_color( $subtext, 0 ); ?>; }
</style>

<div class="non-semantic-protector">
	<h1 class="lmtd-deal ribbon">
		<span class="lmtd-deal ribbon-content">

			<div class="meta-content content-one">
				<div class="meta-amount"><?php echo $savings; ?></div>
				<div class="meta-title"><?php _e( 'Savings', 'woocommerce_limited_deals' ); ?></div>
				<div class="meta-sub-title"><?php printf( __( 'Original Price: %s', 'woocommerce_limited_deals' ), $price ); ?></div>
			</div>
			<div class="meta-content content-two">
				<div class="meta-amount"><?php echo $qty; ?></div>
				<div class="meta-title"><?php _e( 'Amount Sold', 'woocommerce_limited_deals' ) ?></div>
				<div class="meta-sub-title"><?php _e( 'Limited quantity', 'woocommerce_limited_deals' ) ?></div>
			</div>
			<div class="meta-content content-three">
				<div class="meta-amount" id="clock-<?php echo $product_id; ?>"></div>
				<div class="meta-title"><?php _e( 'Remaining', 'woocommerce_limited_deals' ) ?></div>
				<div class="meta-sub-title"><?php _e( 'Tick Tock Tick Tock', 'woocommerce_limited_deals' ) ?></div>
			</div>

		</span>
	</h1>
</div>
