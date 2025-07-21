<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
    <?php
        if (!IS_VITE_DEVELOPMENT) {
            $manifest_path = get_template_directory() . '/dist/.vite/manifest.json';
            if (file_exists($manifest_path)) {
                $manifest = json_decode(file_get_contents($manifest_path), true);
                if (is_array($manifest) && isset($manifest['src/main.ts']['file'])) {
                    $entry_file = $manifest['src/main.ts']['file'];
                    echo '<script type="module" src="' . get_template_directory_uri() . '/dist/' . $entry_file . '"></script>';
                }
                if (is_array($manifest) && isset($manifest['src/main.ts']['css'][0])) {
                    $css_file = $manifest['src/main.ts']['css'][0];
                    echo '<link rel="stylesheet" href="' . get_template_directory_uri() . '/dist/' . $css_file . '">';
                }
            }
        }
    ?>
</head>
<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>