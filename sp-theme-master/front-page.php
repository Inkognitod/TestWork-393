<?php
/*
 * The main template file
 */

get_header( 'shop' );
$sp_obj = new SpClass();
do_action( 'woocommerce_before_main_content' );
?>

<div class="page-content">
	<div class="container">
<h1>Все товары на главной</h1>

			
			<?php echo do_shortcode('[products per_page="4" orderby="date" order="desc"]'); ?>


	</div>
</div>


	<?php get_footer(); ?>