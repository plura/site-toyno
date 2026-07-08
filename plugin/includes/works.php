<?php


add_action( 'rest_api_init', function () {
	
	register_rest_route( 'toyno/v1', '/works/', array(
		'methods' => 'GET',
		'callback' => 'toyno_works_ids',
  	) );

} );


function toyno_works_ids( WP_REST_Request $request = NULL ) {

	$tags = [];

	if( $request && $request->get_param('tags') ) {

		$tags = explode(',', $request->get_param('tags') );

	}

	$query = toyno_works_query( $tags );

	if( $query->have_posts() ) {

		$ids = [];

		foreach( $query->posts as $post ) {

			$ids[] = $post->ID;

		}

		return $ids;

	}

	return [];

}



function toyno_works_query( $tags = [] ) {

	global $sitepress;

	$query_atts = [
		'post_type' => 'toyno_work',
		'posts_per_page' => -1,
		'meta_key' => 'toyno_work_year',
    	'orderby' => 'meta_value_num'
	];

	if( !empty( $tags ) ) {

		$query_atts['tax_query'] = [];

		foreach( $tags as $tag ) {

			$query_atts['tax_query'][] = [
				'taxonomy'  => 'toyno_works_tag',
				'field'		=> 'term_id',
				'terms'		=> $tag
			];

		}

		if( count( $tags ) > 1 ) {

			$query_atts['tax_query']['relation']  = 'AND';

		}

	}

	return p_wpml_query( $query_atts );

}






add_shortcode('toyno-works', 'toyno_works');

function toyno_works( array $args ) {

	$args = shortcode_atts([
		'filter' => 1,
	    'grid-item-hover' => 1
	], $args);

	
	$html = [];

	$html[] = toyno_works_filter();

	$html[] = toyno_works_grid( ['grid-item-hover' => $args['grid-item-hover'] ] );


	$atts = ['class' => 'toyno-works'];

	return "<div " . p_attributes( $atts ) . ">" . implode('', $html) . "</div>";

}



function toyno_works_filter() {

	$data = toyno_works_filter_data();

	if( $data ) {

		$html = [];

		foreach( $data as $k => $group ) {

			$classes = ['toyno-works-filter-group', 'grid-filter-group'];

			$atts = ['class' => implode(' ', $classes), 'data-group' => $k + 1];

			$select = ["<option></option>"];

			foreach( $group as $term ) {

				$select[] = "<option value=\"" . $term['id'] . "\">" . $term['name'] . "</option>";

			}

			$html[] = "<select " . p_attributes($atts) . ">" . implode('', $select) . "</select>";

		}


		$classes = ['toyno-works-filter', 'grid-filter'];

		$atts = ['class' => 'toyno-works-filter'];

		return "<div " . p_attributes( $atts ) . ">" . implode('', $html) . "</div>";

	}

}




function toyno_works_filter_data() {

	$object = acf_get_field( 'field_63dd39b0dfb66' );

	if( $object ) {

		$groups = [];

		foreach( $object['choices'] as $k => $v ) {

			$group = [];

			$terms = p_wpml_query([
				'taxonomy'   => 'toyno_works_tag',
				'hide_empty' => true,
				'meta_query' => [
					[
						'key'       => 'toyno_work_tags_group',
						'value'     => $k,
						'compare'   => '='
					]
				]
			], 'terms');

			foreach( $terms as $term ) {

				$value = ['id' => $term->term_id, 'name' => $term->name];

				if( !in_array($value, $group) ) {

					$group[] = $value;

				}				

			}

			$groups[] = $group;

		}

		return $groups;

	}

}




function toyno_works_grid( array $args ) {

	$query = toyno_works_query();

	if( $query->have_posts() ) {

		$html = [];

		foreach( $query->posts as $post ) {

			$entry = [];

			$img = p_thumbnail( $post->ID );

			$p = get_post( p_wpml_id( $post->ID, false ) );

			if( $img ) {

				$atts = ['src' => $img[0], 'width' => $img[1], 'height' => $img[2], 'class' => 'toyno-work-featured-img' ];

				$entry[] = "<img " . p_attributes( $atts ) . "/>";

			}

			$info = ["<h3 class=\"toyno-work-title\">" . $p->post_title . "</h3>"];
			
			$meta = toyno_work_meta( ['fields' => ['client', 'year'], 'id' => $post->ID ] );

			if( $meta ) {

				$info[] = $meta;

			}

			
			$atts = ['class' => 'toyno-work-info'];

			$entry[] = "<div " . p_attributes( $atts ) . ">" . implode('', $info) . "</div>";


			$classes = ['toyno-work', 'grid-item'];

			if( $img ) {

				$classes[] = 'has-featured-img';

			}

			$atts = [
				'class' => implode(' ', $classes),
				'data-id' => $post->ID,
				'href' => get_permalink( $post ),
				'title' => $p->post_title
			];

			$html[] = "<a " . p_attributes( $atts ) . ">" . implode('', $entry) . "</a>";

		}

		$classes = ['class' => 'toyno-works-grid', 'grid-items'];

		if( $args['grid-item-hover'] ) {

			$classes[] = 'has-item-hover';

		}

		$atts = ['class' => implode(' ', $classes)];

		return "<div " . p_attributes( $atts ) . ">" . implode('', $html) . "</div>";

	}

}






