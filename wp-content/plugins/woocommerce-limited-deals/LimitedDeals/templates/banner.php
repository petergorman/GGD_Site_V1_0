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
		layout: '<div><span id="days-<?php echo $product_id; ?>">{dn}</span> <?php echo $day_text; ?></div>' +
				'<span id="hours-<?php echo $product_id; ?>">{hn}</span> : <span id="minutes-<?php echo $product_id; ?>">{mn}</span> : <span id="seconds-<?php echo $product_id; ?>">{sn}</span>'
	});

	function onExpire() {
		console.log('hi');
		jQuery('div#clock-<?php echo $product_id; ?>').fadeTo('slow', .5);
		setTimeout("location.reload(true);", 2000);
	}
  });
</script>
<p><?php _e( 'Savings', 'woocommerce_limited_deals' ); ?> <?php echo $savings; ?><br>
<?php printf( __( 'Original Price: %s', 'woocommerce_limited_deals' ), $price ); ?></p>

<!--<p><?php echo $qty; ?><br>
<?php _e( 'Amount Sold', 'woocommerce_limited_deals' ) ?><br>
<?php _e( 'Limited quantity', 'woocommerce_limited_deals' ) ?></p>-->

<div class="clock" id="clock-<?php echo $product_id; ?>"></div>
<?php /*?><?php _e( 'Remaining', 'woocommerce_limited_deals' ) ?><?php _e( 'Hurry, deal ends soon', 'woocommerce_limited_deals' ) ?><?php */?>