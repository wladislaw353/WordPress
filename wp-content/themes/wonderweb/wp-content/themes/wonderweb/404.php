<?php
	get_header();
?>

	<section class="page-404">
		<div class="holder">
			<p><?=mlt('Запрашиваемая страница не найдена', 'Запитувана сторінка не знайдена')?></p>
			<br>
			<a class="btn" href="<?=home_url();?>/"><?=mlt('Перейти на главную', 'Перейти на головну')?></a>
		</div>
	</section>

<?php
	get_footer();
