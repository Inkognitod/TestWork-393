<?php
/*
 * Template Name: create product
 */
get_header(); 
$sp_obj = new SpClass();?>

<div class="page-content">
	<div class="container">
	<h1><?php $sp_obj->get_title();?></h1>

			<div <?php post_class('one-post');?>>
				
				<?php echo do_shortcode('[post-form]') ?>
				
			</div>	
	</div>
</div>

<?php get_footer(); ?>