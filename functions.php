<?php

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
			echo '<script type="module" src="https://localhost:5173/src/main.js"></script>';
		}
		add_action('wp_head', 'vite_head_module_hook');
	} else {
		$manifest_path = get_template_directory() . '/dist/.vite/manifest.json';
		if (file_exists($manifest_path)) {
			$manifest = json_decode(file_get_contents($manifest_path), true);
			if (is_array($manifest)) {
				$entry_file = $manifest['src/main.js']['file'] ?? null;
				if ($entry_file) {
					wp_enqueue_script(
						'indago-digital-main',
						get_template_directory_uri() . '/dist/' . $entry_file,
						[],
						null,
						true
					);
				}
				$css_file = $manifest['src/main.js']['css'][0] ?? null;
				if ($css_file) {
					wp_enqueue_style(
						'indago-digital-main',
						get_template_directory_uri() . '/dist/' . $css_file
					);
				}
			}
		}
	}
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
		'supports' => ['title', 'editor', 'excerpt', 'thumbnail'],
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
			'Project Categories',
			'taxonomy general name',
			'indagodigital'
		),
		'singular_name' => _x(
			'Project Category',
			'taxonomy singular name',
			'indagodigital'
		),
		'menu_name' => __('Project Categories', 'indagodigital'),
	];

	$args = [
		'hierarchical' => true,
		'labels' => $labels,
		'show_ui' => true,
		'show_admin_column' => true,
		'query_var' => true,
		'rewrite' => ['slug' => 'project-category'],
		'show_in_rest' => true,
		'rest_base' => 'project_categories',
		'rest_controller_class' => 'WP_REST_Terms_Controller',
	];
	register_taxonomy('project_category', ['project'], $args);
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
