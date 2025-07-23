<?php
/**
 * IndaGo Digital Theme Functions
 *
 * @package indagodigital
 */

// Exit if accessed directly to prevent security vulnerabilities.
defined('ABSPATH') || exit();


// =========================================================================
// POST TYPES & TAXONOMIES REGISTRATION
// =========================================================================

/**
 * Register all custom post types and taxonomies on the 'init' hook.
 */
function indago_digital_register_types() {

    // --- SERVICE POST TYPE ---
    $service_labels = [
        'name' => _x('Services', 'Post type general name', 'indagodigital'),
        'singular_name' => _x('Service', 'Post type singular name', 'indagodigital'),
        'menu_name' => _x('Services', 'Admin Menu text', 'indagodigital'),
        'add_new' => __('Add New Service', 'indagodigital'),
        'add_new_item' => __('Add New Service', 'indagodigital'),
        'edit_item' => __('Edit Service', 'indagodigital'),
        'new_item' => __('New Service', 'indagodigital'),
        'view_item' => __('View Service', 'indagodigital'),
        'search_items' => __('Search Services', 'indagodigital'),
        'not_found' => __('No services found', 'indagodigital'),
        'not_found_in_trash' => __('No services found in Trash', 'indagodigital'),
        'parent_item_colon' => __('Parent Service:', 'indagodigital'),
        'all_items' => __('All Services', 'indagodigital'),
        'archives' => __('Service Archives', 'indagodigital'),
        'insert_into_item' => __('Insert into service', 'indagodigital'),
        'uploaded_to_this_item' => __('Uploaded to this service', 'indagodigital'),
        'filter_items_list' => __('Filter services list', 'indagodigital'),
        'items_list_navigation' => __('Services list navigation', 'indagodigital'),
        'items_list' => __('Services list', 'indagodigital'),
    ];
    $service_args = [
        'labels' => $service_labels,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 6,
        'menu_icon' => 'dashicons-laptop',
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'page-attributes'],
        'has_archive' => true,
        'rewrite' => ['slug' => 'service'],
        'show_in_rest' => true,
        'rest_base' => 'services',
    ];
    register_post_type('service', $service_args);


    // --- PROJECT POST TYPE ---
    $project_labels = [
        'name' => _x('Projects', 'Post type general name', 'indagodigital'),
        'singular_name' => _x('Project', 'Post type singular name', 'indagodigital'),
        'menu_name' => _x('Projects', 'Admin Menu text', 'indagodigital'),
        'add_new' => __('Add New Project', 'indagodigital'),
        'add_new_item' => __('Add New Project', 'indagodigital'),
        'edit_item' => __('Edit Project', 'indagodigital'),
        'new_item' => __('New Project', 'indagodigital'),
        'view_item' => __('View Project', 'indagodigital'),
        'search_items' => __('Search Projects', 'indagodigital'),
        'not_found' => __('No projects found', 'indagodigital'),
        'not_found_in_trash' => __('No projects found in Trash', 'indagodigital'),
        'parent_item_colon' => __('Parent Project:', 'indagodigital'),
        'all_items' => __('All Projects', 'indagodigital'),
        'archives' => __('Project Archives', 'indagodigital'),
        'insert_into_item' => __('Insert into project', 'indagodigital'),
        'uploaded_to_this_item' => __('Uploaded to this project', 'indagodigital'),
        'filter_items_list' => __('Filter projects list', 'indagodigital'),
        'items_list_navigation' => __('Projects list navigation', 'indagodigital'),
        'items_list' => __('Projects list', 'indagodigital'),
    ];
    $project_args = [
        'labels' => $project_labels,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-portfolio',
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'page-attributes'],
        'has_archive' => true,
        'rewrite' => ['slug' => 'project'],
        'show_in_rest' => true,
        'rest_base' => 'projects',
    ];
    register_post_type('project', $project_args);


    // --- REVIEW POST TYPE ---
    $review_labels = [
        'name' => _x('Reviews', 'Post Type General Name', 'indagodigital'),
        'singular_name' => _x('Review', 'Post Type Singular Name', 'indagodigital'),
        'menu_name' => __('Reviews', 'indagodigital'),
        'add_new' => __('Add New Review', 'indagodigital'),
        'add_new_item' => __('Add New Review', 'indagodigital'),
        'edit_item' => __('Edit Review', 'indagodigital'),
        'new_item' => __('New Review', 'indagodigital'),
        'view_item' => __('View Review', 'indagodigital'),
        'search_items' => __('Search Reviews', 'indagodigital'),
        'not_found' => __('No reviews found', 'indagodigital'),
        'not_found_in_trash' => __('No reviews found in Trash', 'indagodigital'),
        'parent_item_colon' => __('Parent Review:', 'indagodigital'),
        'all_items' => __('All Reviews', 'indagodigital'),
        'archives' => __('Review Archives', 'indagodigital'),
        'insert_into_item' => __('Insert into review', 'indagodigital'),
        'uploaded_to_this_item' => __('Uploaded to this review', 'indagodigital'),
        'filter_items_list' => __('Filter reviews list', 'indagodigital'),
        'items_list_navigation' => __('Reviews list navigation', 'indagodigital'),
        'items_list' => __('Reviews list', 'indagodigital'),
    ];
    $review_args = [
        'label' => __('Review', 'indagodigital'),
        'description' => __('Customer reviews and testimonials', 'indagodigital'),
        'labels' => $review_labels,
        'supports' => ['title', 'editor', 'page-attributes'],
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-awards',
        'has_archive' => true,
        'show_in_rest' => true,
        'rest_base' => 'reviews',
        'rewrite' => ['slug' => 'reviews'],
    ];
    register_post_type('review', $review_args);


    // --- HEADLINE POST TYPE ---
    $headline_labels = [
        'name' => _x('Headlines', 'Post type general name', 'indagodigital'),
        'singular_name' => _x('Headline', 'Post type singular name', 'indagodigital'),
        'menu_name' => _x('Headlines', 'Admin Menu text', 'indagodigital'),
        'add_new' => __('Add New Headline', 'indagodigital'),
        'add_new_item' => __('Add New Headline', 'indagodigital'),
        'edit_item' => __('Edit Headline', 'indagodigital'),
        'new_item' => __('New Headline', 'indagodigital'),
        'view_item' => __('View Headline', 'indagodigital'),
        'search_items' => __('Search Headlines', 'indagodigital'),
        'not_found' => __('No headlines found', 'indagodigital'),
        'not_found_in_trash' => __('No headlines found in Trash', 'indagodigital'),
        'parent_item_colon' => __('Parent Headline:', 'indagodigital'),
        'all_items' => __('All Headlines', 'indagodigital'),
        'archives' => __('Headline Archives', 'indagodigital'),
        'insert_into_item' => __('Insert into headline', 'indagodigital'),
        'uploaded_to_this_item' => __('Uploaded to this headline', 'indagodigital'),
        'filter_items_list' => __('Filter headlines list', 'indagodigital'),
        'items_list_navigation' => __('Headlines list navigation', 'indagodigital'),
        'items_list' => __('Headlines list', 'indagodigital'),
    ];
    $headline_args = [
        'labels' => $headline_labels,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 7,
        'menu_icon' => 'dashicons-editor-alignleft',
        'supports' => ['title', 'page-attributes'],
        'has_archive' => false,
        'show_in_rest' => true,
        'rest_base' => 'headlines',
    ];
    register_post_type('headline', $headline_args);


    // --- PROJECT SERVICE TAXONOMY (for Projects) ---
    $service_tax_labels = [
        'name' => _x('Services', 'taxonomy general name', 'indagodigital'),
        'singular_name' => _x('Service', 'taxonomy singular name', 'indagodigital'),
        'menu_name' => __('Services', 'indagodigital'),
        'all_items' => __('All Services', 'indagodigital'),
        'edit_item' => __('Edit Service', 'indagodigital'),
        'view_item' => __('View Service', 'indagodigital'),
        'update_item' => __('Update Service', 'indagodigital'),
        'add_new_item' => __('Add New Service', 'indagodigital'),
        'new_item_name' => __('New Service Name', 'indagodigital'),
        'separate_items_with_commas' => __('Separate services with commas', 'indagodigital'),
        'add_or_remove_items' => __('Add or remove services', 'indagodigital'),
        'choose_from_most_used' => __('Choose from the most used services', 'indagodigital'),
        'not_found' => __('No services found.', 'indagodigital'),
        'back_to_items' => __('Back to services', 'indagodigital'),
    ];
    $service_tax_args = [
        'hierarchical' => false,
        'labels' => $service_tax_labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => ['slug' => 'project-service'],
        'show_in_rest' => true,
        'rest_base' => 'project-services',
    ];
    register_taxonomy('project-service', ['project'], $service_tax_args);


    // --- PROJECT CATEGORY TAXONOMY ---
    $proj_cat_labels = [
        'name' => _x('Categories', 'taxonomy general name', 'indagodigital'),
        'singular_name' => _x('Category', 'taxonomy singular name', 'indagodigital'),
        'menu_name' => __('Categories', 'indagodigital'),
        'all_items' => __('All Categories', 'indagodigital'),
        'edit_item' => __('Edit Category', 'indagodigital'),
        'view_item' => __('View Category', 'indagodigital'),
        'update_item' => __('Update Category', 'indagodigital'),
        'add_new_item' => __('Add New Category', 'indagodigital'),
        'new_item_name' => __('New Category Name', 'indagodigital'),
        'parent_item' => __('Parent Category', 'indagodigital'),
        'parent_item_colon' => __('Parent Category:', 'indagodigital'),
        'search_items' => __('Search Categories', 'indagodigital'),
        'not_found' => __('No categories found.', 'indagodigital'),
        'back_to_items' => __('Back to categories', 'indagodigital'),
    ];
    $proj_cat_args = [
        'hierarchical' => true,
        'labels' => $proj_cat_labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'rewrite' => ['slug' => 'category'],
        'show_in_rest' => true,
        'rest_base' => 'categories',
    ];
    register_taxonomy('category', ['project'], $proj_cat_args);


    // --- CLIENT TAXONOMY (for Projects and Reviews) ---
    $client_labels = [
        'name' => _x('Clients', 'taxonomy general name', 'indagodigital'),
        'singular_name' => _x('Client', 'taxonomy singular name', 'indagodigital'),
        'menu_name' => __('Clients', 'indagodigital'),
        'all_items' => __('All Clients', 'indagodigital'),
        'edit_item' => __('Edit Client', 'indagodigital'),
        'view_item' => __('View Client', 'indagodigital'),
        'update_item' => __('Update Client', 'indagodigital'),
        'add_new_item' => __('Add New Client', 'indagodigital'),
        'new_item_name' => __('New Client Name', 'indagodigital'),
        'separate_items_with_commas' => __('Separate clients with commas', 'indagodigital'),
        'add_or_remove_items' => __('Add or remove clients', 'indagodigital'),
        'choose_from_most_used' => __('Choose from the most used clients', 'indagodigital'),
        'not_found' => __('No clients found.', 'indagodigital'),
        'back_to_items' => __('Back to clients', 'indagodigital'),
    ];
    $client_args = [
        'hierarchical' => false,
        'labels' => $client_labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'rewrite' => ['slug' => 'client'],
        'show_in_rest' => true,
        'rest_base' => 'clients',
    ];
    register_taxonomy('client', ['project', 'review'], $client_args);
}
add_action('init', 'indago_digital_register_types');


