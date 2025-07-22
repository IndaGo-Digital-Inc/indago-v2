<?php
/**
 * Register the Services post type.
 */
function indago_digital_register_service_post_type() {
	$labels = [
		'name' => _x('Services', 'Post type general name', 'indagodigital'),
		'singular_name' => _x('Service', 'Post type singular name', 'indagodigital'),
		'menu_name' => _x('Services', 'Admin Menu text', 'indagodigital'),
		'name_admin_bar' => _x('Service', 'Add New on Toolbar', 'indagodigital'),
		'add_new' => __('Add New', 'indagodigital'),
		'add_new_item' => __('Add New Service', 'indagodigital'),
		'new_item' => __('New Service', 'indagodigital'),
		'edit_item' => __('Edit Service', 'indagodigital'),
		'view_item' => __('View Service', 'indagodigital'),
		'all_items' => __('All Services', 'indagodigital'),
	];

	$args = [
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'query_var' => true,
		'rewrite' => ['slug' => 'service'],
		'capability_type' => 'post',
		'has_archive' => true,
		'hierarchical' => false,
		'menu_position' => 6,
		'menu_icon' => 'dashicons-awards',
		'supports' => ['title', 'editor', 'excerpt', 'thumbnail'],
		'show_in_rest' => true,
		'rest_base' => 'services',
		'rest_controller_class' => 'WP_REST_Posts_Controller',
	];
	register_post_type('service', $args);
}
add_action('init', 'indago_digital_register_service_post_type');

/**
 * Register a custom taxonomy for Services (assignable to Projects).
 */
function indago_digital_register_service_taxonomy() {
	$labels = [
		'name' => _x('Services', 'taxonomy general name', 'indagodigital'),
		'singular_name' => _x('Service', 'taxonomy singular name', 'indagodigital'),
		'menu_name' => __('Services', 'indagodigital'),
	];

	$args = [
		'hierarchical' => false, // Like tags, not categories
		'labels' => $labels,
		'show_ui' => true,
		'show_admin_column' => true,
		'query_var' => true,
		'rewrite' => ['slug' => 'service'],
		'show_in_rest' => true,
		'rest_base' => 'services-tax',
		'rest_controller_class' => 'WP_REST_Terms_Controller',
	];

	register_taxonomy('service', ['project'], $args);
}
add_action('init', 'indago_digital_register_service_taxonomy');


// --- SYNC SERVICE TAXONOMY TERMS WITH SERVICE POSTS ---

/**
 * When a service term is created, create a matching Service post.
 */
add_action('created_service', function($term_id, $tt_id, $taxonomy) {
	if ($taxonomy !== 'service') return;
	$term = get_term($term_id, 'service');
	if (!$term || is_wp_error($term)) return;

	// Check if a Service post with this term already exists (by meta)
	$existing = get_posts([
		'post_type' => 'service',
		'meta_key' => '_service_term_id',
		'meta_value' => $term_id,
		'post_status' => 'any',
		'numberposts' => 1
	]);
	if ($existing) return; // Already exists

	// Create the Service post
	$post_id = wp_insert_post([
		'post_title' => $term->name,
		'post_type' => 'service',
		'post_status' => 'publish',
	]);
	if ($post_id && !is_wp_error($post_id)) {
		update_post_meta($post_id, '_service_term_id', $term_id);
	}
}, 10, 3);

/**
 * When a service term is deleted, delete the matching Service post.
 */
add_action('delete_service', function($term_id, $tt_id, $taxonomy) {
	if ($taxonomy !== 'service') return;
	// Find the Service post by meta
	$posts = get_posts([
		'post_type' => 'service',
		'meta_key' => '_service_term_id',
		'meta_value' => $term_id,
		'post_status' => 'any',
		'numberposts' => 1
	]);
	if ($posts) {
		wp_delete_post($posts[0]->ID, true);
	}
}, 10, 3);

/**
 * When a service term is updated, update the matching Service post title.
 * (Optional, but keeps names in sync if term is renamed)
 */
