<?php
/*
	Template Name: Главная
*/
	get_header();
?>
<!-- 
	<?php the_content(); ?>
	<?=get_bloginfo('template_url');?>
	<?=home_url();?>
	<?=mlt('', '')?> 
-->

<!-- ПОРТФОЛИО -->
<!-- <?php
    $posts = get_posts( array(
        'numberposts' => 4,
        'category'    => 0,
        'orderby'     => 'date',
        'order'       => 'DESC',
        'include'     => array(),
        'exclude'     => array(),
        'meta_key'    => '',
        'meta_value'  =>'',
        'post_type'   => 'portfolio',
        'suppress_filters' => true,
    ) );
    
    foreach( $posts as $post ) {
        setup_postdata($post);
        ?>
            <?php echo get_the_post_thumbnail($post->ID); ?>
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            <p><?php the_excerpt(); ?></p>
            <p><?php the_date(); ?></p>
        <?php
    }
 
    wp_reset_postdata();
?> -->

<!-- БЛОГ -->
<!-- <?php
    global $wp_query;
    $wp_query = new WP_Query([
        'category_name' => 'blog',
        'posts_per_page' => '4',
        'paged' => get_query_var('paged') ?: 1
    ]);
    while(have_posts()) : the_post();?>
        <?php the_post_thumbnail(); ?>
        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        <p><?php the_excerpt(); ?></p>
        <p><?php the_date(); ?></p>
    <?php endwhile;
    wp_reset_query();
?> -->

<?php
	get_footer();