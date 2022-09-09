<?php

add_action( 'wp_enqueue_scripts', 'enqueue_script' );

function enqueue_script() {
    wp_register_script( 'custom-script', get_stylesheet_directory_uri() . '/gyp/gyp_test.js' );
    wp_enqueue_script('custom-script');
}

?>
