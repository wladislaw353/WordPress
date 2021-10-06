
<?php if(stripos($_SERVER['REQUEST_URI'], '/author/') === false) {} else header('Location: /'); ?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
	<?php wp_head(); ?>
	<link rel="icon" type="image/vnd.microsoft.icon" href="<?=get_bloginfo('template_url');?>/img/favicon.png">
	<?php if(isset($_GET['dev'])) echo '<meta http-equiv="Cache-Control" content="no-cache">' ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php 
	wp_nav_menu([
		'theme_location'  => '',
		'menu'            => 'Меню в шапке', 
		'container'       => false, 
		'container_class' => '', 
		'container_id'    => '',
		'menu_class'      => '', 
		'menu_id'         => '',
		'echo'            => true,
		'fallback_cb'     => 'wp_page_menu',
		'before'          => '',
		'after'           => '',
		'link_before'     => '',
		'link_after'      => '',
		'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
		'depth'           => 0,
		'walker'          => '',
	]); 
?>

<!-- 
	<?=get_bloginfo('template_url');?>
	<?=home_url();?>
	<?=mlt('', '')?> 

    <?=get_field('phone', 'option') ?>
    <?=get_field('phone2', 'option') ?>
    <?=get_field('phone3', 'option') ?>
    <?=get_field('email', 'option') ?>
    https://www.facebook.com/<?=get_field('fb', 'option') ?>
    <?=get_field('instagram', 'option') ?>
    <?=get_field('linkedin', 'option') ?>
    <?=get_field('youtube', 'option') ?>
-->

<?php if (get_bloginfo('language')=='ru-ua'): ?>
	<a href="/<?=str_replace('/ru/', '', $_SERVER['REQUEST_URI']);?>">UA</a>
	<!-- <a href="/en/<?=str_replace('/ru/', '', $_SERVER['REQUEST_URI']);?>">EN</a> -->
	<a class="active">RU</a>
<?php elseif (get_bloginfo('language')=='uk-ua'): ?>
	<a class="active">UA</a>
	<!-- <a href="/en<?=$_SERVER['REQUEST_URI'];?>">EN</a> -->
	<a href="/ru<?=$_SERVER['REQUEST_URI'];?>">RU</a>
<?php else: ?>
	<a href="/<?=str_replace('/en/', '', $_SERVER['REQUEST_URI']);?>">UA</a>
	<a class="active">EN</a>
	<a href="/ru/<?=str_replace('/en/', '', $_SERVER['REQUEST_URI']);?>">RU</a>
<?php endif; ?>