add_action('edited_service', function($term_id, $tt_id, $taxonomy) {
	if ($taxonomy !== 'service') return;
	$term = get_term($term_id, 'service');
	if (!$term || is_wp_error($term)) return;
	$posts = get_posts([
		'post_type' => 'service',
		'meta_key' => '_service_term_id',
		'meta_value' => $term_id,
		'post_status' => 'any',
		'numberposts' => 1
	]);
	if ($posts) {
		// Update the post title if it exists
		wp_update_post([
			'ID' => $posts[0]->ID,
			'post_title' => $term->name
		]);
	} else {
		// Create the Service post if it does not exist
		$post_id = wp_insert_post([
			'post_title' => $term->name,
			'post_type' => 'service',
			'post_status' => 'publish',
		]);
		if ($post_id && !is_wp_error($post_id)) {
			update_post_meta($post_id, '_service_term_id', $term_id);
		}
	}
}, 10, 3);

/**
 * (Optional) On plugin/theme activation, ensure all service terms have posts and remove orphaned posts.
 */
function indago_digital_sync_all_service_terms_and_posts() {
	// Create posts for any terms missing a post
	$terms = get_terms(['taxonomy' => 'service', 'hide_empty' => false]);
	foreach ($terms as $term) {
		$existing = get_posts([
			'post_type' => 'service',
			'meta_key' => '_service_term_id',
			'meta_value' => $term->term_id,
			'post_status' => 'any',
			'numberposts' => 1
		]);
		if (!$existing) {
			$post_id = wp_insert_post([
				'post_title' => $term->name,
				'post_type' => 'service',
				'post_status' => 'publish',
			]);
			if ($post_id && !is_wp_error($post_id)) {
				update_post_meta($post_id, '_service_term_id', $term->term_id);
			}
		}
	}
	// Remove posts with no matching term
	$posts = get_posts([
		'post_type' => 'service',
		'post_status' => 'any',
		'numberposts' => -1
	]);
	foreach ($posts as $post) {
		$term_id = get_post_meta($post->ID, '_service_term_id', true);
		if ($term_id && !get_term($term_id, 'service')) {
			wp_delete_post($post->ID, true);
		}
	}
}

// Run the sync automatically on theme activation
add_action('after_switch_theme', 'indago_digital_sync_all_service_terms_and_posts');


// Exit if accessed directly
defined('ABSPATH') || exit();

// --- VITE & THEME SETUP ---

define(
	'IS_VITE_DEVELOPMENT',
	file_exists(get_template_directory() . '/.vite/development')
);

function indago_digital_enqueue_vite_assets()
{
	if (IS_VITE_DEVELOPMENT) {
		function vite_head_module_hook()
		{
			echo '<script type="module" src="https://localhost:5173/@vite/client"></script>';
			echo '<script type="module" src="https://localhost:5173/src/main.ts"></script>';
		}
		add_action('wp_head', 'vite_head_module_hook');
	} //else {
	// 	$manifest_path = get_template_directory() . '/dist/.vite/manifest.json';
	// 	if (file_exists($manifest_path)) {
	// 		$manifest = json_decode(file_get_contents($manifest_path), true);
	// 		if (is_array($manifest)) {
	// 			$entry_file = $manifest['src/main.ts']['file'] ?? null;
	// 			if ($entry_file) {
	// 				wp_enqueue_script(
	// 					'indago-digital-main',
	// 					get_template_directory_uri() . '/dist/' . $entry_file,
	// 					[],
	// 					null,
	// 					true
	// 				);
	// 			}
	// 			$css_file = $manifest['src/main.ts']['css'][0] ?? null;
	// 			if ($css_file) {
	// 				wp_enqueue_style(
	// 					'indago-digital-main',
	// 					get_template_directory_uri() . '/dist/' . $css_file
	// 				);
	// 			}
	// 		}
	// 	}
	// }
}
add_action('wp_enqueue_scripts', 'indago_digital_enqueue_vite_assets');

function indago_digital_theme_setup()
{
	add_theme_support('post-thumbnails');
}
add_action('after_setup_theme', 'indago_digital_theme_setup');

// --- REMOVE DEFAULT WP MENUS & FEATURES ---

/**
 * Remove default Posts, Pages, and Comments menus from the admin sidebar.
 */
function indago_digital_remove_default_menus()
{
	remove_menu_page('edit.php'); // Posts
	remove_menu_page('edit.php?post_type=page'); // Pages
	remove_menu_page('edit-comments.php'); // Comments
}
add_action('admin_menu', 'indago_digital_remove_default_menus');

/**
 * Remove comments and "+ New" links from the top admin bar.
 */
function indago_digital_remove_admin_bar_items()
{
	global $wp_admin_bar;
	$wp_admin_bar->remove_node('comments');
	$wp_admin_bar->remove_node('new-post');
	$wp_admin_bar->remove_node('new-page');
}
add_action(
	'wp_before_admin_bar_render',
	'indago_digital_remove_admin_bar_items'
);

