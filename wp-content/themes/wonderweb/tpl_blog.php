<?php
/*
	Template Name: Блог
*/
    get_header();
?>

<!-- 
    <?php the_post_thumbnail(); ?>
	<?php the_post_thumbnail_url() ?>
	<?php the_title(); ?>
	<?php the_content(); ?>
	<?=wonderweb_breadcrumbs();?>
	<?=get_bloginfo('template_url');?>
	<?=home_url();?>
	<?=mlt('', '')?> 
-->


<?php
    global $wp_query;
    $wp_query = new WP_Query(array(
        'category_name' => 'blog',
        'posts_per_page' => '24',
        'paged' => get_query_var('paged') ? : 1
    ));

    while(have_posts()) : the_post();?>

        <?php the_post_thumbnail(); ?>
        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        <p><?php the_excerpt(); ?></p>
        <p><?php the_date(); ?></p>

    <?php endwhile;
    posts_nav_link();
    wp_reset_query();
?>

<?php
	get_footer();