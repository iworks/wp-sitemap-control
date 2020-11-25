<?php
/*
Copyright 2020-PLUGIN_TILL_YEAR Marcin Pietrzak (marcin@iworks.pl)

this program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( class_exists( 'wp_sitemap_control' ) ) {
	return;
}

require_once dirname( dirname( __FILE__ ) ) . '/iworks.php';

class wp_sitemap_control extends iworks {

	private $capability;
	protected $options;

	public function __construct() {
		parent::__construct();
		$this->options    = wp_sitemap_control_get_options_object();
		$this->base       = dirname( dirname( __FILE__ ) );
		$this->dir        = basename( dirname( $this->base ) );
		$this->version    = 'PLUGIN_VERSION';
		$this->capability = apply_filters( 'wp_sitemap_control_capability', 'manage_options' );
		/**
		 * admin init
		 */
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		/**
		 * hooks
		 */
		add_filter( 'wp_sitemaps_post_types', array( $this, 'post_types' ) );
		add_filter( 'wp_sitemaps_taxonomies', array( $this, 'taxonomies' ) );
		add_filter( 'wp_sitemaps_posts_query_args', array( $this, 'attachments' ), 10, 2 );
		/**
		 * iWorks Rate integration
		 */
		add_action( 'iworks_rate_css', array( $this, 'iworks_rate_css' ) );
	}

	public function attachments( $args, $post_type ) {
		if ( 'attachment' !== $post_type ) {
			return $args;
		}
		$args['post_status'] = array( 'inherit' );
		return $args;
	}

	public function post_types( $elements ) {
		foreach ( $this->options->get_all_options() as $name => $value ) {
			if ( preg_match( '/^post_type_(.+)$/', $name, $matches ) ) {
				$slug = $matches[1];
				if ( $value ) {
					if ( ! isset( $elements[ $slug ] ) ) {
						$elements[ $slug ] = get_post_type_object( $slug );
					}
				} else {
					unset( $elements[ $slug ] );
				}
			}
		}
		return $elements;
	}

	public function taxonomies( $elements ) {
		foreach ( $this->options->get_all_options() as $name => $value ) {
			if ( preg_match( '/^taxonomy_(.+)$/', $name, $matches ) ) {
				$slug = $matches[1];
				if ( $value ) {
					if ( ! isset( $elements[ $slug ] ) ) {
						$elements[ $slug ] = get_taxonomy( $slug );
					}
				} else {
					unset( $elements[ $slug ] );
				}
			}
		}
		return $elements;
	}

	public function admin_init() {
		wp_sitemap_control_options_init();
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
	}

	public function admin_enqueue_scripts() {
		$screen = get_current_screen();
		/**
		 * off on not sitemap pages
		 */
		$re = sprintf( '/%s_/', __CLASS__ );
		if ( ! preg_match( $re, $screen->id ) ) {
			return;
		}
		/**
		 * datepicker
		 */
		$file = 'assets/externals/datepicker/css/jquery-ui-datepicker.css';
		$file = plugins_url( $file, $this->base );
		wp_register_style( 'jquery-ui-datepicker', $file, false, '1.12.1' );
		/**
		 * select2
		 */
		$file = 'assets/externals/select2/css/select2.min.css';
		$file = plugins_url( $file, $this->base );
		wp_register_style( 'select2', $file, false, '4.0.3' );
		/**
		 * Admin styles
		 */
		$file    = sprintf( '/assets/styles/sitemap-admin%s.css', $this->dev );
		$version = $this->get_version( $file );
		$file    = plugins_url( $file, $this->base );
		wp_register_style( 'admin-sitemap', $file, array( 'jquery-ui-datepicker', 'select2' ), $version );
		wp_enqueue_style( 'admin-sitemap' );
		/**
		 * select2
		 */
		wp_register_script( 'select2', plugins_url( 'assets/externals/select2/js/select2.full.min.js', $this->base ), array(), '4.0.3' );
		/**
		 * Admin scripts
		 */
		$files = array(
			'sitemap-admin' => sprintf( 'assets/scripts/admin/sitemap%s.js', $this->dev ),
		);
		if ( '' == $this->dev ) {
			$files = array(
				'sitemap-admin-select2'    => 'assets/scripts/admin/src/select2.js',
				'sitemap-admin-boat'       => 'assets/scripts/admin/src/boat.js',
				'sitemap-admin-datepicker' => 'assets/scripts/admin/src/datepicker.js',
				'sitemap-admin-person'     => 'assets/scripts/admin/src/person.js',
				'sitemap-admin-result'     => 'assets/scripts/admin/src/result.js',
				'sitemap-admin'            => 'assets/scripts/admin/src/sitemap.js',
			);
		}
		$deps = array(
			'jquery-ui-datepicker',
			'select2',
		);
		foreach ( $files as $handle => $file ) {
			wp_register_script(
				$handle,
				plugins_url( $file, $this->base ),
				$deps,
				$this->get_version(),
				true
			);
			wp_enqueue_script( $handle );
		}
		/**
		 * JavaScript messages
		 *
		 * @since 1.0.0
		 */
		$data = array(
			'messages' => array(),
			'nonces'   => array(),
			'user_id'  => get_current_user_id(),
		);
		wp_localize_script(
			'sitemap-admin',
			__CLASS__,
			apply_filters( 'wp_localize_script_sitemap_admin', $data )
		);
	}

	/**
	 * Plugin row data
	 */
	public function plugin_row_meta( $links, $file ) {
		if ( $this->dir . '/wp-sitemap-control.php' == $file ) {
			if ( ! is_multisite() && current_user_can( $this->capability ) ) {
				$url     = add_query_arg(
					'page',
					'wpsmc_index',
					admin_url( 'options-general.php' )
				);
				$links[] = sprintf(
					'<a href="%s">' . __( 'Settings' ) . '</a>',
					esc_url( $url )
				);
			}
			$links[] = '<a href="http://iworks.pl/donate/sitemap.php">' . __( 'Donate' ) . '</a>';

		}
		return $links;
	}

	/**
	 * Change logo for "rate" message.
	 *
	 * @since 2.6.6
	 */
	public function iworks_rate_css() {
		$logo = plugin_dir_url( dirname( dirname( __FILE__ ) ) ) . 'assets/images/logo.svg';
		echo '<style type="text/css">';
		printf( '.iworks-notice-sitemap .iworks-notice-logo{background-image:url(%s);}', esc_url( $logo ) );
		echo '</style>';
	}
}