// =========================================================================
// CUSTOM FIELDS FOR 'PROJECT' POST TYPE
// =========================================================================

/**
 * Add custom fields to the REST API response for the 'project' post type.
 */
function add_project_custom_fields_to_rest() {
    register_rest_field('project', 'project_url', [
        'get_callback' => fn($post) => get_post_meta($post['id'], 'project_url', true),
        'update_callback' => fn($value, $post) => update_post_meta($post->ID, 'project_url', esc_url_raw($value)),
        'schema' => ['type' => 'string', 'description' => 'URL of the project.', 'format' => 'uri', 'context' => ['view', 'edit']],
    ]);
    // NEW: Register REST field for Mobile Project Image
    register_rest_field('project', 'mobile_project_image', [
        'get_callback' => function($post_arr) {
            $image_id = (int) get_post_meta($post_arr['id'], '_mobile_project_image_id', true);
            if ($image_id) {
                return [
                    'id' => $image_id,
                    'url' => wp_get_attachment_image_url($image_id, 'medium_large'), // Choose appropriate size
                    'alt' => get_post_meta($image_id, '_wp_attachment_image_alt', true) ?: '',
                ];
            }
            return null; // Return null if no image is set
        },
        'schema' => [
            'description' => __( 'Mobile version of the project image.', 'indagodigital' ),
            'type'        => ['object', 'null'],
            'properties'  => [
                'id'  => ['type' => 'integer'],
                'url' => ['type' => 'string', 'format' => 'uri'],
                'alt' => ['type' => 'string'],
            ],
            'context'     => [ 'view', 'edit' ],
        ],
    ]);
    // NEW: Register REST field for Desktop Project Image
    register_rest_field('project', 'desktop_project_image', [
        'get_callback' => function($post_arr) {
            $image_id = (int) get_post_meta($post_arr['id'], '_desktop_project_image_id', true);
            if ($image_id) {
                return [
                    'id' => $image_id,
                    'url' => wp_get_attachment_image_url($image_id, 'large'), // Choose appropriate size
                    'alt' => get_post_meta($image_id, '_wp_attachment_image_alt', true) ?: '',
                ];
            }
            return null; // Return null if no image is set
        },
        'schema' => [
            'description' => __( 'Desktop version of the project image.', 'indagodigital' ),
            'type'        => ['object', 'null'],
            'properties'  => [
                'id'  => ['type' => 'integer'],
                'url' => ['type' => 'string', 'format' => 'uri'],
                'alt' => ['type' => 'string'],
            ],
            'context'     => [ 'view', 'edit' ],
        ],
    ]);
}
add_action('rest_api_init', 'add_project_custom_fields_to_rest');

