<?php




/**
 *
 * - featured img
 * - featured img ID
 * - wpml
 * - wpml ID
 * - Essential Grid
 */



/* POST FEATURED IMAGE */

//get object featured image
//if no post thumbnail is found, it searches an acf field
function pwp_featured_image( $postID, $acf_field = false, $size = 'large' ) {

	$id = pwp_featured_image_id( $postID );

	if( $id ) {

		foreach( ['large', 'full', 'medium', 'thumbnail'] as $imgsize ) {

			$img = wp_get_attachment_image_src($id, $imgsize);

			if( $img ) {

				return $img;

			}

		}

	}

	return false;

}

//get object featured image id
//if no post thumbnail is found, it searches an acf field
function pwp_featured_image_id( $postID, $acf_field = false ) {

	if( has_post_thumbnail( $postID ) ) {

		return get_post_thumbnail_id( $postID );

	} else if( $acf_field ) {

		$gallery = get_field($acf_field, $postID);

		if( $gallery ) {

			return $gallery[0]['ID'];

		}

	}

	return false;

}




/* WPML */

// wpml: check if wpml exists
function pwp_wpml() {

	return class_exists('sitepress');

}


// wpml: gets the wpml id
function pwp_wpml_id( $id = false, $default = true, $type = 'post' ) {

    global $sitepress;

    if( !$id ) {

    	$id = get_the_ID();

    }

    if( pwp_wpml() && ( !$default || $sitepress->get_current_language() !== $sitepress->get_default_language() ) ) {

    	$objectIDs = is_array( $id ) ? $id : [ $id ];

    	$ids = [];

	    $lang = $default ? $sitepress->get_default_language() : $sitepress->get_current_language();

    	foreach( $objectIDs as $objectID ) {

	    	if( $type === 'post' ) {

	    		$type = get_post_type( $objectID );

	    	}

	        $ids[] = apply_filters( 'wpml_object_id', $objectID, $type, true, $lang );

    	}

    	if( !is_array( $id ) ) {

    		return $ids[0];

    	}

    	return $ids;

    }

    return $id;

}




// ESSENTIAL GRID
function pwp_essential_grid($posts, $alias, $label = false) {

    $ids = [];

    foreach( $posts as $post ) {

        $ids[] = pwp_wpml_id( $post->ID );

    }

    $atts = ['class' => 'pwp-eg-holder'];
 
    if( $label ) {

        $atts['data-label'] = $label;

    }

    $html = do_shortcode('[ess_grid alias="' . $alias . '" posts="' . implode(',', $ids) . '"]');

    return "<div " . p_attributes( $atts ) . ">" . $html . "</div>";    

}
