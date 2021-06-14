<?php
// add_action( 'init', 'register_faq_post_type' );
// function register_faq_post_type() {

// 	// Раздел вопроса - faqcat
// 	register_taxonomy( 'faqcat', [ 'faq' ], [
// 		'label'                 => 'Раздел вопроса', // определяется параметром $labels->name
// 		'labels'                => array(
// 			'name'              => 'Разделы вопросов',
// 			'singular_name'     => 'Раздел вопроса',
// 			'search_items'      => 'Искать Раздел вопроса',
// 			'all_items'         => 'Все Разделы вопросов',
// 			'parent_item'       => 'Родит. раздел вопроса',
// 			'parent_item_colon' => 'Родит. раздел вопроса:',
// 			'edit_item'         => 'Ред. Раздел вопроса',
// 			'update_item'       => 'Обновить Раздел вопроса',
// 			'add_new_item'      => 'Добавить Раздел вопроса',
// 			'new_item_name'     => 'Новый Раздел вопроса',
// 			'menu_name'         => 'Раздел вопроса',
// 		),
// 		'description'           => 'Рубрики для раздела вопросов', // описание таксономии
// 		'public'                => true,
// 		'show_in_nav_menus'     => false, // равен аргументу public
// 		'show_ui'               => true, // равен аргументу public
// 		'show_tagcloud'         => false, // равен аргументу show_ui
// 		'hierarchical'          => true,
// 		'rewrite'               => array('slug'=>'faq', 'hierarchical'=>false, 'with_front'=>false, 'feed'=>false ),
// 		'show_admin_column'     => true, // Позволить или нет авто-создание колонки таксономии в таблице ассоциированного типа записи. (с версии 3.5)
// 	] );

// 	// тип записи - вопросы - faq
// 	register_post_type( 'faq', [
// 		'label'               => 'Вопросы',
// 		'labels'              => array(
// 			'name'          => 'Вопросы',
// 			'singular_name' => 'Вопрос',
// 			'menu_name'     => 'Архив вопросов',
// 			'all_items'     => 'Все вопросы',
// 			'add_new'       => 'Добавить вопрос',
// 			'add_new_item'  => 'Добавить новый вопрос',
// 			'edit'          => 'Редактировать',
// 			'edit_item'     => 'Редактировать вопрос',
// 			'new_item'      => 'Новый вопрос',
// 		),
// 		'description'         => '',
// 		'public'              => true,
// 		'publicly_queryable'  => true,
// 		'show_ui'             => true,
// 		'show_in_rest'        => false,
// 		'rest_base'           => '',
// 		'show_in_menu'        => true,
// 		'exclude_from_search' => false,
// 		'capability_type'     => 'post',
// 		'map_meta_cap'        => true,
// 		'hierarchical'        => false,
// 		'rewrite'             => array( 'slug'=>'faq/%faqcat%', 'with_front'=>false, 'pages'=>false, 'feeds'=>false, 'feed'=>false ),
// 		'has_archive'         => 'faq',
// 		'query_var'           => true,
// 		'supports'            => array( 'title', 'editor' ),
// 		'taxonomies'          => array( 'faqcat' ),
// 	] );

// }

// ## Отфильтруем ЧПУ произвольного типа
// // фильтр: apply_filters( 'post_type_link', $post_link, $post, $leavename, $sample );
// add_filter( 'post_type_link', 'faq_permalink', 1, 2 );
// function faq_permalink( $permalink, $post ){

// 	// выходим если это не наш тип записи: без холдера %faqcat%
// 	if( strpos( $permalink, '%faqcat%' ) === false )
// 		return $permalink;

// 	// Получаем элементы таксы
// 	$terms = get_the_terms( $post, 'faqcat' );
// 	// если есть элемент заменим холдер
// 	if( ! is_wp_error( $terms ) && !empty( $terms ) && is_object( $terms[0] ) )
// 		$term_slug = array_pop( $terms )->slug;
// 	// элемента нет, а должен быть...
// 	else
// 		$term_slug = 'no-faqcat';

// 	return str_replace( '%faqcat%', $term_slug, $permalink );
// }