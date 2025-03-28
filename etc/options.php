<?php
function sitemap_control_options() {
	$options = array();
	/**
	 * main settings
	 */
	$options['index'] = array(
		'version'         => '0.0',
		'use_tabs'        => true,
		'page_title'      => __( 'Sitemap', 'wp-sitemap-control' ),
		'menu'            => 'options',
		'options'         => array(),
		'enqueue_scripts' => array(
			'wp-sitemap-control-admin',
		),
		'metaboxes'       => array(
			'assistance' => array(
				'title'    => __( 'We are waiting for your message', 'wp-sitemap-control' ),
				'callback' => 'iworks_wp_sitemap_controls_options_need_assistance',
				'context'  => 'side',
				'priority' => 'core',
			),
			'love'       => array(
				'title'    => __( 'I love what I do!', 'wp-sitemap-control' ),
				'callback' => 'iworks_wp_sitemap_control_options_loved_this_plugin',
				'context'  => 'side',
				'priority' => 'core',
			),
		),
	);
	/**
	 * Post types
	 */
	$options['index']['options'][] = array(
		'type'  => 'heading',
		'label' => __( 'Post Types', 'wp-sitemap-control' ),
	);
	$options['index']['options'][] = array(
		'name'    => 'post_type',
		'type'    => 'checkbox',
		'th'      => __( 'Switch all', 'wp-sitemap-control' ),
		'classes' => array( 'switch-button' ),
	);
	foreach ( get_post_types( array( 'public' => true ), 'objects' ) as $slug => $obj ) {
		if ( 'post_format' === $slug ) {
			continue;
		}
		$options['index']['options'][] = array(
			'type'  => 'subheading',
			'label' => $obj->label,
		);
		$options['index']['options'][] = array(
			'name'              => 'post_type_' . $slug,
			'type'              => 'checkbox',
			'th'                => __( 'Enable', 'wp-sitemap-control' ),
			'default'           => in_array( $slug, array( 'post', 'page' ) ),
			'sanitize_callback' => 'absint',
			'classes'           => array( 'switch-button post-type' ),
		);
		$options['index']['options'][] = array(
			'name'              => 'single_' . $slug,
			'type'              => 'checkbox',
			'th'                => __( 'Allow exclusion', 'wp-sitemap-control' ),
			'default'           => 0,
			'sanitize_callback' => 'absint',
			'classes'           => array( 'switch-button post-type' ),
		);
	}
	/**
	 * Taxonomies
	 */
	$options['index']['options'][] = array(
		'type'  => 'heading',
		'label' => __( 'Taxonomies', 'wp-sitemap-control' ),
	);
	$options['index']['options'][] = array(
		'name'    => 'taxonomy',
		'type'    => 'checkbox',
		'th'      => __( 'Switch all', 'wp-sitemap-control' ),
		'classes' => array( 'switch-button' ),
	);
	foreach ( get_taxonomies( array( 'public' => true ), 'objects' ) as $slug => $obj ) {
		$options['index']['options'][] = array(
			'type'  => 'subheading',
			'label' => $obj->label,
		);
		$options['index']['options'][] = array(
			'name'              => 'taxonomy_' . $slug,
			'type'              => 'checkbox',
			'th'                => __( 'Enable', 'wp-sitemap-control' ),
			'default'           => in_array( $slug, array( 'post_tag', 'category' ) ),
			'sanitize_callback' => 'absint',
			'classes'           => array( 'switch-button taxonomy' ),
		);
		// $options['index']['options'][] = array(
			// 'name'              => 'taxonomy_exclusion_' . $slug,
			// 'type'              => 'checkbox',
			// 'th'                => __( 'Allow exclusion', 'wp-sitemap-control' ),
			// 'default'           => 0,
			// 'sanitize_callback' => 'absint',
			// 'classes'           => array( 'switch-button post-type' ),
		// );
	}
	/**
	 * Misc
	 */
	$options['index']['options'][] = array(
		'type'  => 'heading',
		'label' => __( 'Misc', 'wp-sitemap-control' ),
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

function iworks_wp_sitemap_control_options_loved_this_plugin( $iworks_wp_sitemap_control ) {
	$content = apply_filters( 'iworks_rate_love', '', 'wp-sitemap-control' );
	if ( ! empty( $content ) ) {
		echo $content;
		return;
	}
	?>
<p><?php _e( 'Below are some links to help spread this plugin to other users', 'wp-sitemap-control' ); ?></p>
<ul>
	<li><a href="https://wordpress.org/support/plugin/wp-sitemap-control/reviews/#new-post"><?php _e( 'Give it a five stars on WordPress.org', 'wp-sitemap-control' ); ?></a></li>
	<li><a href="<?php _ex( 'https://wordpress.org/plugins/wp-sitemap-control/', 'plugin home page on WordPress.org', 'wp-sitemap-control' ); ?>"><?php _e( 'Link to it so others can easily find it', 'wp-sitemap-control' ); ?></a></li>
</ul>
	<?php
}

function iworks_wp_sitemap_controls_options_need_assistance( $iworks_wp_sitemap_controls ) {
	$content = apply_filters( 'iworks_rate_assistance', '', 'wp-sitemap-control' );
	if ( ! empty( $content ) ) {
		echo $content;
		return;
	}

	?>
<p><?php _e( 'We are waiting for your message', 'wp-sitemap-control' ); ?></p>
<ul>
	<li><a href="<?php _ex( 'https://wordpress.org/support/plugin/wp-sitemap-control/', 'link to support forum on WordPress.org', 'wp-sitemap-control' ); ?>"><?php _e( 'WordPress Help Forum', 'wp-sitemap-control' ); ?></a></li>
</ul>
	<?php
}