function toyno_work_meta( $args = [] ) {

	$args = array_merge([
		'fields' => ['client', 'year', 'file'],
		'id' => ''
	], $args);

	$html = [];

	foreach( $args['fields'] as $k => $field ) {

		$id = p_wpml_id( $args['id'] , !preg_match('/(client)/', $field) );

		$value = get_field( 'toyno_work_' . $field, $id );

		if( $value ) {

			$atts = ['class' => 'toyno-work-meta-field', 'data-type' => $field ];

			if( $field === 'file' ) {

				$atts = array_merge($atts, ['href' => $value['url'], 'title' => __('Case Study', 'toyno'), 'target' => '_blank' ]);

				$html[] = "<a " . p_attributes( $atts ) . ">" . __('Case Study', 'toyno') . "</a>";

			} else {

				$html[] = "<div " . p_attributes( $atts ) . ">" . $value . "</div>";

			}

		}

	}

	if( !empty( $html ) ) {

		$atts = ['class' => 'toyno-work-meta'];

		return "<div " . p_attributes( $atts ) . ">" . implode('', $html ) . "</div>";

	}

	return false;

}


function toyno_work_meta_shortcode( $args ) {

	$args = shortcode_atts([
		'fields' => '',
		'id' => ''
	], $args);

	if( !empty( $args['id'] ) || get_post_type() === 'toyno_work' ) {

		if( empty( $args['id'] ) ) {

			$args['id'] = get_the_ID();

		}

		if( is_string( $args['fields'] ) ) {

			$args['fields'] = array_map('trim', explode(',', $args['fields']));

		}

		return toyno_work_meta( $args );

	}	

}

add_shortcode('toyno-work-meta', 'toyno_work_meta_shortcode');





function toyno_work_gallery( $id ) {

	$gallery = get_field('toyno_work_gallery', p_wpml_id( $id ) );

	if( $gallery ) {

		$html = [];

		foreach( $gallery as $media ) {

			//https://rudrastyh.com/wordpress/responsive-images.html

			$atts = ['class' => 'toyno-work-gallery-media'];


			if( get_post_mime_type( $media['ID'] ) === 'video/mp4' ) {

				$atts = array_merge( $atts, [
					'height' => $media['height'],
					'width' => $media['width']
				]);

				$atts_source = ['src' => $media['url'], 'type' => $media['mime_type'] ];

				$html[] = "<video controls " . p_attributes( $atts ) . "><source " . p_attributes( $atts_source ) . "/></video>";


			} else {


				if( get_post_mime_type( $media['ID'] ) === 'image/gif' ) {

					$atts = array_merge( $atts, [
						'src' => wp_get_attachment_image_url( $media['ID'], 'full' )
					]);

				} else {

					$atts = array_merge( $atts, [
						'src' => wp_get_attachment_image_url( $media['ID'], 'large' ),
						'srcset' => wp_get_attachment_image_srcset( $media['ID'], 'large' ),
						'sizes' => wp_get_attachment_image_sizes( $media['ID'], 'large' )
					]);

				}

				$html[] = "<img " . p_attributes( $atts ) . "/>";

			}

		}

		$atts = ['class' => 'toyno-work-gallery'];

		return "<div " . p_attributes( $atts ) . ">" . implode('', $html) . "</div>";

	}


}



add_shortcode('toyno-work-gallery', function( $args ) {

	$args = shortcode_atts(['id' => ''], $args );

	if( !empty( $args['id'] ) || is_singular('toyno_work') ) {

		if( empty( $args['id'] ) ) {

			$args['id'] = get_the_ID();

		}

		return toyno_work_gallery( $args['id'] );

	}

} );

