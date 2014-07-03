<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<title><?php wp_title( ' | ', true, 'right' ); ?></title>

<link rel="icon" type="image/png" href="<?php bloginfo('template_directory'); ?>/img/favicon.png" />

<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>

<link rel="stylesheet" type="text/css" href="<?php echo get_stylesheet_uri(); ?>" />

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<div class="container">

<div class="row">
	<div class="col-xs-12">
		<?php wp_nav_menu( array( 'theme_location' => 'main-menu' ) ); ?>
		
		<?php if ( is_user_logged_in() ) { ?>
			<a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" title="<?php _e('My Account','woothemes'); ?>"><?php _e('My Account','woothemes'); ?></a><br>
			<a href="<?php echo wp_logout_url( get_permalink( wc_get_page_id( 'myaccount' ) ) ); ?>">Log Out</a><br>
		 <?php } 
		 else { ?>
			<a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" title="<?php _e('Login / Register','woothemes'); ?>"><?php _e('Login / Register','woothemes'); ?></a>
		 <?php } ?>

		
		
		
		
		
		<?php /*?><?php if ( is_user_logged_in() ) { ?>
			<?php wp_nav_menu( array( 'theme_location' => 'logged-in-menu' ) ); ?>
		 <?php } 
		 else { ?>
			<?php wp_nav_menu( array( 'theme_location' => 'logged-out-menu' ) ); ?>
		 <?php } ?><?php */?>
	</div> <!-- END Col -->
</div> <!-- END Row -->
