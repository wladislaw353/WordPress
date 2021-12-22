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
    //posts_nav_link();
    wp_reset_query();
?>

<?php the_posts_pagination([
    'prev_text'    => __('<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M10.6866 2.97976C10.8641 3.15727 10.8802 3.43505 10.735 3.63079L10.6866 3.68687L6.37367 7.99998L10.6866 12.3131C10.8641 12.4906 10.8802 12.7684 10.735 12.9641L10.6866 13.0202C10.5091 13.1977 10.2313 13.2138 10.0355 13.0686L9.97945 13.0202L5.31279 8.35353C5.13528 8.17602 5.11914 7.89825 5.26438 7.70251L5.31279 7.64643L9.97945 2.97976C10.1747 2.7845 10.4913 2.7845 10.6866 2.97976Z" fill="black" /> </svg>'),
    'next_text'    => __('<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M5.31344 2.97976C5.13593 3.15727 5.11979 3.43505 5.26503 3.63079L5.31344 3.68687L9.62633 7.99998L5.31344 12.3131C5.13593 12.4906 5.11979 12.7684 5.26503 12.9641L5.31344 13.0202C5.49095 13.1977 5.76873 13.2138 5.96447 13.0686L6.02055 13.0202L10.6872 8.35353C10.8647 8.17602 10.8809 7.89825 10.7356 7.70251L10.6872 7.64643L6.02055 2.97976C5.82528 2.7845 5.5087 2.7845 5.31344 2.97976Z" fill="black" /> </svg>'),
])?>

<?php
	get_footer();