/**
 * Remove comment support from default post types.
 */
function indago_digital_remove_comment_support()
{
	remove_post_type_support('post', 'comments');
	remove_post_type_support('page', 'comments');
}
add_action('init', 'indago_digital_remove_comment_support', 100);

// --- CATCH-ALL ROUTE FOR VUE ROUTER ---

/**
 * Redirect all non-admin, non-API, non-file requests to the main index.php file
 * to let the Vue Router handle the routing.
 */
function indago_digital_handle_vue_routes()
{
	// Don't interfere with the admin area, API requests, or real files/directories.
	if (
		is_admin() ||
		strpos($_SERVER['REQUEST_URI'], '/wp-json/') !== false ||
		file_exists(ABSPATH . ltrim($_SERVER['REQUEST_URI'], '/'))
	) {
		return;
	}

	// If the request is not for the root, load the main index file.
	if ($_SERVER['REQUEST_URI'] !== '/') {
		global $wp_query;
		$wp_query->set_404();
		status_header(200);
		include get_template_directory() . '/index.php';
		exit();
	}
}
add_action('template_redirect', 'indago_digital_handle_vue_routes');

// --- PROJECTS POST TYPE & TAXONOMIES ---

function indago_digital_register_project_post_type()
{
	$labels = [
		'name' => _x('Projects', 'Post type general name', 'indagodigital'),
		'singular_name' => _x(
			'Project',
			'Post type singular name',
			'indagodigital'
		),
		'menu_name' => _x('Projects', 'Admin Menu text', 'indagodigital'),
		'name_admin_bar' => _x(
			'Project',
			'Add New on Toolbar',
			'indagodigital'
		),
		'add_new' => __('Add New', 'indagodigital'),
		'add_new_item' => __('Add New Project', 'indagodigital'),
		'new_item' => __('New Project', 'indagodigital'),
		'edit_item' => __('Edit Project', 'indagodigital'),
		'view_item' => __('View Project', 'indagodigital'),
		'all_items' => __('All Projects', 'indagodigital'),
	];

	$args = [
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'query_var' => true,
		'rewrite' => ['slug' => 'project'],
		'capability_type' => 'post',
		'has_archive' => true,
		'hierarchical' => false,
		'menu_position' => 5,
		'menu_icon' => 'dashicons-portfolio', // This sets the menu icon
		'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'page-attributes'],
		'show_in_rest' => true,
		'rest_base' => 'projects',
		'rest_controller_class' => 'WP_REST_Posts_Controller',
	];
	register_post_type('project', $args);
}
add_action('init', 'indago_digital_register_project_post_type');

function indago_digital_register_project_taxonomy()
{
	$labels = [
		'name' => _x(
			'Categories',
			'taxonomy general name',
			'indagodigital'
		),
		'singular_name' => _x(
			'Category',
			'taxonomy singular name',
			'indagodigital'
		),
		'menu_name' => __('Categories', 'indagodigital'),
	];

	$args = [
		'hierarchical' => true,
		'labels' => $labels,
		'show_ui' => true,
		'show_admin_column' => true,
		'query_var' => true,
		'rewrite' => ['slug' => 'category'],
		'show_in_rest' => true,
		'rest_base' => 'categories',
		'rest_controller_class' => 'WP_REST_Terms_Controller',
	];
	register_taxonomy('category', ['project'], $args);
}
add_action('init', 'indago_digital_register_project_taxonomy');

/**
 * Register a custom taxonomy for "Clients".
 */
function indago_digital_register_client_taxonomy()
{
	$labels = [
		'name' => _x('Clients', 'taxonomy general name', 'indagodigital'),
		'singular_name' => _x(
			'Client',
			'taxonomy singular name',
			'indagodigital'
		),
		'search_items' => __('Search Clients', 'indagodigital'),
		'all_items' => __('All Clients', 'indagodigital'),
		'edit_item' => __('Edit Client', 'indagodigital'),
		'update_item' => __('Update Client', 'indagodigital'),
		'add_new_item' => __('Add New Client', 'indagodigital'),
		'new_item_name' => __('New Client Name', 'indagodigital'),
		'menu_name' => __('Clients', 'indagodigital'),
	];

	$args = [
		'hierarchical' => false, // Like tags, not categories
		'labels' => $labels,
		'show_ui' => true,
		'show_admin_column' => true,
		'query_var' => true,
		'rewrite' => ['slug' => 'client'],
		'show_in_rest' => true,
		'rest_base' => 'clients',
		'rest_controller_class' => 'WP_REST_Terms_Controller',
	];

	register_taxonomy('client', ['project'], $args);
}
add_action('init', 'indago_digital_register_client_taxonomy');

