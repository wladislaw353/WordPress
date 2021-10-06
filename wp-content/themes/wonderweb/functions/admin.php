<?php
add_filter('show_admin_bar', '__return_false');

add_action('admin_bar_menu', function($wp_admin_bar) {
    $wp_admin_bar->remove_node('wp-logo');
    $wp_admin_bar->add_menu([
        'id'    => 'menu_id',
        'title' => '<img src="/wp-includes/logo-mini.png" style="width:19px;transform:translateY(5px);">',
        'href'  => '',
        'meta'  => [],
    ]);
    $wp_admin_bar->add_menu([
        'parent' => 'menu_id',
        'id'     => 'child_id',
        'title'  => 'Группы полей',
        'href'   => '/wp-admin/edit.php?post_type=acf-field-group',
        'meta'   => [],
    ]);
}, 10);

if( function_exists('acf_add_options_page') ) {
	acf_add_options_page([
		'menu_title' => 'Контакты',
		'page_title' => 'Контакты',
		'icon_url' => 'dashicons-email-alt',
	]);
	acf_add_options_page([
		'menu_title' => 'Аналитика',
		'page_title' => 'Идентификаторы аналитики',
		'icon_url' => 'dashicons-code-standards',
	]);
}

add_action('admin_bar_menu', function($wp_admin_bar) {
    $wp_admin_bar->remove_node('wp-logo');
}, 999);

add_action('add_admin_bar_menus', function() {
	remove_action('admin_bar_menu', 'wp_admin_bar_updates_menu', 50);
});

add_action('admin_menu', function() {
    remove_submenu_page('themes.php', 'customize.php?return=' . urlencode($_SERVER['SCRIPT_NAME']));
    remove_menu_page('edit.php?post_type=acf-field-group');
}, 99999);

add_filter( 'plugin_action_links', function($actions, $plugin_file) {
	unset( $actions['edit'] );
	$important_plugins = array(
		'advanced-custom-fields/acf.php',
		'advanced-custom-fields-pro/acf.php',
		'all-in-one-seo-pack/all_in_one_seo_pack.php',
		'wp-multilang/wp-multilang.php',
		'redirection/redirection.php',
		'cyr3lat/cyr-to-lat.php',
		'svg-support/svg-support.php',
	);
	if ( in_array($plugin_file, $important_plugins) ) {
		unset( $actions['deactivate'] );
		$actions[ 'info' ] = '<b class="musthave_js">Actions blocked for security reasons</b><style>#advanced-custom-fields-update,#advanced-custom-fields-pro-update,#all-in-one-seo-pack-update,#wp-multilang-update{display:none}</style>';
	}
	return $actions;
}, 10, 2);

add_filter('admin_print_footer_scripts-plugins.php', function($actions) { 
    ?><script>jQuery(function($){$('.musthave_js').closest('tr').find('input[type="checkbox"]').remove()})</script><?php 
});

add_filter('login_headertext', function ($login_header_text) {
	return '<svg width="86" height="86" viewBox="0 0 71 71" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: block;transform: translate(-1px, -1px);"> <rect width="71" height="71" rx="35.5" fill="#f0f0f1" style=" "></rect><rect width="69" height="69" rx="35.5" fill="#fff" style="transform: translate(1px, 1px);"></rect> <path d="M35.5 63C50.6878 63 63 50.6878 63 35.5C63 20.3122 50.6878 8 35.5 8C20.3122 8 8 20.3122 8 35.5C8 50.6878 20.3122 63 35.5 63Z" stroke="#3E3E3E" stroke-miterlimit="10"></path> <path d="M35.5 50.6252C43.8534 50.6252 50.6252 43.8534 50.6252 35.5C50.6252 27.1466 43.8534 20.3748 35.5 20.3748C27.1466 20.3748 20.3748 27.1466 20.3748 35.5C20.3748 43.8534 27.1466 50.6252 35.5 50.6252Z" fill="url(#paint0_linear)"></path> <path d="M51.817 16.7828C53.7411 16.7828 55.3008 15.2231 55.3008 13.299C55.3008 11.375 53.7411 9.81525 51.817 9.81525C49.893 9.81525 48.3332 11.375 48.3332 13.299C48.3332 15.2231 49.893 16.7828 51.817 16.7828Z" fill="url(#paint1_linear)"></path> <defs> <linearGradient id="paint0_linear" x1="21.453" y1="39.5284" x2="50.5372" y2="31.1874" gradientUnits="userSpaceOnUse"> <stop stop-color="#55F5FF"></stop> <stop offset="0.48" stop-color="#00B5EE"></stop> <stop offset="0.58" stop-color="#0BACEE"></stop> <stop offset="0.74" stop-color="#2895F0"></stop> <stop offset="0.95" stop-color="#5670F2"></stop> <stop offset="1" stop-color="#6167F2"></stop> </linearGradient> <linearGradient id="paint1_linear" x1="48.5816" y1="14.2269" x2="55.2805" y2="12.3057" gradientUnits="userSpaceOnUse"> <stop stop-color="#55F5FF"></stop> <stop offset="0.48" stop-color="#00B5EE"></stop> <stop offset="0.58" stop-color="#0BACEE"></stop> <stop offset="0.74" stop-color="#2895F0"></stop> <stop offset="0.95" stop-color="#5670F2"></stop> <stop offset="1" stop-color="#6167F2"></stop> </linearGradient> </defs> </svg>';
});

add_filter('post_type_labels_post', function($labels) {
	$new = [
		'name'                  => 'Статьи',
		'singular_name'         => 'Статья',
		'add_new'               => 'Добавить статью',
		'add_new_item'          => 'Добавить статью',
		'edit_item'             => 'Редактировать статью',
		'new_item'              => 'Новая статья',
		'view_item'             => 'Просмотреть статью',
		'search_items'          => 'Поиск статей',
		'not_found'             => 'Статей не найдено.',
		'not_found_in_trash'    => 'Статей в корзине не найдено.',
		'parent_item_colon'     => '',
		'all_items'             => 'Все статьи',
		'archives'              => 'Архивы статей',
		'insert_into_item'      => 'Вставить в статью',
		'uploaded_to_this_item' => 'Загруженные для этой статьи',
		'featured_image'        => 'Миниатюра статьи',
		'filter_items_list'     => 'Фильтровать список статей',
		'items_list_navigation' => 'Навигация по списку статей',
		'items_list'            => 'Список статей',
		'menu_name'             => 'Статьи',
		'name_admin_bar'        => 'Статью'
    ];
	return (object) array_merge( (array) $labels, $new );
});

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