<?php

function toyno_posts() {

	$params = [
		'post_type' => 'post',
		'posts_per_page' => -1	
	];

	if( is_archive() ) {

		$term = get_queried_object();

		$params['tax_query'] = [

			[
				'taxonomy' => $term->taxonomy,
				'field'    => 'term_id',
				'terms'    => $term->term_id

			]

		];

	}


	$query = p_wpml_query( $params );

	if( $query->have_posts() ) {

		$html = [];

		foreach( $query->posts as $post ) {

			$entry = [];

			$atts_a = ['href' => get_permalink( $post ), 'title' => $post->post_title];

			$classes = ['toyno-post','grid-item'];

			$img = p_thumbnail( p_wpml_id( $post->ID ) );

			if( $img ) {

				$classes[] = 'has-featured-img';

				$atts = ['src' => $img[0], 'width' => $img[1], 'height' => $img[2], 'class' => 'toyno-post-featured-img' ];

				$entry[] = "<a " . p_attributes($atts_a) . "><img " . p_attributes( $atts ) . "/></a>";

			}

			$atts = array_merge( $atts_a, ['class' => 'toyno-post-title']);

			$entry[] = "<a " . p_attributes( $atts ) . ">" . $post->post_title . "</a>";


			$categories = wp_get_post_categories( $post->ID, array( 'fields' => 'all' ) );

			if( $categories ) {

				$cat = [];

				foreach( $categories as $c ) {

					$atts = [
						'class' => 'toyno-post-category',
						'href' => get_term_link($c->term_id, $c->taxonomy),
						'title' => $c->name
					];

					$cat[] = "<a " . p_attributes( $atts ) . ">" . $c->name . "</a>";

				}

				$entry[] = "<div class=\"toyno-post-categories\">" . implode('', $cat) . "</div>";

			}


			$atts = [
				'class' => implode(' ', $classes),
				'data-id' => $post->ID/*,
				'href' => get_permalink( $post ),
				'title' => $post->post_title*/
			];

			$html[] = "<div " . p_attributes( $atts ) . ">" . implode('', $entry) . "</div>";
			
		}


		$classes = ['class' => 'toyno-posts', 'grid-items'];

		$atts = ['class' => implode(' ', $classes)];

		return "<div " . p_attributes( $atts ) . ">" . implode('', $html) . "</div>";

	}

}

add_shortcode('toyno-posts', 'toyno_posts');
