<?php
# === REMOVE AUTHOR PAGE === #
add_action('template_redirect', function() {
    global $wp_query;
    if ( is_page() || is_single() || is_front_page()) return;
	$wp_query->set_404();
	status_header(404);
});

add_action('init', function() {
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );    
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );  
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
    add_filter( 'wp_resource_hints', 'disable_emojis_remove_dns_prefetch', 10, 2 );
});
function disable_emojis_remove_dns_prefetch($urls, $relation_type) {
    if ( 'dns-prefetch' == $relation_type ) {
        $emoji_svg_url_bit = 'https://s.w.org/images/core/emoji/';
        foreach ( $urls as $key => $url ) {
            if ( strpos( $url, $emoji_svg_url_bit ) !== false ) {
                unset( $urls[$key] );
            }
        }
    }
    return $urls;
}

//Remove Gutenberg Block Library CSS from loading on the frontend
function smartwp_remove_wp_block_library_css(){
    wp_dequeue_style( 'aioseop-toolbar-menu-css' );
    wp_dequeue_style( 'wpm-main-css' );
    wp_dequeue_style( 'wp-block-library' );
    wp_dequeue_style( 'wp-block-library-theme' );
    wp_dequeue_style( 'wc-blocks-style' ); // Remove WooCommerce block CSS
} 
add_action( 'wp_enqueue_scripts', 'smartwp_remove_wp_block_library_css', 100 );

add_action('wp_footer', function() {
    wp_deregister_script('wp-embed');
});