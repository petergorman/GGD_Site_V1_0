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
			<?php wp_nav_menu( array( 'theme_location' => 'logged-in-menu' ) ); ?>
		 <?php } 
		 else { ?>
			<?php wp_nav_menu( array( 'theme_location' => 'logged-out-menu' ) ); ?>
		 <?php } ?>
	</div> <!-- END Col -->
</div> <!-- END Row -->
