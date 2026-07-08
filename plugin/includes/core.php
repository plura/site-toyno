<?php

add_shortcode('toyno-ui-goback', function() {

	$atts = ['class' => 'toyno-ui-goback', 'href' => '#', 'title' => __('Back', 'toyno')];

	return "<a " . p_attributes( $atts ) . ">" . $atts['title'] . "</a>";
 
});
