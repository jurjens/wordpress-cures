<?php

// 1.1. Optimize image quality
add_filter( 'jpeg_quality', function( $arg ) { return 100; });

// 1.2. Disable Emojicons
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );

// 1.3. Disable Admin bar
show_admin_bar(false);