/**
 * Add the meta box for project details to the 'project' post editor.
 */
function add_project_meta_box() {
    add_meta_box('project_details_meta_box', 'Project Details', 'render_project_meta_box_html', 'project', 'normal', 'high');
    // NEW: Add meta box for Mobile/Desktop Project Images
    add_meta_box('project_responsive_images_meta_box', 'Responsive Project Images', 'render_responsive_project_images_meta_box_html', 'project', 'normal', 'high');
}
add_action('add_meta_boxes', 'add_project_meta_box');

/**
 * Render the HTML for the custom fields meta box for Projects.
 */
function render_project_meta_box_html($post) {
    wp_nonce_field('project_details_nonce_action', 'project_details_nonce');
    $project_url = get_post_meta($post->ID, 'project_url', true);
    ?>
    <style>
        .project-field { padding: 6px 0; }
        .project-field label { font-weight: bold; display: block; margin-bottom: 4px; }
        .project-field input[type="text"],
        .project-field input[type="url"] {
            width: 100%;
            padding: 8px;
            background: #f0f0f1;
            border: 1px solid #ccc;
            box-shadow: inset 0 1px 2px rgba(0,0,0,.07);
        }
    </style>
    <div class="project-field">
        <label for="project_url_field">Project URL</label>
        <input type="url" id="project_url_field" name="project_url_field" value="<?php echo esc_url($project_url); ?>" class="regular-text">
        <p class="description">Enter the full URL for the project (e.g., https://www.example.com).</p>
    </div>
    <?php
}

/**
 * Render the HTML for the Responsive Project Images meta box.
 */
function render_responsive_project_images_meta_box_html($post) {
    wp_nonce_field('responsive_project_images_nonce_action', 'responsive_project_images_nonce');

    $mobile_image_id = (int) get_post_meta($post->ID, '_mobile_project_image_id', true);
    $desktop_image_id = (int) get_post_meta($post->ID, '_desktop_project_image_id', true);

    $mobile_image_url = $mobile_image_id ? wp_get_attachment_image_url($mobile_image_id, 'thumbnail') : '';
    $desktop_image_url = $desktop_image_id ? wp_get_attachment_image_url($desktop_image_id, 'thumbnail') : '';
    ?>
    <style>
        .image-field-wrap {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
            margin-bottom: 15px;
        }
        .image-field-wrap:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .image-field-wrap label {
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
        }
        .image-preview {
            max-width: 150px;
            height: auto;
            border: 1px solid #ccc;
            margin-bottom: 10px;
            display: block;
        }
        .image-preview.empty {
            width: 150px;
            height: 100px;
            background: #f0f0f1;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #888;
            font-size: 14px;
        }
        .button-group {
            display: flex;
            gap: 10px;
        }
    </style>
    <div class="image-field-wrap">
        <label for="mobile_project_image_field">Mobile Project Image</label>
        <?php if ($mobile_image_url) : ?>
            <img src="<?php echo esc_url($mobile_image_url); ?>" class="image-preview" id="mobile_image_preview">
        <?php else : ?>
            <div class="image-preview empty" id="mobile_image_preview">No Image Selected</div>
        <?php endif; ?>
        <input type="hidden" id="mobile_project_image_id" name="mobile_project_image_id" value="<?php echo esc_attr($mobile_image_id); ?>">
        <div class="button-group">
            <button type="button" class="button button-secondary select-image-button" data-field="mobile">Select Image</button>
            <button type="button" class="button button-secondary remove-image-button" data-field="mobile" <?php echo $mobile_image_id ? '' : 'style="display:none;"'; ?>>Remove Image</button>
        </div>
        <p class="description">Upload or select an image for mobile display.</p>
    </div>

    <div class="image-field-wrap">
        <label for="desktop_project_image_field">Desktop Project Image</label>
        <?php if ($desktop_image_url) : ?>
            <img src="<?php echo esc_url($desktop_image_url); ?>" class="image-preview" id="desktop_image_preview">
        <?php else : ?>
            <div class="image-preview empty" id="desktop_image_preview">No Image Selected</div>
        <?php endif; ?>
        <input type="hidden" id="desktop_project_image_id" name="desktop_project_image_id" value="<?php echo esc_attr($desktop_image_id); ?>">
        <div class="button-group">
            <button type="button" class="button button-secondary select-image-button" data-field="desktop">Select Image</button>
            <button type="button" class="button button-secondary remove-image-button" data-field="desktop" <?php echo $desktop_image_id ? '' : 'style="display:none;"'; ?>>Remove Image</button>
        </div>
        <p class="description">Upload or select an image for desktop display.</p>
    </div>
    <?php
}


/**
 * Save the custom meta box data for Projects.
 */
function save_project_meta_box_data($post_id) {
    // Save Project URL
    if (isset($_POST['project_details_nonce']) && wp_verify_nonce($_POST['project_details_nonce'], 'project_details_nonce_action')) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;
        if (isset($_POST['post_type']) && 'project' == $_POST['post_type'] && !current_user_can('edit_post', $post_id)) return $post_id;

        if (isset($_POST['project_url_field'])) {
            update_post_meta($post_id, 'project_url', esc_url_raw($_POST['project_url_field']));
        }
    }

    // Save Responsive Project Image IDs
    if (isset($_POST['responsive_project_images_nonce']) && wp_verify_nonce($_POST['responsive_project_images_nonce'], 'responsive_project_images_nonce_action')) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;
        if (isset($_POST['post_type']) && 'project' == $_POST['post_type'] && !current_user_can('edit_post', $post_id)) return $post_id;

        // Mobile Image ID
        if (isset($_POST['mobile_project_image_id'])) {
            $mobile_image_id = (int) $_POST['mobile_project_image_id'];
            if ($mobile_image_id > 0) {
                update_post_meta($post_id, '_mobile_project_image_id', $mobile_image_id);
            } else {
                delete_post_meta($post_id, '_mobile_project_image_id'); // Remove if no image selected
            }
        }

        // Desktop Image ID
        if (isset($_POST['desktop_project_image_id'])) {
            $desktop_image_id = (int) $_POST['desktop_project_image_id'];
            if ($desktop_image_id > 0) {
                update_post_meta($post_id, '_desktop_project_image_id', $desktop_image_id);
            } else {
                delete_post_meta($post_id, '_desktop_project_image_id'); // Remove if no image selected
            }
        }
    }
}
add_action('save_post', 'save_project_meta_box_data');


