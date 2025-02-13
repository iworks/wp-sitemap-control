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

if ( class_exists( 'sitemap_control' ) ) {
	return;
}

require_once dirname( __FILE__ ) . '/class-wp-sitemap-control-base.php';

class sitemap_control extends iworks_wp_sitemap_control_base {

	/**
	 * Capability for the plugin.
	 *
	 * @since 1.0.0
	 */
	private $capability;

	/**
	 * Option class object.
	 *
	 * @since 1.0.2
	 */
	protected $options;

	/**
	 * Nonce name
	 *
	 * @since 1.0.2
	 */
	private $nonce_name = 'iworks_wp_sitemap_control_nonce';


	/**
	 * Mata name for sitemap.xml include/exclude.
	 *
	 * @since 1.0.2
	 */
	private $meta_name;

	public function __construct() {
		parent::__construct();
		$this->options    = sitemap_control_get_options_object();
		$this->base       = dirname( dirname( __FILE__ ) );
		$this->dir        = basename( dirname( $this->base ) );
		$this->version    = 'PLUGIN_VERSION';
		$this->capability = apply_filters( 'sitemap_control_capability', 'manage_options' );
		$this->meta_name  = $this->get_meta_name( $this->options->get_option_name( 'include' ) );
		/**
		 * WordPress hooks
		 */
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'edit_attachment', array( $this, 'save_data' ) );
		add_action( 'init', array( $this, 'register' ) );
		add_action( 'init', array( $this, 'action_init_register_iworks_rate' ), PHP_INT_MAX );
		add_action( 'load-settings_page_wpsmc_index', array( $this, 'admin_enqueue' ) );
		add_action( 'save_post', array( $this, 'save_data' ) );
		add_filter( 'wp_sitemaps_posts_query_args', array( $this, 'filter_wp_sitemaps_posts_query_args' ), 10, 2 );
		/**
		 * hooks
		 */
		add_filter( 'wp_sitemaps_post_types', array( $this, 'post_types' ) );
		add_filter( 'wp_sitemaps_taxonomies', array( $this, 'taxonomies' ) );
		add_filter( 'wp_sitemaps_posts_query_args', array( $this, 'attachments' ), 10, 2 );
		add_filter( 'wp_sitemaps_posts_entry', array( $this, 'add_last_mod' ), 10, 2 );
		add_filter( 'wp_sitemaps_add_provider', array( $this, 'provider' ), 10, 2 );
		/**
		 * add head link
		 */
		add_action( 'wp_head', array( $this, 'wp_head_add_sitemap' ) );
		/**
		 * iWorks Rate integration
		 * change logo for rate
		 */
		add_filter( 'iworks_rate_notice_logo_style', array( $this, 'filter_plugin_logo' ), 10, 2 );
		/**
		 * load github class
		 *
		 * @since 1.0.8
		 */
		$filename = __DIR__ . '/class-wp-sitemap-control-github.php';
		if ( is_file( $filename ) ) {
			include_once $filename;
			new iworks_wp_sitemap_control_github();
		}
	}

	public function wp_head_add_sitemap() {
		printf(
			'<link rel="sitemap" type="application/xml" title="%s" href="%s" />%s',
			_x( 'Sitemap', 'sitemap tag title in html head', 'wp-sitemap-control' ),
			wp_make_link_relative( site_url( '/wp-sitemap.xml' ) ),
			PHP_EOL
		);
	}

	/**
	 * Show attachements
	 *
	 * @since 1.0.0
	 */
	public function attachments( $args, $post_type ) {
		if ( 'attachment' !== $post_type ) {
			return $args;
		}
		$args['post_status'] = array( 'inherit' );
		return $args;
	}

	/**
	 * handle post types
	 *
	 * @since 1.0.0
	 */
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

	/**
	 * handle taxonomies
	 *
	 * @since 1.0.0
	 */
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
		sitemap_control_options_init();
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
	 *
	 * @since 1.0.0
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
					'<a href="%s">' . __( 'Settings', 'wp-sitemap-control' ) . '</a>',
					esc_url( $url )
				);
			}
			$links[] = '<a href="http://iworks.pl/donate/wp-sitemap-control.php">' . __( 'Donate', 'wp-sitemap-control' ) . '</a>';

		}
		return $links;
	}

	/**
	 * Plugin logo for rate messages
	 *
	 * @since 1.0.1
	 *
	 * @param string $logo Logo, can be empty.
	 * @param object $plugin Plugin basic data.
	 */
	public function filter_plugin_logo( $logo, $plugin ) {
		if ( is_object( $plugin ) ) {
			$plugin = (array) $plugin;
		}
		if ( 'wp-sitemap-control' === $plugin['slug'] ) {
			return plugin_dir_url( dirname( dirname( __FILE__ ) ) ) . '/assets/images/logo.svg';
		}
		return $logo;
	}

	/**
	 * Add lastmod to entry
	 *
	 * @since 1.0.0
	 */
	public function add_last_mod( $entry, $post ) {
		if ( $this->options->get_option( 'lastmod' ) ) {
			$entry['lastmod'] = gmdate( 'Y-m-d', strtotime( $post->post_modified_gmt ) );
		}
		return $entry;
	}

	/**
	 * Handle provider
	 *
	 * @since 1.0.0
	 */
	public function provider( $provider, $name ) {
		/**
		 * remove users (authors)
		 */
		if (
			'users' === $name
			&& ! $this->options->get_option( 'users' )
		) {
			return false;
		}
		return $provider;
	}

	/**
	 * Register assets
	 *
	 * @since 1.0.3
	 */
	public function register() {
		/**
		 * Admin scripts
		 */
		$files = array(
			'wp-sitemap-control-admin' => array(
				'src'  => sprintf( '/admin/wp-sitemap-control%s.js', $this->dev ),
				'deps' => array( 'jquery' ),
			),
		);
		foreach ( $files as $handle => $data ) {
			$file = sprintf( 'assets/scripts%s', $data['src'] );
			wp_register_script(
				$handle,
				plugins_url( $file, $this->base ),
				$data['deps'],
				$this->get_version(),
				true
			);
		}
	}

	/**
	 * Enqueue assets
	 *
	 * @since 1.0.3
	 */
	public function admin_enqueue() {
		wp_enqueue_script( 'wp-sitemap-control-admin' );
	}

	public function add_meta_boxes() {
		$post_type = get_post_type();
		if ( ! $this->options->get_option( 'single_' . $post_type ) ) {
			return;
		}
		add_meta_box(
			'wp-sitemap-control',
			__( 'WP Sitemap Control', 'wp-sitemap-control' ),
			array( $this, 'meta_box_html' ),
			$post_type,
			'side',
			'low'
		);
	}

	private function sanitize_include_exclude_value( $value ) {
		if ( empty( $value ) ) {
			return 'include';
		}
		if ( preg_match( '/^(include|exclude)$/', $value ) ) {
			return $value;
		}
		return 'include';
	}

	private function get_include_exclude_value_by_post_meta( $post_id ) {
		$value = get_post_meta( $post_id, $this->meta_name, true );
		$value = $this->sanitize_include_exclude_value( $value );
		return $value;
	}

	/**
	 * entry metabox html content
	 *
	 * @since 1.0.0
	 */
	public function meta_box_html( $post ) {
		$value = $this->get_include_exclude_value_by_post_meta( $post->ID );
		$this->add_nonce();
		$post_type_object = get_post_type_object( get_post_type() );
		echo '<ul>';
		echo '<li>';
		echo '<label>';
		printf(
			' <input type="radio" name="%s" value="include" %s />',
			esc_attr( $this->meta_name ),
			checked( $value, 'include', false )
		);
		esc_html_e( 'Include in sitemap', 'wp-sitemap-control' );
		echo '</label';
		echo '</li>';
		echo '<li>';
		echo '<label>';
		printf(
			' <input type="radio" name="%s" value="exclude" %s />',
			esc_attr( $this->meta_name ),
			checked( $value, 'exclude', false )
		);
		esc_html_e( 'Exclude from sitemap', 'wp-sitemap-control' );
		echo '</label';
		echo '</li>';
		echo '</ul>';
	}

	/**
	 * generate nonce
	 *
	 * @since 1.0.2
	 */
	private function add_nonce() {
		wp_nonce_field( __CLASS__, $this->nonce_name );
	}

	/**
	 * Save entry meta for sitemap.xml
	 *
	 * @since 1.0.2
	 */
	public function save_data( $post_id ) {
		if ( ! $this->check_nonce() ) {
			return;
		}
		$value = filter_input( INPUT_POST, $this->meta_name );
		$value = $this->sanitize_include_exclude_value( $value );
		$this->update_single_post_meta( $post_id, $this->meta_name, $value );
	}

	/**
	 * Check nonce.
	 *
	 * @since 1.0.2
	 */
	private function check_nonce() {
		$value = filter_input( INPUT_POST, $this->nonce_name );
		if ( ! empty( $value ) ) {
			return wp_verify_nonce( $value, __CLASS__ );
		}
		return false;
	}

	/**
	 * Update single meta post value.
	 *
	 * @since 1.0.2
	 */
	private function update_single_post_meta( $post_ID, $meta_key, $meta_value ) {
		if ( empty( $meta_value ) ) {
			delete_post_meta( $post_ID, $meta_key );
			return;
		}
		if ( add_post_meta( $post_ID, $meta_key, $meta_value, true ) ) {
			return;
		}
		update_post_meta( $post_ID, $meta_key, $meta_value );
	}

	/**
	 * Filter entries on sitemap.xml
	 *
	 * @since 1.0.2
	 */
	public function filter_wp_sitemaps_posts_query_args( $args, $post_type ) {
		$local_args = array(
			'fields'      => 'ids',
			'meta_key'    => $this->meta_name,
			'meta_value'  => 'exclude',
			'nopaging'    => true,
			'post_status' => 'any',
			'post_type'   => $post_type,
		);
		$query      = new WP_Query( $local_args );
		if ( isset( $query->posts ) && ! empty( $query->posts ) ) {
			$args['post__not_in'] = $query->posts;
		}
		return $args;
	}

	/**
	 * register plugin to iWorks Rate Helper
	 *
	 * @since 1.0.0
	 */
	public function action_init_register_iworks_rate() {
		if ( ! class_exists( 'iworks_rate' ) ) {
			include_once dirname( __FILE__ ) . '/rate/rate.php';
		}
		do_action(
			'iworks-register-plugin',
			plugin_basename( $this->plugin_file ),
			__( 'WP Sitemap Control', 'wp-sitemap-control' ),
			'wp-sitemap-control'
		);
	}

}
