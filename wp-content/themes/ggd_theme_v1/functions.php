<?php
add_action( 'after_setup_theme', 'blankslate_setup' );
function blankslate_setup()
{
load_theme_textdomain( 'blankslate', get_template_directory() . '/languages' );
add_theme_support( 'automatic-feed-links' );
add_theme_support( 'post-thumbnails' );
global $content_width;
if ( ! isset( $content_width ) ) $content_width = 640;

function register_my_menus() {
  register_nav_menus(
    array(
      'main-menu' => __( 'Main Menu' ),
      'sub-menu' => __( 'Sub Menu' ),
	  'side-menu' => __( 'Side Menu' ),
	  'footer-menu' => __( 'Footer Menu' )
    )
  );
}
add_action( 'init', 'register_my_menus' );
}

add_action( 'wp_enqueue_scripts', 'blankslate_load_scripts' );
function blankslate_load_scripts()
{
wp_enqueue_script( 'jquery' );
}
add_action( 'comment_form_before', 'blankslate_enqueue_comment_reply_script' );
function blankslate_enqueue_comment_reply_script()
{
if ( get_option( 'thread_comments' ) ) { wp_enqueue_script( 'comment-reply' ); }
}
add_filter( 'the_title', 'blankslate_title' );
function blankslate_title( $title ) {
if ( $title == '' ) {
return '&rarr;';
} else {
return $title;
}
}
add_filter( 'wp_title', 'blankslate_filter_wp_title' );
function blankslate_filter_wp_title( $title )
{
return $title . esc_attr( get_bloginfo( 'name' ) );
}
add_action( 'widgets_init', 'blankslate_widgets_init' );
function blankslate_widgets_init()
{
register_sidebar( array (
'name' => __( 'Sidebar Widget Area', 'blankslate' ),
'id' => 'primary-widget-area',
'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
'after_widget' => "</li>",
'before_title' => '<h3 class="widget-title">',
'after_title' => '</h3>',
) );
}
function blankslate_custom_pings( $comment )
{
$GLOBALS['comment'] = $comment;
?>
<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>"><?php echo comment_author_link(); ?></li>
<?php 
}
add_filter( 'get_comments_number', 'blankslate_comments_number' );
function blankslate_comments_number( $count )
{
if ( !is_admin() ) {
global $id;
$comments_by_type = &separate_comments( get_comments( 'status=approve&post_id=' . $id ) );
return count( $comments_by_type['comment'] );
} else {
return $count;
}
}



/**************  Green Golf Deals Hooks  *************
Master location: includes/wc-template-hooks.php **/

/** Disable Woo Styles **/
add_filter( 'woocommerce_enqueue_styles', '__return_false' );
add_theme_support( 'genesis-connect-woocommerce' );



/**************  Single Product Page  ***************/

/** Product Page  theme/woocommerce/single-product.php **/
add_action( 'ggd_before_main_content', 'woocommerce_breadcrumb', 20, 0 );

/** Change cart button text // 2.1 + **/
add_filter( 'woocommerce_product_single_add_to_cart_text', 'woo_custom_cart_button_text' );    
function woo_custom_cart_button_text() { return __( 'Buy now!', 'woocommerce' ); }


/** Sale flashes */
remove_action( 'ggd_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );

/** Before Single Products Summary Div */
add_action( 'ggd_single_product_images', 'woocommerce_show_product_images', 1 );
add_action( 'woocommerce_product_images', 'woocommerce_show_product_thumbnails', 2 );


/** Single Product Content  theme/woocommerce/content-single-product **/
add_action( 'ggd_single_product_titles', 'woocommerce_template_single_title', 1 );
add_action( 'ggd_single_product_titles', 'woocommerce_template_single_excerpt', 2 );

add_action( 'ggd_single_product_price', 'woocommerce_template_single_price', 1 );

add_action( 'ggd_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
add_action( 'ggd_simple_add_to_cart', 'woocommerce_simple_add_to_cart', 30 );
remove_action( 'ggd_single_product_summary', 'woocommerce_template_single_sharing', 50 );

/**************  Shop Page  ***************/

/** Change cart button text // 2.1 + **/

/** Sale flashes next deal */
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );

/** Product Loop Items */
add_action( 'ggd_single_product_images', 'woocommerce_show_product_images', 1 );
add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
