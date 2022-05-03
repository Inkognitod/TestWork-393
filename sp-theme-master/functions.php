<?php
/*
 * sp functions and definitions
 */

if ( ! function_exists( 'sp_setup' ) ) :

	function sp_setup() {

		load_theme_textdomain( 'sp-theme', get_template_directory() . '/languages' );

		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );


		register_nav_menus( array(
			'menu-1' => esc_html__( 'Primary', 'sp-theme' ),
		) );

		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );


		add_theme_support( 'custom-background', apply_filters( 'sp_custom_background_args', array(
			'default-color' => 'ffffff',
			'default-image' => '',
		)));

		add_theme_support( 'customize-selective-refresh-widgets' );

		add_theme_support( 'custom-logo', array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		) );
	}
endif;

add_action( 'after_setup_theme', 'sp_setup' );


function sp_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'sp_content_width', 640 );
}
add_action( 'after_setup_theme', 'sp_content_width', 0 );

//Register widget area.
function sp_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'sp-theme' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'sp-theme' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	));
}
add_action( 'widgets_init', 'sp_widgets_init' );

//Enqueue scripts and styles.
function sp_scripts() {
	wp_enqueue_style('bootstrap', get_template_directory_uri().'/css/bootstrap.css');
	wp_enqueue_style('cms-style', get_stylesheet_uri() );
	wp_enqueue_style('mediascreen', get_template_directory_uri().'/css/mediascreen.css');
	wp_enqueue_style('swiper', get_template_directory_uri().'/css/swiper.css');
	wp_enqueue_style('animate', get_template_directory_uri().'/css/animate.css');
	wp_enqueue_style('fontawesome', get_template_directory_uri().'/css/fontawesome-all.css');

	wp_enqueue_script('jquery');
	wp_enqueue_script('swiper', get_template_directory_uri().'/js/swiper.js');
	wp_enqueue_script('wow', get_template_directory_uri().'/js/wow.js');
	wp_enqueue_script('lazyload', get_template_directory_uri().'/js/lazyload.js');
	wp_enqueue_script('script', get_template_directory_uri().'/js/script.js');

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
	if ( ! did_action( 'wp_enqueue_media' ) ) {
		wp_enqueue_media();
	}
 	wp_enqueue_script( 'myuploadscript', get_stylesheet_directory_uri() . '/js/myuploadscript.js', array( 'jquery' ) );
}
add_action( 'wp_enqueue_scripts', 'sp_scripts' );

//Include the TGM_Plugin_Activation class.
require get_template_directory() . '/inc/class-tgm-plugin-activation.php';

//Implement the Custom Header feature.
require get_template_directory() . '/inc/custom-header.php';

//Functions which enhance the theme by hooking into WordPress.
require get_template_directory() . '/inc/sp-class.php';

//Functions which enhance the theme by hooking into WordPress.
require get_template_directory() . '/inc/template-functions.php';

//Customizer additions.
require get_template_directory() . '/inc/customizer.php';



/* woo custom fields */
function woocommerce_support() {
    add_theme_support( 'woocommerce' );
}

add_action( 'after_setup_theme', 'woocommerce_support' );


//Работаем с картинкой
add_action( 'admin_enqueue_scripts', 'misha_include_js' );
function misha_include_js() {
	if ( ! did_action( 'wp_enqueue_media' ) ) {
		wp_enqueue_media();
	}
 	wp_enqueue_script( 'myuploadscript', get_stylesheet_directory_uri() . '/js/myuploadscript.js', array( 'jquery' ) );
}

