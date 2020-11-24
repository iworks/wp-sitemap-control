<?php
function iworks_sitemap_options() {
	$options = array();
	/**
	 * main settings
	 */
	$parent           = 'options-general.php';
	$options['index'] = array(
		'version'    => '0.0',
		'use_tabs'   => true,
		'page_title' => __( 'Sitemap', 'sitemap' ),
		'menu'       => 'submenu',
		'parent'     => $parent,
		'options'    => array(),
	);
	/**
	 * Post types
	 */
	$options['index']['options'][] = array(
		'type'  => 'heading',
		'label' => __( 'Post Types', 'sitemap' ),
	);
	foreach ( get_post_types( array( 'public' => true ), 'objects' ) as $slug => $obj ) {
		$options['index']['options'][] = array(
			'name'              => 'post_type_' . $slug,
			'type'              => 'checkbox',
			'th'                => $obj->label,
			'default'           => in_array( $slug, array( 'post', 'page' ) ),
			'sanitize_callback' => 'absint',
			'classes'           => array( 'switch-button' ),
		);
	}
	/**
	 * Taxonomies
	 */
	$options['index']['options'][] = array(
		'type'  => 'heading',
		'label' => __( 'Taxonomies', 'sitemap' ),
	);
	foreach ( get_taxonomies( array( 'public' => true ), 'objects' ) as $slug => $obj ) {
		$options['index']['options'][] = array(
			'name'              => 'taxonomy_' . $slug,
			'type'              => 'checkbox',
			'th'                => $obj->label,
			'default'           => in_array( $slug, array( 'post_tag', 'category' ) ),
			'sanitize_callback' => 'absint',
			'classes'           => array( 'switch-button' ),
		);
	}
	return $options;
}

