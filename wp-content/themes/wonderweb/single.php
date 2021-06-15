<?php
	get_header();
?>

<!-- 
	<?php the_post_thumbnail(); ?>
	<?php the_post_thumbnail_url() ?>

	<?=get_bloginfo('template_url');?>
	<?=home_url();?>
	<?=mlt('', '')?>
	<span id="fb-shareq" data-href="<?='https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];?>">Share in fb</span>
-->

<section class="seo-text indent">
	<div class="holder">
		<?php the_post_thumbnail() ?>
		<p><?=get_the_date(); ?></p>
		<h1><?php the_title(); ?></h1>
		<?php the_content(); ?>
	</div>
</section>


<?php
	while ( have_posts() ) :
		the_post();

		the_post_navigation(
			array(
				'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'Previous:', 'wonderweb-custom-theme' ) . '</span> <span class="nav-title">%title</span>',
				'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Next:', 'wonderweb-custom-theme' ) . '</span> <span class="nav-title">%title</span>',
			)
		);

	endwhile;
?>

<?php
	//get_sidebar();
	get_footer();