/**
 * Register the Headlines post type.
 */
function indago_digital_register_headlines_post_type() {
	$labels = [
		'name' => _x('Headlines', 'Post type general name', 'indagodigital'),
		'singular_name' => _x('Headline', 'Post type singular name', 'indagodigital'),
		'menu_name' => _x('Headlines', 'Admin Menu text', 'indagodigital'),
		'name_admin_bar' => _x('Headline', 'Add New on Toolbar', 'indagodigital'),
		'add_new' => __('Add New', 'indagodigital'),
		'add_new_item' => __('Add New Headline', 'indagodigital'),
		'new_item' => __('New Headline', 'indagodigital'),
		'edit_item' => __('Edit Headline', 'indagodigital'),
		'view_item' => __('View Headline', 'indagodigital'),
		'all_items' => __('All Headlines', 'indagodigital'),
	];

	$args = [
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'query_var' => true,
		'rewrite' => ['slug' => 'headline'],
		'capability_type' => 'post',
		'has_archive' => false,
		'hierarchical' => false,
		'menu_position' => 7,
		'menu_icon' => 'dashicons-editor-alignleft',
		'supports' => ['title', 'page-attributes'], // Only title and menu order
		'show_in_rest' => true,
		'rest_base' => 'headlines',
		'rest_controller_class' => 'WP_REST_Posts_Controller',
	];
	register_post_type('headline', $args);
}
add_action('init', 'indago_digital_register_headlines_post_type');

/**
 * Register the "Review" Custom Post Type.
 */