//Создадим свои поля
add_action( 'woocommerce_product_options_general_product_data', 'add_fields_to_options_general_product_data' );
function add_fields_to_options_general_product_data() {
	woocommerce_wp_text_input(
		array(
			'id'          => 'my_product_img',
			'label'       => 'Картинка',
			'type'	  	  => 'hidden',
		)
	);
	$my_product_img_value = get_post_meta( get_the_ID(),'my_product_img', true);
	if( $my_product_img = wp_get_attachment_image_src( $my_product_img_value ) ) {
		echo '<p class="form-field my_product_img_field ">
			<label for="my_product_img"></label><a href="#" class="upload_image_button"><img src="' . $my_product_img[0] . '" /></a>
			  <a href="#" class="remove_image_button">Remove image</a>
			  <input type="hidden" name="my_product_img" value="' . $my_product_img_value . '"> </p>';
	} else {
		echo '<p class="form-field my_product_img_field ">
			<label for="my_product_img"></label><a href="#" class="upload_image_button">Upload image</a>
			  <a href="#" class="remove_image_button" style="display:none">Remove image</a>
			  <input type="hidden" name="my_product_img" value=""></p>';
	} 
	woocommerce_wp_text_input(
		array(
			'id'          => 'my_product_date',
			'label'       => 'Дата создания',
			'type'	  	  => 'date',
		)
	);
	woocommerce_wp_select(
		array(
			'id'          => 'my_product_type',
			'label'       => 'Тип продукта',
			'options' => array(
			  '' => 'Выберите...',
			  'rare' => 'rare',
			  'frequent' => 'frequent',
			  'unusual' => 'unusual'
			),
			'description' => 'Выбор типа продукта (rare, frequent, unusual)',
			'desc_tip'    => 'true',
		)
	);
	
	echo '<button type="reset" name="reset" class="button alt">Очистить поля</button>';
	echo '<button type="submit" name="save" id="publish" class="button alt">Сохранить данные</button>';
}
//Сохраним созданные поля
add_action( 'woocommerce_process_product_meta', 'save_my_product_fields' );
function save_my_product_fields( $post_id ) {
  $product = wc_get_product( $post_id );
  
  // дата
  $update_meta_data_value = isset( $_POST['my_product_date'] ) ? $_POST['my_product_date'] : '';
  $product->update_meta_data( 'my_product_date', $update_meta_data_value );
  // тип
  $custom_field_type_value = isset( $_POST['my_product_type'] ) ? $_POST['my_product_type'] : '';
  $product->update_meta_data( 'my_product_type', $custom_field_type_value );
  //картинка
  $custom_field_img_value = isset( $_POST['my_product_img'] ) ? $_POST['my_product_img'] : '';
  $product->update_meta_data( 'my_product_img', $custom_field_img_value );
  
  $product->save();
}

// Покажем созданные поля на фронте
add_action('woocommerce_before_add_to_cart_form', 'display_custom_meta_field_value', 25 );
add_action('woocommerce_after_shop_loop_item_title', 'display_custom_meta_field_value', 9 );
function display_custom_meta_field_value() {
    global $product;

    if( $my_product_date = $product->get_meta('my_product_date') )
        echo  '<p class="my_product_value"><span>' . __("Дата:", "woocommerce") . '</span> ' . $my_product_date . '</p>';

    if( $my_product_type = $product->get_meta('my_product_type') )
        echo  '<p class="my_product_value"><span>' . __("Тип:", "woocommerce") . '</span> ' . $my_product_type . '</p>';

}




/* Добавим форму создания поста на фронте */
function post_form_shortcode($atts, $content = null) {

ob_start();

echo '<form method="post" enctype="multipart/form-data" name="mainForm" action="" id="front_product_create">
			<div id="postTitleOuter">
				<label>Название продукта</label>
				<input type="text" name="productTitle" class="postTitle"/>
			</div>
			<div id="postContentOuter">
				<label>Цена</label>
				<input type="text" name="_regular_price" class="postTitle"/>
			</div>
			<div id="postContentOuter">
				<label>Дата создания</label>
				<input type="date" name="my_product_date" class="postTitle"/>
			</div>
			<div id="postContentOuter">
				<label>Тип продукта</label>
				<select name="my_product_type" id="type-select">
					<option value="">Выберите...</option>
					<option value="rare">rare</option>
					<option value="frequent">frequent</option>
					<option value="unusual">unusual</option>
				</select>
			</div>
			<div id="postContentOuter">
				<label>Картинка</label>
				<!--<input type="file" name="my_product_img" id="my_product_img" />-->
				<a href="#" class="upload_image_button">Upload image</a>
				<a href="#" class="remove_image_button" style="display:none">Remove image</a>
				<input type="hidden" name="my_product_img" value="">
				<!--<button type="button" onclick="clearText()">x</button>-->
			</div>
			<input type="submit" name="add_post" id="add_post" value="Добавить товар">
			<input type="reset"  value="Очистить ">
		</form>';

// Сброс запроса для предотвращения конфликтов
return ob_get_clean();
}
add_shortcode("post-form", "post_form_shortcode");


