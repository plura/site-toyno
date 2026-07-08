<?php


add_action( 'rest_api_init', function () {
	
	register_rest_route( 'toyno/v1', '/team/', array(
		'methods' => 'GET',
		'callback' => 'toyno_team_ids',
  	) );

} );


function toyno_team_ids( WP_REST_Request $request = NULL ) {

	if( $request && $request->get_param('ids') ) {

		$ids = explode(',', $request->get_param('ids') );

		$query = toyno_team_members_query(['ids' => $ids, 'active' => -1]);

		if( $query->have_posts() ) {

			$members = [];

			foreach( $query->posts as $post ) {

				$members[ $post->ID ] = toyno_team_member( ['id' => $post->ID] );

			}

			return $members;

		}

	}

	return [];

}


/*wereerrerewr*/
function toyno_team_members_query( array $args = [] ) {

	$args = array_merge([
		'active' => 1,
		'type' => 'team'
	], $args);

	$params = [
		'post_type' => 'toyno_team_member',
		'posts_per_page' => -1
	];

	$meta = [];

	if( $args['active'] !== -1 ) {

		$meta[] = [
			'key'	=> 'toyno_team_member_status',
			'value'	=> 1
		];

	}

	if( $args['type'] === 'partners' ) {

		$meta[] = [
			'key'	=> 'toyno_team_member_partner',
			'value'	=> 1
		];

	} else {

		$team = [

			'relation' => 'OR', 

			[
				'key'		=> 'toyno_team_member_partner',
				'value'		=> 0
			],

			[
				'key'		=> 'toyno_team_member_partner',
				'compare'	=> 'NOT EXISTS'
			]

		];

		if( empty( $meta ) ) {

			$meta = array_merge($meta, $team);

		} else {

			$meta = array_merge($meta, [ $team ] );

		}		

	}

	if( !empty( $args['ids'] ) ) {

		$params['post__in'] = $args['ids'];

	}

	if( !empty( $meta ) ) {

		$params['meta_query'] = $meta;

	}

	return p_wpml_query( $params );

}





function toyno_team_members() {

	$queries = [
		'team' => [
			'data' => toyno_team_members_query(),
			'label' => __('Team', 'toyno')
		],
		'partners' => [
			'data' =>toyno_team_members_query(['type' => 'partners']),
			'label' => __('Partners', 'toyno')
		]
	];

	$html = [];

	foreach( $queries as $k => $obj ) {

		if( $obj['data']->have_posts() ) {

			$group = [];

			foreach( $obj['data']->posts as $post ) {
				
				$group[] = toyno_team_member( ['id' => $post->ID] );

			}

			$classes = ['class' => 'toyno-team-members', 'grid-items'];

			$atts = [
				'class' => implode(' ', $classes),
				'data-label' => $obj['label'],
				'data-layout' => 'grid',
				'data-member-type' => $k
			];

			$html[] = "<div " . p_attributes( $atts ) . ">" . implode('', $group) . "</div>";

		}

	}

	if( !empty( $html ) ) {

		return implode('', $html);

	}

}

add_shortcode('toyno-team-members', 'toyno_team_members');


function toyno_team_member( $args ) {

	$args = array_merge([
		'bio'	=> true,
		'fields'		=> ['pronouns', 'position', 'social_media'],
		'id' 			=> '',
	], $args);

	$entry = [];

	$post = get_post( p_wpml_id( $args['id'], false ) );

	$img = p_thumbnail( p_wpml_id( $args['id'] ) );

	if( $img ) {

		$atts = ['src' => $img[0], 'width' => $img[1], 'height' => $img[2], 'class' => 'toyno-team-member-img' ];

		$entry[] = "<div class=\"toyno-team-member-img-holder\"><img " . p_attributes( $atts ) . "/></div>";

	}


	$info = ["<h3 class=\"toyno-team-member-name\">" . $post->post_title . "</h3>"];
	
	$meta = toyno_team_member_meta( ['id' => p_wpml_id( $args['id'] ), 'fields' => $args['fields'] ] );

	if( $meta ) {

		$info[] = $meta;

	}

	if( $args['bio'] ) {

		$info[] = "<div class=\"toyno-team-member-bio\">" . apply_filters('the_content', $post->post_content) . "</div>";

	}
	
	$atts = ['class' => 'toyno-team-member-info'];

	$entry[] = "<div " . p_attributes( $atts ) . ">" . implode('', $info) . "</div>";



	$classes = ['toyno-team-member', 'grid-item'];

	if( $img ) {

		$classes[] = 'has-featured-img';

	}

	$atts = [
		'class' => implode(' ', $classes),
		'data-id' => $args['id']
	];


	foreach( ['collaborator', 'partner'] as $k ) {

		$field = get_field( 'toyno_team_member_' . $k, $args['id'] );

		if( $field ) {

			$atts['data-' . $k] = 1;

		}

	}

	return "<div " . p_attributes( $atts ) . ">" . implode('', $entry) . "</div>";

}




function toyno_team_member_meta( $args = [] ) {

	$args = array_merge([
		'fields'	=> ['pronouns', 'position', 'social_media'],
		'id' 		=> ''		
	], $args);

	$classes =  ['toyno-team-member-meta'];

	$id = empty( $args['id'] ) ? get_the_ID() : $args['id'];

	$html = [];

	foreach( $args['fields'] as $field ) {

		$id = p_wpml_id( $args['id'] , !preg_match('/(pronouns|position)/', $field) );
 
		$value = get_field( 'toyno_team_member_' . $field, $id );

		if( $value ) {

			if( $field === 'social_media' ) {

				$a = [];

				foreach( $value as $social_media ) {

					$atts = [
						'class' => 'toyno-team-member-social-media',
						'href' => $social_media['toyno_team_member_social_media_url'],
						'target' => '_blank',
						'title' => $social_media['toyno_team_member_social_media_name']
					];

					$a[] = "<a " . p_attributes( $atts ) . ">" . $atts['title'] . "</a>";

				}

				$value = implode('', $a);

			}

			$atts_meta_field = ['class' => 'toyno-team-member-meta-field', 'data-type' => preg_replace('/_/', '-', $field)];

			$html[] = "<div " . p_attributes( $atts_meta_field ) . ">" . $value . "</div>";

			$classes[] = "has-meta-" . preg_replace('/_/', '-', $field);

		}

	}

	if( !empty( $html ) ) {

		$atts = ['class' => implode(' ', $classes) ];

		return "<div " . p_attributes( $atts ) . ">" . implode('', $html ) . "</div>";

	}

	return false;

}




add_shortcode('team-member-holder', function() {

	return "<div class=\"team-member-holder\"></div>";

});