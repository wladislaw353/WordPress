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
<?php
    // $posts = get_posts([
    //     'numberposts' => 4,
    //     'category'    => 0,
    //     'orderby'     => 'date',
    //     'order'       => 'DESC',
    //     'include'     => [],
    //     'exclude'     => [],
    //     'meta_key'    => '',
    //     'meta_value'  => '',
    //     'post_type'   => 'portfolio',
    //     'suppress_filters' => true,
    // ]);
    
    // foreach( $posts as $post ) {
    //     setup_postdata($post);
    //     ?>
    //         <?php the_post_thumbnail($post->ID); ?>
    //         <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
    //         <p><?php the_excerpt(); ?></p>
    //         <p><?php the_date(); ?></p>
    //     <?php
    // }

    $query = new WP_Query([
        'posts_per_page' => '12',
        'paged' => get_query_var('paged') ? : 1,
        'post_type' => 'portfolio',
        'order'     => 'DESC',
        'orderby'   => 'date',
        'tax_query' => [
            [
                'taxonomy' => 'project-type',
                'field'    => 'slug',
                'terms'    => get_post_meta($post->ID, 'projects_type', true),
            ],
        ],
    ]);

    while(have_posts()) : the_post();?>

        <?php the_post_thumbnail(); ?>
        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        <p><?php the_excerpt(); ?></p>
        <p><?php the_date(); ?></p>

    <?php endwhile;
 
    $GLOBALS['wp_query']->max_num_pages = $query->max_num_pages;
    the_posts_pagination([
        'prev_text'    => __('<'),
        'next_text'    => __('>'),
    ]);
    
    wp_reset_postdata();
?>

<!-- БЛОГ -->
<?php
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
?>

<?php
	get_footer();