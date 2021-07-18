<?php
if ( !defined('_S_VERSION') ) define('_S_VERSION', '1.0.0');

require_once 'functions/reset.php';
require_once 'functions/admin.php';
require_once 'functions/custom-post-types.php';
require_once 'functions/widgets.php';

add_action('after_setup_theme', 'theme_setup');

add_action('wp_head', function() { if (!empty(get_field('gtm' , 'option'))): ?><script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start': new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0], j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src= 'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f); })(window,document,'script','dataLayer','<?=get_field('gtm' , 'option')?>');</script><?php endif; }, 1);
add_action('wp_body_open', function() { if (!empty(get_field('gtm' , 'option'))): ?><noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?=get_field('gtm' , 'option')?>" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript><?php endif; }, 1);

add_action('wp_enqueue_scripts', function() {
	# === CSS === #
	wp_enqueue_style( 'swipercss', 'https://unpkg.com/swiper/swiper-bundle.min.css' );
	wp_enqueue_style( 'style', get_template_directory_uri() . '/css/main.min.css' );
});
add_action('wp_footer', function() {
	wp_deregister_script('jquery-core');
	wp_deregister_script('jquery');
	wp_register_script( 'jquery-core', 'https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js', false, null, true );
	wp_register_script( 'jquery', false, array('jquery-core'), null, true );
	wp_enqueue_script( 'jquery' );
	# === JS  === #
	wp_enqueue_script( 'swiperjs', 'https://unpkg.com/swiper/swiper-bundle.min.js' );
	wp_enqueue_script( 'main', get_template_directory_uri() . '/js/main.js' );
});


# === MULTILANG === #
function mlt($ru, $ua = false, $en = false) {
	if (get_bloginfo('language')=='ru-ua') return $ru;
	if (get_bloginfo('language')=='uk-ua') return $ua;
	else return $en;
}


if( function_exists('acf_add_options_page') ) {
	acf_add_options_page([
		'menu_title' => 'Контакты',
		'page_title' => 'Контакты',
		'icon_url' => 'dashicons-email-alt',
	]);
	acf_add_options_page([
		'menu_title' => 'GTM',
		'page_title' => 'GTM',
		'icon_url' => 'dashicons-code-standards',
	]);
}


if ( !function_exists('theme_setup') ) :
	function theme_setup() {
		load_theme_textdomain( 'wonderweb-custom-theme', get_template_directory() . '/languages' );
		add_theme_support( 'menus' );
		// add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'post-formats');
		add_theme_support(
			'html5',
			array(
				'search-form',
				// 'comment-form',
				// 'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
			)
		);
		add_theme_support(
			'custom-background',
			apply_filters(
				'theme_custom_background_args',
				array(
					'default-color' => 'ffffff',
					'default-image' => '',
				)
			)
		);
		add_theme_support( 'customize-selective-refresh-widgets' );
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 250,
				'width'       => 250,
				'flex-width'  => true,
				'flex-height' => true,
			)
		);
	}
endif;

add_filter('excerpt_more', function($more) {
	return '..';
});