/**
 * Enqueue scripts for the Project Gallery meta box.
 */
function enqueue_project_gallery_scripts($hook) {
    global $post;
    // Load scripts only on project edit screen
    if ('post.php' != $hook && 'post-new.php' != $hook) {
        return;
    }
    if ($post && 'project' === $post->post_type) {
        wp_enqueue_media(); // Enqueue WordPress media scripts
        wp_enqueue_script(
            'project-responsive-image-uploader', // Unique handle
            get_template_directory_uri() . '/js/project-responsive-image-uploader.js', // Your NEW JS file
            ['jquery'], // Dependency: WordPress's bundled jQuery
            filemtime(get_template_directory() . '/js/project-responsive-image-uploader.js'), // Versioning for cache busting
            true // Load in footer
        );
    }
}
add_action('admin_enqueue_scripts', 'enqueue_project_gallery_scripts');


// =========================================================================
// FORM SUBMISSIONS
// =========================================================================

/**
 * Register the "Submissions" Custom Post Type for storing form entries.
 */
function indago_digital_register_submission_cpt() {
    $labels = [
        'name' => _x('Submissions', 'Post Type General Name', 'indagodigital'),
        'singular_name' => _x('Submission', 'Post Type Singular Name', 'indagodigital'),
        'menu_name' => __('Submissions', 'indagodigital'),
        'all_items' => __('All Submissions', 'indagodigital'),
        'view_item' => __('View Submission', 'indagodigital'),
        'add_new_item' => __('Add New Submission', 'indagodigital'),
        'edit_item' => __('Edit Submission', 'indagodigital'),
        'new_item' => __('New Submission', 'indagodigital'),
        'search_items' => __('Search Submissions', 'indagodigital'),
        'not_found' => __('No submissions found', 'indagodigital'),
        'not_found_in_trash' => __('No submissions found in Trash', 'indagodigital'),
    ];
    $args = [
        'label' => __('Submission', 'indagodigital'),
        'description' => __('For storing website form submissions.', 'indagodigital'),
        'labels' => $labels,
        'supports' => ['custom-fields'], // Only supports custom fields, title is auto-generated.
        'public' => false, // Not visible on the front-end.
        'show_ui' => true, // Visible in the admin dashboard.
        'show_in_menu' => true,
        'menu_position' => 8,
        'menu_icon' => 'dashicons-email-alt',
        'capability_type' => 'post',
        'capabilities' => [ // Prevent users from creating new submissions manually.
            'create_posts' => 'do_not_allow',
        ],
        'map_meta_cap' => true, // Required for the 'create_posts' capability to work.
        'show_in_rest' => false, // Not needed for the front-end.
    ];
    register_post_type('submission', $args);
}
add_action('init', 'indago_digital_register_submission_cpt');