/* Сохраним данные формы */
if($_POST['add_post']){
	$post = array(
    'post_content' => '',
    'post_status' => "publish",
    'post_title' => $_POST['productTitle'],
    'post_parent' => '',
    'post_type' => "product",
	);

	//Create post
	$post_id = wp_insert_post( $post, $wp_error );
	if($post_id){
		$attach_id = get_post_meta($product->parent_id, "_thumbnail_id", true);
		add_post_meta($post_id, '_thumbnail_id', $attach_id);
	}

	update_post_meta( $post_id, 'simple', 'product_type');
		 
	update_post_meta( $post_id, '_price', $_POST['_regular_price'] );
	update_post_meta( $post_id, 'my_product_date', $_POST['my_product_date'] );
	
    $selected_type = $_POST['my_product_type'];
	update_post_meta( $post_id, 'my_product_type', $selected_type );

	
	// загружаем картинку
	update_post_meta( $post_id, 'my_product_img', $_POST['my_product_img']  );
	update_post_meta( $post_id, '_featured', $_POST['my_product_img']  );


	/* перезагрузим страницу после отправки формы */
	wp_redirect( site_url()."?page_id=2" );
      exit();
}



//Меняем картинку на свою

//Product loop
function custom_image_woo() {

    remove_action('woocommerce_before_shop_loop_item_title' , 'woocommerce_template_loop_product_thumbnail' , 10);
	add_action( 'woocommerce_before_shop_loop_item_title', 'fink_template_loop_product_thumbnail', 10 );

	function fink_template_loop_product_thumbnail() {
		global $product;
		$image_size = apply_filters( 'single_product_archive_thumbnail_size', $size );
		$product_id = $product->get_id();
		$my_product_img_value = get_post_meta( $product_id,'my_product_img', true);
		$my_product_img = wp_get_attachment_image_src( $my_product_img_value );

		if ( $my_product_img ) {
			$my_product_img = str_replace( ' ', '%20', $my_product_img );
			$image_webp = str_replace( ['.png', '.jpg', '.jpeg', '.gif'], '.webp', $my_product_img );
			echo '<img src="' . $my_product_img[0] . '">';
		}
	}
}
add_action( 'woocommerce_init', 'custom_image_woo');


// Single product page
add_filter('woocommerce_single_product_image_thumbnail_html', 'main_image_away', 10, 2);
function main_image_away($html, $attachment_id ) {
$my_product_img_value = get_post_meta( get_the_ID(),'my_product_img', true);
$my_product_img = wp_get_attachment_image_src( $my_product_img_value );
    global $post, $product;
    $featured_image = get_post_thumbnail_id( $post->ID );
    if ( $attachment_id == $featured_image )
        $html = '<img src="' . $my_product_img[0] . '">';
    return $html;
}


// Thumbs
add_filter( 'manage_edit-product_columns', 'remove_woo_columns' );
function remove_woo_columns( $cols ) {
	unset( $cols['thumb'] ); // e.g unset( $cols['product_cat'] );
	return $cols;
}

/*add_filter( 'manage_edit-product_columns', 'my_img_column', 20 );
function my_img_column( $columns_array ) {

	return array_slice( $columns_array, 0, 1, true )
	+ array( 'my_thumb' => 'Картинка' )
	+ array_slice( $columns_array, 1, NULL, true );
}

add_action( 'manage_posts_custom_column', 'populate_my_img' );
function populate_my_img( $column_name ) {

	if( $column_name  == 'my_thumb' ) {
		$x = get_post_meta( $post_id, 'my_product_img', true); // taxonomy name
		echo $x[0]->name;
	}
}*/

add_filter('manage_edit-product_columns', 'add_img_column');
add_filter('manage_posts_custom_column', 'manage_img_column', 10, 2);

/*function add_img_column($columns) {
    $columns['img'] = 'Картинка';
    return $columns;
}*/
function add_img_column( $columns_array ) {

	return array_slice( $columns_array, 0, 1, true )
	+ array( 'img' => 'Картинка' )
	+ array_slice( $columns_array, 1, NULL, true );
}

function manage_img_column($column_name, $post_id) {
    if( $column_name == 'img' ) {
$my_product_img_value = get_post_meta( get_the_ID(),'my_product_img', true);
$my_product_img = wp_get_attachment_image_src( $my_product_img_value );
//        echo get_post_meta( $post_id, 'my_product_img', true);
		echo '<img src="' . $my_product_img[0] . '">';
    }
    return $column_name;
}