function wonderweb_breadcrumbs() {
	$text['home']     = get_bloginfo(); // текст для главной
	$text['category'] = '%s'; // текст для страницы рубрики
	$text['search']   = 'Результаты поиска по запросу "%s"';
	$text['tag']      = 'Записи с тегом "%s"';
	$text['author']   = 'Статьи автора %s';
	$text['404']      = '404';
	$text['page']     = 'Страница %s';
	$text['cpage']    = 'Страница комментариев %s';

	$wrap_before    = '<div class="breadcrumbs" itemscope itemtype="http://schema.org/BreadcrumbList">';
	$wrap_after     = '</div>';
	$sep            = ' / '; // разделитель между "крошками"
	$before         = '<span class="breadcrumbs-current">';
	$after          = '</span>';

	$show_home_link = 1; // показывать ссылку "Главная"
	$show_current   = 1; // показывать название текущей страницы
	$show_last_sep  = 0; // показывать последний разделитель, когда название текущей страницы не отображается

	global $post;
	$home_url       = home_url('/');
	$link           = '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
	$link          .= '<a class="breadcrumbs-link" href="%1$s" itemprop="item"><span itemprop="name">%2$s</span></a>';
	$link          .= '<meta itemprop="position" content="%3$s" />';
	$link          .= '</span>';
	$parent_id      = ( $post ) ? $post->post_parent : '';
	$home_link      = sprintf( $link, $home_url, $text['home'], 1 );

	if ( !is_home() and !is_front_page() ) { $position = 0; echo $wrap_before; if ( $show_home_link ) { $position += 1; echo $home_link; } if ( is_category() ) { $parents = get_ancestors( get_query_var('cat'), 'category' ); foreach ( array_reverse( $parents ) as $cat ) { $position += 1; if ( $position > 1 ) echo $sep; echo sprintf( $link, get_category_link( $cat ), get_cat_name( $cat ), $position ); } if ( get_query_var( 'paged' ) ) { $position += 1; $cat = get_query_var('cat'); echo $sep . sprintf( $link, get_category_link( $cat ), get_cat_name( $cat ), $position ); echo $sep . $before . sprintf( $text['page'], get_query_var( 'paged' ) ) . $after; } else { if ( $show_current ) { if ( $position >= 1 ) echo $sep; echo $before . sprintf( $text['category'], single_cat_title( '', false ) ) . $after; } elseif ( $show_last_sep ) echo $sep; } } elseif ( is_search() ) { if ( get_query_var( 'paged' ) ) { $position += 1; if ( $show_home_link ) echo $sep; echo sprintf( $link, $home_url . '?s=' . get_search_query(), sprintf( $text['search'], get_search_query() ), $position ); echo $sep . $before . sprintf( $text['page'], get_query_var( 'paged' ) ) . $after; } else { if ( $show_current ) { if ( $position >= 1 ) echo $sep; echo $before . sprintf( $text['search'], get_search_query() ) . $after; } elseif ( $show_last_sep ) echo $sep; } } elseif ( is_year() ) { if ( $show_home_link && $show_current ) echo $sep; if ( $show_current ) echo $before . get_the_time('Y') . $after; elseif ( $show_home_link && $show_last_sep ) echo $sep; } elseif ( is_month() ) { if ( $show_home_link ) echo $sep; $position += 1; echo sprintf( $link, get_year_link( get_the_time('Y') ), get_the_time('Y'), $position ); if ( $show_current ) echo $sep . $before . get_the_time('F') . $after; elseif ( $show_last_sep ) echo $sep; } elseif ( is_day() ) { if ( $show_home_link ) echo $sep; $position += 1; echo sprintf( $link, get_year_link( get_the_time('Y') ), get_the_time('Y'), $position ) . $sep; $position += 1; echo sprintf( $link, get_month_link( get_the_time('Y'), get_the_time('m') ), get_the_time('F'), $position ); if ( $show_current ) echo $sep . $before . get_the_time('d') . $after; elseif ( $show_last_sep ) echo $sep; } elseif ( is_single() && ! is_attachment() ) { if ( get_post_type() != 'post' ) { $position += 1; $post_type = get_post_type_object( get_post_type() ); if ( $position > 1 ) echo $sep; echo sprintf( $link, get_post_type_archive_link( $post_type->name ), $post_type->labels->name, $position ); if ( $show_current ) echo $sep . $before . get_the_title() . $after; elseif ( $show_last_sep ) echo $sep; } else { $cat = get_the_category(); $catID = $cat[0]->cat_ID; $parents = get_ancestors( $catID, 'category' ); $parents = array_reverse( $parents ); $parents[] = $catID; foreach ( $parents as $cat ) { $position += 1; if ( $position > 1 ) echo $sep; echo sprintf( $link, get_category_link( $cat ), get_cat_name( $cat ), $position ); } if ( get_query_var( 'cpage' ) ) { $position += 1; echo $sep . sprintf( $link, get_permalink(), get_the_title(), $position ); echo $sep . $before . sprintf( $text['cpage'], get_query_var( 'cpage' ) ) . $after; } else { if ( $show_current ) echo $sep . $before . get_the_title() . $after; elseif ( $show_last_sep ) echo $sep; } } } elseif ( is_post_type_archive() ) { $post_type = get_post_type_object( get_post_type() ); if ( get_query_var( 'paged' ) ) { $position += 1; if ( $position > 1 ) echo $sep; echo sprintf( $link, get_post_type_archive_link( $post_type->name ), $post_type->label, $position ); echo $sep . $before . sprintf( $text['page'], get_query_var( 'paged' ) ) . $after; } else { if ( $show_home_link && $show_current ) echo $sep; if ( $show_current ) echo $before . $post_type->label . $after; elseif ( $show_home_link && $show_last_sep ) echo $sep; } } elseif ( is_attachment() ) { $parent = get_post( $parent_id ); $cat = get_the_category( $parent->ID ); $catID = $cat[0]->cat_ID; $parents = get_ancestors( $catID, 'category' ); $parents = array_reverse( $parents ); $parents[] = $catID; foreach ( $parents as $cat ) { $position += 1; if ( $position > 1 ) echo $sep; echo sprintf( $link, get_category_link( $cat ), get_cat_name( $cat ), $position ); } $position += 1; echo $sep . sprintf( $link, get_permalink( $parent ), $parent->post_title, $position ); if ( $show_current ) echo $sep . $before . get_the_title() . $after; elseif ( $show_last_sep ) echo $sep; } elseif ( is_page() && ! $parent_id ) { if ( $show_home_link && $show_current ) echo $sep; if ( $show_current ) echo $before . get_the_title() . $after; elseif ( $show_home_link && $show_last_sep ) echo $sep; } elseif ( is_page() && $parent_id ) { $parents = get_post_ancestors( get_the_ID() ); foreach ( array_reverse( $parents ) as $pageID ) { $position += 1; if ( $position > 1 ) echo $sep; echo sprintf( $link, get_page_link( $pageID ), get_the_title( $pageID ), $position ); } if ( $show_current ) echo $sep . $before . get_the_title() . $after; elseif ( $show_last_sep ) echo $sep; } elseif ( is_tag() ) { if ( get_query_var( 'paged' ) ) { $position += 1; $tagID = get_query_var( 'tag_id' ); echo $sep . sprintf( $link, get_tag_link( $tagID ), single_tag_title( '', false ), $position ); echo $sep . $before . sprintf( $text['page'], get_query_var( 'paged' ) ) . $after; } else { if ( $show_home_link && $show_current ) echo $sep; if ( $show_current ) echo $before . sprintf( $text['tag'], single_tag_title( '', false ) ) . $after; elseif ( $show_home_link && $show_last_sep ) echo $sep; } } elseif ( is_author() ) { $author = get_userdata( get_query_var( 'author' ) ); if ( get_query_var( 'paged' ) ) { $position += 1; echo $sep . sprintf( $link, get_author_posts_url( $author->ID ), sprintf( $text['author'], $author->display_name ), $position ); echo $sep . $before . sprintf( $text['page'], get_query_var( 'paged' ) ) . $after; } else { if ( $show_home_link && $show_current ) echo $sep; if ( $show_current ) echo $before . sprintf( $text['author'], $author->display_name ) . $after; elseif ( $show_home_link && $show_last_sep ) echo $sep; } } elseif ( is_404() ) { if ( $show_home_link && $show_current ) echo $sep; if ( $show_current ) echo $before . $text['404'] . $after; elseif ( $show_last_sep ) echo $sep; } elseif ( has_post_format() && ! is_singular() ) { if ( $show_home_link && $show_current ) echo $sep; echo get_post_format_string( get_post_format() ); } echo $wrap_after;}
}