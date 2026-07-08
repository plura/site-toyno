<?php

/*
 * Plugin Name: TOYNO
 * Description: Common, site specific code changes for toyno.com website
 * Domain Path: /languages
 * Text Domain: toyno
 */

add_action( 'init', 'toyno_load_textdomain' );
  
/**
 * Load plugin textdomain.
 */
function toyno_load_textdomain() {

    load_plugin_textdomain( 'toyno', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 

}


$TOYNO_MODULES = [
	'p/p',
    'p/modules/p-wp',
    'p/modules/p-wpml',    
    'includes/core',
    'includes/lang',
    'includes/posts',
    'includes/team',
    'includes/works',
    'includes/workshops'
];

$TOYNO_SCRIPTS = ['globals', 'custom-cursor', 'dragmanager', 'dynamic-grid', 'mosaic', 'sidebar', 'toyno-team-members', 'toyno-workshops'];

foreach ($TOYNO_MODULES as $module) {

	$path = dirname( __FILE__ ) . "/" . $module . ".php";

	if( file_exists( $path ) ) {

    	include_once( $path );

	}

}



function toyno_styles() {

    global $sitepress, $TOYNO_SCRIPTS;

    $toyno_data = [
        'pluginURL' => plugin_dir_url( __FILE__ ), 
        'restURL' => rest_url(),
        'restNonce' => wp_create_nonce('wp_rest'),

        'dictionary' => toyno_lang_js()
        
    ];

    if( p_wpml() ) {

        $toyno_data = array_merge( $toyno_data, [

            'lang' => $sitepress->get_current_language()

        ]);

    }

    wp_enqueue_style( 'fancybox', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css');

    wp_enqueue_style( 'panzoom', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/panzoom.css');

    wp_enqueue_script( 'fancybox', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js');

    wp_enqueue_script( 'p', plugins_url( "/p/js/p.js", __FILE__ ) );

    pwp_enqueue( $TOYNO_SCRIPTS, 'toyno-' );


    if( is_singular() ) {

        $toyno_data = array_merge($toyno_data, [
            'id' => get_queried_object()->ID,
            'title' => get_queried_object()->post_title,
            'type' => get_queried_object()->post_type,
            'url' => get_permalink( get_queried_object()->ID )
        ]);

    } else if( is_archive() ) {

        $toyno_data = array_merge($toyno_data, [
            'archive' => 1,
            'type' => get_queried_object()->name
        ]);

    }

    wp_enqueue_script( 'toyno-core', plugins_url( "/includes/js/scripts.js", __FILE__ ), ['fancybox', 'p'], time() );

    wp_localize_script('toyno-core', 'toyno_core', $toyno_data);

}

add_action( 'wp_enqueue_scripts', 'toyno_styles' );