function create_reviews_cpt() {

	$labels = [
		'name'                  => _x( 'Reviews', 'Post Type General Name', 'text_domain' ),
		'singular_name'         => _x( 'Review', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'             => __( 'Reviews', 'text_domain' ),
		'name_admin_bar'        => __( 'Review', 'text_domain' ),
		'archives'              => __( 'Review Archives', 'text_domain' ),
		'attributes'            => __( 'Review Attributes', 'text_domain' ),
		'parent_item_colon'     => __( 'Parent Review:', 'text_domain' ),
		'all_items'             => __( 'All Reviews', 'text_domain' ),
		'add_new_item'          => __( 'Add New Review', 'text_domain' ),
		'add_new'               => __( 'Add New', 'text_domain' ),
		'new_item'              => __( 'New Review', 'text_domain' ),
		'edit_item'             => __( 'Edit Review', 'text_domain' ),
		'update_item'           => __( 'Update Review', 'text_domain' ),
		'view_item'             => __( 'View Review', 'text_domain' ),
		'view_items'            => __( 'View Reviews', 'text_domain' ),
		'search_items'          => __( 'Search Review', 'text_domain' ),
		'not_found'             => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
		'insert_into_item'      => __( 'Insert into review', 'text_domain' ),
		'uploaded_to_this_item' => __( 'Uploaded to this review', 'text_domain' ),
		'items_list'            => __( 'Reviews list', 'text_domain' ),
		'items_list_navigation' => __( 'Reviews list navigation', 'text_domain' ),
		'filter_items_list'     => __( 'Filter reviews list', 'text_domain' ),
	];
	$args = [
		'label'                 => __( 'Review', 'text_domain' ),
		'description'           => __( 'Customer reviews and testimonials', 'text_domain' ),
		'labels'                => $labels,
		'supports'              => [ 'title', 'editor', 'page-attributes' ], // Enables Title, Body Content, and menu_order
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-star-filled',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'post',
		'show_in_rest'          => true,
		'rest_base'             => 'reviews', // Explicitly set the API endpoint slug
		'rewrite'               => ['slug' => 'reviews'],
	];
	register_post_type( 'review', $args );

}
add_action( 'init', 'create_reviews_cpt', 0 );


/**
 * Register custom fields to the 'review' post type REST API response.
 */
function add_review_custom_fields_to_rest() {
	// Add Reviewer Name
	register_rest_field(
		'review', // Post type
		'reviewer_name', // Name of the field in the REST API response
		[
			'get_callback'    => function( $post_arr ) {
				return get_post_meta( $post_arr['id'], 'reviewer_name', true );
			},
			'update_callback' => function( $value, $post ) {
				if ( ! is_string( $value ) ) {
					return new WP_Error( 'rest_invalid_param', esc_html__( 'Reviewer name must be a string.', 'text_domain' ), [ 'status' => 400 ] );
				}
				return update_post_meta( $post->ID, 'reviewer_name', sanitize_text_field( $value ) );
			},
			'schema'          => [
				'description' => __( 'Name of the person who gave the review.', 'text_domain' ),
				'type'        => 'string',
				'context'     => [ 'view', 'edit' ],
			],
		]
	);

	// Add Reviewer Title
	register_rest_field(
		'review', // Post type
		'reviewer_title', // Name of the field in the REST API response
		[
			'get_callback'    => function( $post_arr ) {
				return get_post_meta( $post_arr['id'], 'reviewer_title', true );
			},
			'update_callback' => function( $value, $post ) {
				if ( ! is_string( $value ) ) {
					return new WP_Error( 'rest_invalid_param', esc_html__( 'Reviewer title must be a string.', 'text_domain' ), [ 'status' => 400 ] );
				}
				return update_post_meta( $post->ID, 'reviewer_title', sanitize_text_field( $value ) );
			},
			'schema'          => [
				'description' => __( 'Title or position of the reviewer.', 'text_domain' ),
				'type'        => 'string',
				'context'     => [ 'view', 'edit' ],
			],
		]
	);
}
add_action( 'rest_api_init', 'add_review_custom_fields_to_rest' );


/**
 * Add the meta box for custom fields to the 'review' post type editor.
 */
function add_review_meta_box() {
	add_meta_box(
		'review_details_meta_box',          // ID of the meta box
		'Reviewer Details',                 // Title of the meta box
		'render_review_meta_box_html',      // Callback function to render the HTML
		'review',                           // The screen or post type to show the box on
		'normal',                           // Context (where on the screen)
		'high'                              // Priority
	);
}
add_action( 'add_meta_boxes', 'add_review_meta_box' );


/**
 * Render the HTML for the custom fields meta box.
 *
 * @param WP_Post $post The post object.
 */
function render_review_meta_box_html( $post ) {
	// Add a nonce field for security
	wp_nonce_field( 'review_details_nonce_action', 'review_details_nonce' );

	// Get existing values
	$reviewer_name = get_post_meta( $post->ID, 'reviewer_name', true );
	$reviewer_title = get_post_meta( $post->ID, 'reviewer_title', true );

	// Render the fields
	?>
	<style>
		.review-meta-field { margin-bottom: 15px; }
		.review-meta-field label { display: block; font-weight: bold; margin-bottom: 5px; }
		.review-meta-field input { width: 100%; padding: 8px; }
	</style>

	<div class="review-meta-field">
		<label for="reviewer_name_field">Reviewer Name</label>
		<input type="text" id="reviewer_name_field" name="reviewer_name_field" value="<?php echo esc_attr( $reviewer_name ); ?>" class="widefat">
	</div>

	<div class="review-meta-field">
		<label for="reviewer_title_field">Reviewer Title/Position</label>
		<input type="text" id="reviewer_title_field" name="reviewer_title_field" value="<?php echo esc_attr( $reviewer_title ); ?>" class="widefat">
	</div>
	<?php
}


/**
 * Save the custom meta box data when a post is saved.
 *
 * @param int $post_id The ID of the post being saved.
 */
function save_review_meta_box_data( $post_id ) {
	// Check if our nonce is set.
	if ( ! isset( $_POST['review_details_nonce'] ) ) {
		return;
	}

	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $_POST['review_details_nonce'], 'review_details_nonce_action' ) ) {
		return;
	}

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check the user's permissions.
	if ( isset( $_POST['post_type'] ) && 'review' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}

	// Sanitize and save the Reviewer Name
	if ( isset( $_POST['reviewer_name_field'] ) ) {
		$name_data = sanitize_text_field( $_POST['reviewer_name_field'] );
		update_post_meta( $post_id, 'reviewer_name', $name_data );
	}

	// Sanitize and save the Reviewer Title
	if ( isset( $_POST['reviewer_title_field'] ) ) {
		$title_data = sanitize_text_field( $_POST['reviewer_title_field'] );
		update_post_meta( $post_id, 'reviewer_title', $title_data );
	}
}
add_action( 'save_post', 'save_review_meta_box_data' );