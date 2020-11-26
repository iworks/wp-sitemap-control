<?php
function sitemap_control_options() {
	$options = array();
	/**
	 * main settings
	 */
	$parent           = 'options-general.php';
	$options['index'] = array(
		'version'    => '0.0',
		'use_tabs'   => true,
		'page_title' => __( 'Sitemap', 'wp-sitemap-control' ),
		'menu'       => 'submenu',
		'parent'     => $parent,
		'options'    => array(),
	);
	/**
	 * Post types
	 */
	$options['index']['options'][] = array(
		'type'  => 'heading',
		'label' => __( 'Post Types', 'wp-sitemap-control' ),
	);
	foreach ( get_post_types( array( 'public' => true ), 'objects' ) as $slug => $obj ) {
		if ( 'post_format' === $slug ) {
			continue;
		}
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
		'label' => __( 'Taxonomies', 'wp-sitemap-control' ),
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
	/**
	 * Misc
	 */
	$options['index']['options'][] = array(
		'type'  => 'heading',
		'label' => __( 'Misc', 'sitemap' ),
	);
	$options['index']['options'][] = array(
		'name'              => 'users',
		'type'              => 'checkbox',
		'th'                => __( 'Users', 'wp-sitemap-control' ),
		'default'           => 1,
		'sanitize_callback' => 'absint',
		'classes'           => array( 'switch-button' ),
	);
	$options['index']['options'][] = array(
		'name'              => 'lastmod',
		'type'              => 'checkbox',
		'th'                => __( 'Last modified', 'wp-sitemap-control' ),
		'default'           => 0,
		'sanitize_callback' => 'absint',
		'classes'           => array( 'switch-button' ),
		'description'       => __( 'Add the last modified date for entries.', 'wp-sitemap-control' ),
	);
	return $options;
}

