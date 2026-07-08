<?php


function toyno_workshops() {

	$params = [
		'post_type' => 'toyno_workshop'
	];

	$labels = [
		'current' => __('Current Workshops', 'toyno'),
		'previous' => __('Previous Workshops', 'toyno'),
		'next' => __('Future Workshops', 'toyno')
	];

	//https://wordpress.stackexchange.com/a/284257 
	
	$currentdate = date('Y-m-d') . ' 00:00:00';
	
	//current
	$current_start = [
		'key'		=> 'toyno_workshop_datetime_start',
		'compare'	=> '>=',
		'value'		=> $currentdate/*,
		'type'		=> 'DATE'*/
	];

	$current_end = 			[
		'key'		=> 'toyno_workshop_datetime_end',
		'compare'	=> '<=',
		'value'		=> $currentdate/*,
		'type'		=> 'DATE'*/
	];

	//previous
	$previous_end = 			[
		'key'		=> 'toyno_workshop_datetime_end',
		'compare'	=> '<',
		'value'		=> $currentdate/*,
		'type'		=> 'DATE'*/
	];

	//next
	$next_start = 			[
		'key'		=> 'toyno_workshop_datetime_start',
		'compare'	=> '>',
		'value'		=> $currentdate/*,
		'type'		=> 'DATE'*/
	];


	$workshops_groups = [

		'current' => p_wpml_query( array_merge( $params, [ 'meta_query' => ['relation' => 'AND', $current_start, $current_end] ] ) ),

		'previous' => p_wpml_query( array_merge( $params, [ 'meta_query' => [$previous_end] ] ) ),

		'next' => p_wpml_query( array_merge( $params, [ 'meta_query' => [$next_start] ] ) )

	];


	foreach( $workshops_groups as $key => $group ) {

		$html = [];

		if( $group->have_posts() ) {

			$html_group = [];

			foreach( $group->posts as $post ) {

				$atts = [
					'class' => 'toyno-workshop',
					'href' => get_permalink( $post ),
					'title' => $post->post_title
				];

				$atts_meta = [
					'class' => 'toyno-workshop-meta',
					'data-date-end' => toyno_workshop_date( 'toyno_workshop_datetime_end', $post ),
					'data-date-start' => toyno_workshop_date( 'toyno_workshop_datetime_start', $post )
				];

				$html_group[] = "<a " . p_attributes( $atts ) . ">

					<div class=\"toyno-workshop-title\">" . $post->post_title . "</div>

					<div " . p_attributes( $atts_meta ) . "></div>

				</a>";

			}

			if( !empty( $html_group ) ) {

				$atts = ['class' => 'toyno-workhops-group', 'data-label' => $labels[ $key ] ];

				$html[] = "<div " . p_attributes($atts) . ">" . implode('', $html_group) . "</div>";

			}

		}

		if( !empty( $html_group ) ) {

			$atts = ['class' => 'toyno-workshops'];

			return "<div " . p_attributes($atts) . ">" . implode('', $html) . "</div>";

		}

	}

}


function toyno_workshop_date( $field, $post ) {

	return date_i18n( __('l, j \d\e F Y', 'toyno'), strtotime( get_field( $field, $post->ID ) ) );

}


add_shortcode('toyno-workshops', function( $args ) {

	return toyno_workshops();

});




function toyno_workshop_speakers( array $args ) {

	$speakers = get_field('toyno_workshop_speakers', p_wpml_id( $args['id'] ) );

	if( $speakers ) {

		$html = [];

		foreach( $speakers as $row ) {

			$speaker = $row['toyno_workshop_speaker'];

			$html[] = toyno_team_member( ['id' => $speaker->ID, 'fields' => ['pronouns', 'position'], 'bio' => 0 ] );

		}

		$classes = ['class' => 'toyno-team-members', 'grid-items'];

		$atts = [
			'class' => implode(' ', $classes),
			'data-label' => __('Speakers', 'toyno'),
			'data-layout-type' => 'speakers'
		];

		return "<div " . p_attributes( $atts ) . ">" . implode('', $html) . "</div>";
	}

}


add_shortcode('toyno-workshop-speakers', function( $args ) {

	$args = shortcode_atts([
		'id' => ''
	], $args);

	if( !empty( $args['id'] ) || is_singular('toyno_workshop') ) {

		if( empty( $args['id'] ) ) {

			$args['id'] = get_the_ID();

		}

		return toyno_workshop_speakers( $args );

	}

});