<?php
	get_header();
?>

<!-- 
<?php the_post_thumbnail(); ?>
	<?php the_post_thumbnail_url() ?>

	<?=get_bloginfo('template_url');?>
	<?=home_url();?>
	<?=mlt('', '')?> 
-->

<section class="seo-text holder">

	<?=wonderweb_breadcrumbs();?>

	<h1><?php the_title(); ?></h1>
	<?php the_content(); ?>
	
</section>


<?php
	get_footer();