/**
 * Add meta box for submission details.
 */
function add_submission_meta_box() {
    add_meta_box(
        'submission_details_meta_box',
        'Submission Details',
        'render_submission_meta_box_html',
        'submission',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_submission_meta_box');

/**
 * Render the HTML for the submission meta box.
 */
function render_submission_meta_box_html($post) {
    // Get existing values
    $full_name = get_post_meta($post->ID, '_full_name', true);
    $email = get_post_meta($post->ID, '_email_address', true);
    $phone = get_post_meta($post->ID, '_phone_number', true);
    $subject = get_post_meta($post->ID, '_subject', true);
    ?>
    <style>
        .submission-field { padding: 6px 0; }
        .submission-field label { font-weight: bold; display: block; margin-bottom: 4px; }
        .submission-field input { width: 100%; padding: 8px; background: #f0f0f1; border: none; box-shadow: none; }
    </style>
    <div class="submission-field">
        <label for="full_name_field">Full Name</label>
        <input type="text" id="full_name_field" value="<?php echo esc_attr($full_name); ?>" readonly>
    </div>
    <div class="submission-field">
        <label for="email_address_field">Email Address</label>
        <input type="email" id="email_address_field" value="<?php echo esc_attr($email); ?>" readonly>
    </div>
    <div class="submission-field">
        <label for="phone_number_field">Phone Number</label>
        <input type="tel" id="phone_number_field" value="<?php echo esc_attr($phone); ?>" readonly>
    </div>
    <div class="submission-field">
        <label for="subject_field">Subject</label>
        <input type="text" id="subject_field" value="<?php echo esc_attr($subject); ?>" readonly>
    </div>
    <?php
}

/**
 * Add custom columns to the 'submission' post type list table.
 */
function set_custom_edit_submission_columns($columns) {
    // Remove default columns we don't need.
    unset($columns['title']);
    unset($columns['date']);

    // Add our custom columns.
    $columns['submission_title'] = __('Submission', 'indagodigital'); // Re-add title with custom label
    $columns['full_name'] = __('Full Name', 'indagodigital');
    $columns['email_address'] = __('Email Address', 'indagodigital');
    $columns['phone_number'] = __('Phone Number', 'indagodigital');
    $columns['submission_date'] = __('Date', 'indagodigital'); // Re-add date

    return $columns;
}
add_filter('manage_submission_posts_columns', 'set_custom_edit_submission_columns');

/**
 * Populate the custom columns with data.
 */
function custom_submission_column_content($column, $post_id) {
    switch ($column) {
        case 'submission_title':
            echo get_the_title($post_id);
            break;
        case 'full_name':
            echo esc_html(get_post_meta($post_id, '_full_name', true));
            break;
        case 'email_address':
            $email = esc_html(get_post_meta($post_id, '_email_address', true));
            echo '<a href="mailto:' . esc_attr($email) . '">' . $email . '</a>';
            break;
        case 'phone_number':
            $phone = esc_html(get_post_meta($post_id, '_phone_number', true));
            echo '<a href="tel:' . esc_attr($phone) . '">' . $phone . '</a>';
            break;
        case 'submission_date':
            echo get_the_date('F j, Y g:i a', $post_id);
            break;
    }
}
add_action('manage_submission_posts_custom_column', 'custom_submission_column_content', 10, 2);

/**
 * Make custom columns sortable.
 */
function make_submission_columns_sortable($columns) {
    $columns['submission_title'] = 'title';
    $columns['full_name'] = 'full_name';
    $columns['submission_date'] = 'date';
    return $columns;
}
add_filter('manage_edit-submission_sortable_columns', 'make_submission_columns_sortable');


// =========================================================================
// SYNC 'PROJECT-SERVICE' TAXONOMY TERMS WITH 'SERVICE' POSTS
// =========================================================================

/**
 * When a 'project-service' term is created, create a matching 'service' post.
 */
add_action('created_project-service', function($term_id) {
    $term = get_term($term_id, 'project-service');
    if (!$term || is_wp_error($term)) return;

    $existing = get_posts([
        'post_type' => 'service',
        'meta_key' => '_service_term_id',
        'meta_value' => $term_id,
        'post_status' => 'any',
        'numberposts' => 1
    ]);
    if ($existing) return;

    $post_id = wp_insert_post([
        'post_title' => $term->name,
        'post_type' => 'service',
        'post_status' => 'publish',
    ]);
    if ($post_id && !is_wp_error($post_id)) {
        update_post_meta($post_id, '_service_term_id', $term_id);
    }
});

/**
 * When a 'project-service' term is deleted, delete the matching 'service' post.
 */
add_action('delete_project-service', function($term_id) {
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
});

/**
 * When a 'project-service' term is updated, update the matching 'service' post title.
 */
add_action('edited_project-service', function($term_id) {
    $term = get_term($term_id, 'project-service');
    if (!$term || is_wp_error($term)) return;

    $posts = get_posts([
        'post_type' => 'service',
        'meta_key' => '_service_term_id',
        'meta_value' => $term_id,
        'post_status' => 'any',
        'numberposts' => 1
    ]);
    if ($posts) {
        wp_update_post(['ID' => $posts[0]->ID, 'post_title' => $term->name]);
    } else {
        // If post doesn't exist for some reason, create it.
        $post_id = wp_insert_post([
            'post_title' => $term->name,
            'post_type' => 'service',
            'post_status' => 'publish',
        ]);
        if ($post_id && !is_wp_error($post_id)) {
            update_post_meta($post_id, '_service_term_id', $term_id);
        }
    }
});

/**
 * On theme activation, sync all terms and posts to ensure data integrity.
 */
function indago_digital_sync_all_service_terms_and_posts() {
    $terms = get_terms(['taxonomy' => 'project-service', 'hide_empty' => false]);
    foreach ($terms as $term) {
        $existing = get_posts(['post_type' => 'service', 'meta_key' => '_service_term_id', 'meta_value' => $term->term_id, 'post_status' => 'any', 'numberposts' => 1]);
        if (!$existing) {
            $post_id = wp_insert_post(['post_title' => $term->name, 'post_type' => 'service', 'post_status' => 'publish']);
            if ($post_id && !is_wp_error($post_id)) {
                update_post_meta($post_id, '_service_term_id', $term->term_id);
            }
        }
    }
    $posts = get_posts(['post_type' => 'service', 'post_status' => 'any', 'numberposts' => -1]);
    foreach ($posts as $post) {
        $term_id = get_post_meta($post->ID, '_service_term_id', true);
        if ($term_id && !get_term($term_id, 'project-service')) {
            wp_delete_post($post->ID, true);
        }
    }
}
add_action('after_switch_theme', 'indago_digital_sync_all_service_terms_and_posts');

/**
 * Add the menu_order of the corresponding 'service' post to the 'project-service' taxonomy term in the REST API.
 * This allows us to sort the terms on the front-end based on the post order.
 */
function add_service_term_order_to_rest( $term ) {
    // Find the 'service' post that is synced with this term via post meta.
    $linked_posts = get_posts([
        'post_type'      => 'service',
        'meta_key'       => '_service_term_id',
        'meta_value'     => $term['id'],
        'posts_per_page' => 1,
        'fields'         => 'ids', // We only need the ID to get the post object.
    ]);

    if ( ! empty( $linked_posts ) ) {
        $post = get_post( $linked_posts[0] );
        return (int) $post->menu_order;
    }

    // Return a high number for any terms that aren't synced, so they appear last.
    return 999;
}

/**
 * Register the new 'menu_order' field for the 'project-service' taxonomy.
 */
function register_service_term_order_field() {
    register_rest_field(
        'project-service', // The slug of the taxonomy we are modifying.
        'menu_order',      // The name of our new field in the API response.
        [
            'get_callback' => 'add_service_term_order_to_rest',
            'schema'       => [
                'description' => __( 'The menu_order from the corresponding service post.', 'indagodigital' ),
                'type'        => 'integer',
                'context'     => [ 'view', 'edit' ],
            ],
        ]
    );
}
add_action( 'rest_api_init', 'register_service_term_order_field' );

/**
 * =========================================================================
 * CUSTOM REST API ENDPOINT FOR FORM SUBMISSIONS
 * =========================================================================
 */

/**
 * Register a custom REST API route to handle form submissions.
 */
function indago_digital_register_submission_route() {
    register_rest_route('indago/v1', '/submissions', [
        'methods' => 'POST',
        'callback' => 'indago_digital_handle_submission',
        'permission_callback' => '__return_true', // Allow public access to submit the form
    ]);
}
add_action('rest_api_init', 'indago_digital_register_submission_route');

/**
 * The callback function that handles the form submission data.
 *
 * @param WP_REST_Request $request The request object.
 * @return WP_REST_Response|WP_Error
 */
function indago_digital_handle_submission(WP_REST_Request $request) {
    // Get the JSON data from the request body
    $params = $request->get_json_params();

    // Get and sanitize the form fields
    $full_name = isset($params['fullName']) ? sanitize_text_field($params['fullName']) : '';
    $email = isset($params['email']) ? sanitize_email($params['email']) : '';
    $phone = isset($params['phone']) ? sanitize_text_field($params['phone']) : '';
    $subject = isset($params['subject']) ? sanitize_text_field($params['subject']) : '';

    // Basic validation
    if (empty($full_name) || empty($email) || empty($subject)) {
        return new WP_Error('missing_fields', 'Required fields are missing.', ['status' => 400]);
    }

    // Create the post title
    $post_title = $full_name . ' - ' . current_time('F j, Y @ g:i a');

    // Insert the new 'submission' post
    $post_id = wp_insert_post([
        'post_type' => 'submission',
        'post_title' => $post_title,
        'post_status' => 'publish',
    ]);

    // If the post was created successfully, add the custom fields (meta data)
    if ($post_id && !is_wp_error($post_id)) {
        update_post_meta($post_id, '_full_name', $full_name);
        update_post_meta($post_id, '_email_address', $email);
        update_post_meta($post_id, '_phone_number', $phone);
        update_post_meta($post_id, '_subject', $subject);

        // Return a success response
        return new WP_REST_Response(['message' => 'Thank you for your submission!'], 200);
    } else {
        // Return an error response if the post could not be created
        return new WP_Error('submission_failed', 'Could not save the submission.', ['status' => 500]);
    }
}


// =========================================================================
// THEME SETUP & VITE INTEGRATION
// =========================================================================

/**
 * Basic theme setup.
 */
function indago_digital_theme_setup() {
    add_theme_support('post-thumbnails');
}
add_action('after_setup_theme', 'indago_digital_theme_setup');

/**
 * Vite integration for development and production builds.
 */
define('IS_VITE_DEVELOPMENT', file_exists(get_template_directory() . '/.vite/development'));

function indago_digital_enqueue_vite_assets() {
    if (IS_VITE_DEVELOPMENT) {
        // Enqueue scripts for Vite HMR in development.
        function vite_head_module_hook() {
            echo '<script type="module" src="https://localhost:5173/@vite/client"></script>';
            echo '<script type="module" src="https://localhost:5173/src/main.ts"></script>';
        }
        add_action('wp_head', 'vite_head_module_hook');
    } else {
        // Enqueue production build assets from the manifest file.
        // This 'else' block is commented out, but is the standard way to load production files.
        /*
        $manifest_path = get_template_directory() . '/dist/.vite/manifest.json';
        if (file_exists($manifest_path)) {
            $manifest = json_decode(file_get_contents($manifest_path), true);
            if (is_array($manifest)) {
                $entry_js = $manifest['src/main.ts']['file'] ?? null;
                if ($entry_js) {
                    wp_enqueue_script('indago-digital-main-js', get_template_directory_uri() . '/dist/' . $entry_js, [], null, true);
                }
                $entry_css = $manifest['src/main.ts']['css'][0] ?? null;
                if ($entry_css) {
                    wp_enqueue_style('indago-digital-main-css', get_template_directory_uri() . '/dist/' . $entry_css);
                }
            }
        }
        */
    }
}
add_action('wp_enqueue_scripts', 'indago_digital_enqueue_vite_assets');

// Prevent WordPress from automatically scaling down large images on upload.
add_filter( 'big_image_size_threshold', '__return_false' );


// =========================================================================
// CUSTOM FIELDS FOR 'REVIEW' POST TYPE
// =========================================================================

/**
 * Add custom fields to the REST API response for the 'review' post type.
 */
function add_review_custom_fields_to_rest() {
    register_rest_field('review', 'reviewer_name', [
        'get_callback' => fn($post) => get_post_meta($post['id'], 'reviewer_name', true),
        'update_callback' => fn($value, $post) => update_post_meta($post->ID, 'reviewer_name', sanitize_text_field($value)),
        'schema' => ['type' => 'string', 'description' => 'Name of the reviewer.', 'context' => ['view', 'edit']],
    ]);
    register_rest_field('review', 'reviewer_title', [
        'get_callback' => fn($post) => get_post_meta($post['id'], 'reviewer_title', true),
        'update_callback' => fn($value, $post) => update_post_meta($post->ID, 'reviewer_title', sanitize_text_field($value)),
        'schema' => ['type' => 'string', 'description' => 'Title/position of the reviewer.', 'context' => ['view', 'edit']],
    ]);
}
add_action('rest_api_init', 'add_review_custom_fields_to_rest');

/**
 * Add the meta box for reviewer details to the 'review' post editor.
 */
function add_review_meta_box() {
    add_meta_box('review_details_meta_box', 'Reviewer Details', 'render_review_meta_box_html', 'review', 'normal', 'high');
}
add_action('add_meta_boxes', 'add_review_meta_box');

/**
 * Render the HTML for the custom fields meta box.
 */
function render_review_meta_box_html($post) {
    wp_nonce_field('review_details_nonce_action', 'review_details_nonce');
    $reviewer_name = get_post_meta($post->ID, 'reviewer_name', true);
    $reviewer_title = get_post_meta($post->ID, 'reviewer_title', true);
    ?>
    <p><label for="reviewer_name_field" style="font-weight:bold;">Reviewer Name</label><br>
    <input type="text" id="reviewer_name_field" name="reviewer_name_field" value="<?php echo esc_attr($reviewer_name); ?>" style="width:100%;"></p>
    <p><label for="reviewer_title_field" style="font-weight:bold;">Reviewer Title/Position</label><br>
    <input type="text" id="reviewer_title_field" name="reviewer_title_field" value="<?php echo esc_attr($reviewer_title); ?>" style="width:100%;"></p>
    <?php
}

/**
 * Save the custom meta box data.
 */
function save_review_meta_box_data($post_id) {
    if (!isset($_POST['review_details_nonce']) || !wp_verify_nonce($_POST['review_details_nonce'], 'review_details_nonce_action')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (isset($_POST['post_type']) && 'review' == $_POST['post_type'] && !current_user_can('edit_post', $post_id)) return;
    if (isset($_POST['reviewer_name_field'])) {
        update_post_meta($post_id, 'reviewer_name', sanitize_text_field($_POST['reviewer_name_field']));
    }
    if (isset($_POST['reviewer_title_field'])) {
        update_post_meta($post_id, 'reviewer_title', sanitize_text_field($_POST['reviewer_title_field']));
    }
}
add_action('save_post', 'save_review_meta_box_data');


// =========================================================================
// ADMIN AREA CLEANUP
// =========================================================================

/**
 * Remove default menu items from the admin sidebar.
 */
function indago_digital_remove_default_menus() {
    remove_menu_page('edit.php'); // Posts
    remove_menu_page('edit.php?post_type=page'); // Pages
    remove_menu_page('edit-comments.php'); // Comments
}
add_action('admin_menu', 'indago_digital_remove_default_menus');

/**
 * Remove items from the top admin bar.
 */
function indago_digital_remove_admin_bar_items($wp_admin_bar) {
    $wp_admin_bar->remove_node('comments');
    $wp_admin_bar->remove_node('new-post');
    $wp_admin_bar->remove_node('new-page');
}
add_action('admin_bar_menu', 'indago_digital_remove_admin_bar_items', 999);

/**
 * Remove comment support from default post types.
 */
function indago_digital_remove_comment_support() {
    remove_post_type_support('post', 'comments');
    remove_post_type_support('page', 'comments');
}
add_action('init', 'indago_digital_remove_comment_support', 100);


// =========================================================================
// VUE ROUTER CATCH-ALL
// =========================================================================

/**
 * Redirect all non-admin, non-API requests to index.php to let Vue Router handle routing.
 */
function indago_digital_handle_vue_routes() {
    if (is_admin() || strpos($_SERVER['REQUEST_URI'], '/wp-json/') !== false || file_exists(ABSPATH . ltrim($_SERVER['REQUEST_URI'], '/'))) {
        return;
    }
    if ($_SERVER['REQUEST_URI'] !== '/') {
        global $wp_query;
        $wp_query->set_404();
        status_header(200); // Send a 200 status so the browser renders the Vue app.
        include(get_template_directory() . '/index.php');
        exit();
    }
}
add_action('template_redirect', 'indago_digital_handle_vue_routes');

/**
 * =========================================================================
 * COMPANY INFORMATION OPTIONS PAGE
 * =========================================================================
 */

/**
 * Add a top-level menu page for Company Information.
 */
function indago_digital_add_company_info_page() {
    add_menu_page(
        'Company Information',      // Page Title
        'Company Info',             // Menu Title
        'manage_options',           // Capability required to see this option
        'company_info',             // Menu Slug
        'indago_digital_render_company_info_page_html', // Function to render the page's HTML
        'dashicons-info-outline',   // Icon
        2                           // Position in the menu
    );
}
add_action('admin_menu', 'indago_digital_add_company_info_page');

/**
 * Render the HTML for the Company Information options page.
 */
function indago_digital_render_company_info_page_html() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
            // Output security fields for the registered setting section
            settings_fields('company_info_options');
            // Output the sections and fields for the page
            do_settings_sections('company_info');
            // Output save settings button
            submit_button('Save Settings');
            ?>
        </form>
    </div>
    <?php
}

/**
 * Register the settings, sections, and fields for the options page.
 */
function indago_digital_register_company_info_settings() {
    // 1. Register a single setting group
    register_setting('company_info_options', 'company_info_options', [
        'sanitize_callback' => 'indago_digital_sanitize_company_options'
    ]);

    // 2. Add a section to the page
    add_settings_section(
        'company_info_main_section',            // ID
        '',                                     // Title (removed)
        null,                                   // Callback function (removed)
        'company_info'                          // Page slug
    );

    // 3. Add the fields for each piece of information
    add_settings_field('company_name', 'Company Name', 'indago_digital_render_field_html', 'company_info', 'company_info_main_section', ['id' => 'company_name', 'type' => 'text']);
    add_settings_field('company_location', 'Location', 'indago_digital_render_field_html', 'company_info', 'company_info_main_section', ['id' => 'company_location', 'type' => 'text']);
    add_settings_field('company_phone', 'Phone Number', 'indago_digital_render_field_html', 'company_info', 'company_phone_main_section', ['id' => 'company_phone', 'type' => 'tel']);
    add_settings_field('company_email', 'Email Address', 'indago_digital_render_field_html', 'company_info', 'company_email_main_section', ['id' => 'company_email', 'type' => 'email']);
}
add_action('admin_init', 'indago_digital_register_company_info_settings');

/**
 * Reusable callback function to render the HTML for each input field.
 *
 * @param array $args Arguments passed from add_settings_field.
 */
function indago_digital_render_field_html($args) {
    $options = get_option('company_info_options');
    $value = isset($options[$args['id']]) ? $options[$args['id']] : '';
    ?>
    <input
        type="<?php echo esc_attr($args['type']); ?>"
        id="<?php echo esc_attr($args['id']); ?>"
        name="company_info_options[<?php echo esc_attr($args['id']); ?>]"
        value="<?php echo esc_attr($value); ?>"
        class="regular-text"
    >
    <?php
}

/**
 * Sanitize the option values before saving them to the database.
 *
 * @param array $input The input array from the form.
 * @return array The sanitized array.
 */
function indago_digital_sanitize_company_options($input) {
    $sanitized_input = [];
    if (isset($input['company_name'])) {
        $sanitized_input['company_name'] = sanitize_text_field($input['company_name']);
    }
    if (isset($input['company_location'])) {
        $sanitized_input['company_location'] = sanitize_text_field($input['company_location']);
    }
    if (isset($input['company_phone'])) {
        $sanitized_input['company_phone'] = sanitize_text_field($input['company_phone']);
    }
    if (isset($input['company_email'])) {
        $sanitized_input['company_email'] = sanitize_email($input['company_email']);
    }
    return $sanitized_input;
}

/**
 * Register a custom REST API endpoint to expose company info.
 */
function indago_digital_register_company_info_endpoint() {
    register_rest_route('indago/v1', '/company-info', [
        'methods' => 'GET',
        'callback' => 'indago_digital_get_company_info_callback',
        'permission_callback' => '__return_true', // Make the endpoint public
    ]);
}
add_action('rest_api_init', 'indago_digital_register_company_info_endpoint');

/**
 * Callback function for the company info REST API endpoint.
 *
 * @return WP_REST_Response
 */
function indago_digital_get_company_info_callback() {
    // Retrieve the saved options from the database.
    $options = get_option('company_info_options');

    // Ensure we return an object even if no options are saved yet.
    $data = [
        'company_name' => isset($options['company_name']) ? $options['company_name'] : '',
        'company_location' => isset($options['company_location']) ? $options['company_location'] : '',
        'company_phone' => isset($options['company_phone']) ? $options['company_phone'] : '',
        'company_email' => isset($options['company_email']) ? $options['company_email'] : '',
    ];

    return new WP_REST_Response($data, 200);
}