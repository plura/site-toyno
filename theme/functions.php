<?php

function wpdocs_child_theme_setup() {

    load_child_theme_textdomain( 'divi', get_stylesheet_directory() . '/languages' );

}

add_action( 'after_setup_theme', 'wpdocs_child_theme_setup' );


$TOYNO_HOME_IDS = [319, 1131, 1689];



function my_theme_enqueue_styles() { 

	global $TOYNO_HOME_IDS;

    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );

	wp_enqueue_style( 'toyno-theme-globals-env', get_stylesheet_directory_uri() . '/includes/css/env' . get_envID() . '.css', [], time() );

    wp_enqueue_style( 'toyno-theme-globals', get_stylesheet_directory_uri() . '/includes/css/globals.css', [], time() );

    if( is_page() && in_array( p_wpml_id( get_the_ID() ), $TOYNO_HOME_IDS ) ) {

    	wp_enqueue_style( 'toyno-theme-home', get_stylesheet_directory_uri() . '/includes/css/home.css', [], time() );

    }

    wp_enqueue_script('toyno-theme-core', get_stylesheet_directory_uri() . '/includes/js/scripts.js', [], time() );

}

add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );



function get_envID() {

	if( isset( $_COOKIE['toyno_envID'] ) ) {

		return $_COOKIE['toyno_envID'];

	}

	return $GLOBALS['toyno_envID'];

}


//set environment ID as a cookie
add_action('init', function() {

    if ( !is_admin() && !isset( $_COOKIE['toyno_envID'] ) ) {

    	$envID = rand(1, 4);

        setcookie('toyno_envID', $envID );

        //saves it globally (cookie will only be available after page refresh)
        $GLOBALS['toyno_envID'] = $envID;
    
    }

});


//sets css global vars for envID
add_action( 'wp_head', function() {

	global $TOYNO_HOME_IDS;

	if( !is_admin() ) {

		//if( isset($_COOKIE['toyno_envID']))  echo "cookie: " . $_COOKIE['toyno_envID'];

	    $envID = get_envID();   

	    $css = [];

	    $css_vars = [];

		if( is_page() && in_array( p_wpml_id(), $TOYNO_HOME_IDS ) ) {
		
			$css_vars[] = "--toyno-envID: " . $envID;

			foreach ([1, 2, 3] as $n) {

				if( $n === 2 ) continue;

				$type = $n === 3 ? 'png' : 'svg';

				$css_vars[] = "--toyno-home-grid-section-bg" . $n . ": url(\"" . get_stylesheet_directory_uri() . "/media/home/env" . $envID . "/bg" . $n . "." . $type . "\")";

				$css[] = ".home-grid-section.bg" . $n . " { background-image: var(--toyno-home-grid-section-bg" . $n . "); }";

			}

		} else {

			$css_vars = ["--toyno-title: '" . p_title(['html' => 0]) . "'"];

		}

		if( !empty( $css_vars ) ) {

			$css_vars = [":root {" . implode(';', $css_vars ) . ";}"];

		}		

		$css = array_merge(  $css_vars, $css );

	    ?><style><?php echo implode(' ', $css); ?></style><?php

	}

} );

//adds a body class envID
add_filter( 'body_class', function( $classes ) {

	global $TOYNO_HOME_IDS;

	if( !is_admin() ) {

		$c = [ 'toyno-env' . get_envID() ];


		if( is_page() && in_array( p_wpml_id(), $TOYNO_HOME_IDS ) ) {

			$c[] = 'toyno-home';

		} else if( is_category() || is_singular('post') || ( is_page() && in_array( p_wpml_id(), [747] ) ) ) {

			$c[] = 'toyno-custom-bg-color';

		}
		
    	return array_merge( $classes, $c );

	}

} );




add_filter('p_title', function( $text ) {

	if( is_singular('toyno_work') ) {

		return get_the_title( p_wpml_id( 14, false ) );

	} else if( is_single() ) {

		return get_the_title( p_wpml_id( 747, false ) );

	}

    return $text;

}, 10, 1);