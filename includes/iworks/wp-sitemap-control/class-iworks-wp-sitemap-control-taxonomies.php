<?php
/**
 * WP Sitemap Control - Taxonomies
 *
 * @package     WP Sitemap Control
 * @author      Marcin Pietrzak <marcin@iworks.pl>
 * @copyright   Copyright 2025 Marcin Pietrzak
 * @license     GPL-3.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name: WP Sitemap Control - Taxonomies
 * Description: Controls which taxonomies are included in the WordPress sitemap.
 * Version:     1.2.0
 * Author:      Marcin Pietrzak
 * Author URI:  https://iworks.pl/
 * License:     GPL-3.0-or-later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'iworks_wp_sitemap_control_taxonomies' ) ) {

	/**
	 * Class iworks_wp_sitemap_control_taxonomies
	 *
	 * Controls which taxonomies are included in the WordPress sitemap.
	 */
	class iworks_wp_sitemap_control_taxonomies extends iworks_wp_sitemap_control_base {

		/**
		 * Meta key for storing the exclude from sitemap setting.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		private $exclude_meta_key = 'exclude_from_sitemap';

		/**
		 * Class constructor.
		 */
		public function __construct() {
			parent::__construct();
			/**
			 * WordPress hooks
			 */
			add_action( 'created_term', array( $this, 'save_taxonomy_meta' ), 10, 3 );
			add_action( 'edited_term', array( $this, 'save_taxonomy_meta' ), 10, 3 );
			add_action( 'admin_init', array( $this, 'register_term_meta' ) );
			add_filter( 'wp_sitemaps_taxonomies_query_args', array( $this, 'filter_wp_sitemaps_taxonomies_query_args' ), 10, 2 );
		}

		/**
		 * Exclude terms from the sitemap.
		 *
		 * @param WP_Term_Query $query The term query object.
		 */
		public function filter_wp_sitemaps_taxonomies_query_args( $args, $taxonomy ) {
			$term_query_args = array(
				'taxonomy'   => $taxonomy,
				'fields'     => 'ids',
				'meta_query' => array(
					array(
						'key'     => $this->exclude_meta_key,
						'value'   => '1',
						'compare' => '=',
					),
				),
			);
			$term_query      = new WP_Term_Query( $term_query_args );
			$exclude_ids     = $term_query->get_terms();
			if ( empty( $exclude_ids ) ) {
				return $args;
			}
			$args['exclude'] = $exclude_ids;
			return $args;
		}

		/**
		 * Register term meta for our custom field.
		 *
		 * @since 1.2.0
		 */
		public function register_term_meta() {
			$taxonomies = $this->get_available_taxonomies();
			foreach ( $taxonomies as $taxonomy ) {
				// Add field to add term form
				add_action( $taxonomy->name . '_add_form_fields', array( $this, 'add_taxonomy_field' ), 10, 2 );

				// Add field to edit term form
				add_action( $taxonomy->name . '_edit_form_fields', array( $this, 'edit_taxonomy_field' ), 10, 2 );

				// Add column to term list table
				add_filter( 'manage_edit-' . $taxonomy->name . '_columns', array( $this, 'add_term_column' ) );
				add_filter( 'manage_' . $taxonomy->name . '_custom_column', array( $this, 'manage_term_column' ), 10, 3 );
			}
		}

		/**
		 * Add field to add new taxonomy term form.
		 *
		 * @param string $taxonomy Taxonomy slug.
		 */
		public function add_taxonomy_field( $taxonomy ) {
			?>
			<div class="form-field term-exclude-from-sitemap-wrap">
				<label for="exclude_from_sitemap">
					<input type="checkbox" name="exclude_from_sitemap" id="exclude_from_sitemap" value="1" />
					<?php esc_html_e( 'Exclude from sitemap', 'wp-sitemap-control' ); ?>
				</label>
				<p class="description">
					<?php esc_html_e( 'Check this to exclude this term from the sitemap.', 'wp-sitemap-control' ); ?>
				</p>
			</div>
			<?php
		}

		/**
		 * Add field to edit taxonomy term form.
		 *
		 * @param WP_Term $term     Current taxonomy term object.
		 * @param string  $taxonomy Current taxonomy slug.
		 */
		public function edit_taxonomy_field( $term, $taxonomy ) {
			$exclude = get_term_meta( $term->term_id, $this->exclude_meta_key, true );
			?>
			<tr class="form-field term-exclude-from-sitemap-wrap">
				<th scope="row">
					<label for="exclude_from_sitemap"><?php esc_html_e( 'Exclude from sitemap', 'wp-sitemap-control' ); ?></label>
				</th>
				<td>
					<label>
						<input type="checkbox" name="exclude_from_sitemap" id="exclude_from_sitemap" value="1" <?php checked( $exclude, '1' ); ?> />
						<?php esc_html_e( 'Exclude this term from the sitemap', 'wp-sitemap-control' ); ?>
					</label>
					<p class="description">
						<?php esc_html_e( 'Check this to exclude this term from the sitemap.', 'wp-sitemap-control' ); ?>
					</p>
				</td>
			</tr>
			<?php
		}

		/**
		 * Save the custom field value.
		 *
		 * @param int    $term_id  Term ID.
		 * @param int    $tt_id    Term taxonomy ID.
		 * @param string $taxonomy Taxonomy slug.
		 */
		public function save_taxonomy_meta( $term_id, $tt_id = '', $taxonomy = '' ) {
			if ( isset( $_POST['exclude_from_sitemap'] ) ) {
				update_term_meta( $term_id, $this->exclude_meta_key, '1' );
			} else {
				delete_term_meta( $term_id, $this->exclude_meta_key );
			}
		}

		/**
		 * Add custom column to taxonomy list table.
		 *
		 * @param array $columns Existing columns.
		 * @return array Modified columns.
		 */
		public function add_term_column( $columns ) {
			$columns['exclude_from_sitemap'] = __( 'Sitemap', 'wp-sitemap-control' );
			return $columns;
		}

		/**
		 * Display custom column content in taxonomy list table.
		 *
		 * @param string $content     Column content.
		 * @param string $column_name Column name.
		 * @param int    $term_id     Term ID.
		 * @return string Column content.
		 */
		public function manage_term_column( $content, $column_name, $term_id ) {
			if ( 'exclude_from_sitemap' === $column_name ) {
				$exclude = get_term_meta( $term_id, $this->exclude_meta_key, true );
				$content = $exclude ?
					'<span class="dashicons dashicons-dismiss" style="color:#dc3232;" title="' . esc_attr__( 'Excluded from sitemap', 'wp-sitemap-control' ) . '"></span>' :
					'<span class="dashicons dashicons-yes" style="color:#46b450;" title="' . esc_attr__( 'Included in sitemap', 'wp-sitemap-control' ) . '"></span>';
			}
			return $content;
		}

		/**
		 * Check if a specific term is excluded from sitemap.
		 *
		 * @param int $term_id Term ID.
		 * @return bool True if excluded, false otherwise.
		 */
		public function is_term_excluded( $term_id ) {
			return (bool) get_term_meta( $term_id, $this->exclude_meta_key, true );
		}

		/**
		 * Get all public taxonomies that should be available for sitemap control.
		 *
		 * @return array List of taxonomy objects.
		 */
		public function get_available_taxonomies() {
			$taxonomies = get_taxonomies(
				array(
					'public'  => true,
					'show_ui' => true,
				),
				'objects'
			);

			return apply_filters( 'iworks_wp_sitemap_control_available_taxonomies', $taxonomies );
		}

		/**
		 * Check if a specific taxonomy is included in the sitemap.
		 *
		 * @param string $taxonomy Taxonomy name.
		 * @return bool True if included, false otherwise.
		 */
		public function is_taxonomy_included( $taxonomy ) {
			$included = get_option( 'iworks_wp_sitemap_control_taxonomies', array() );
			return in_array( $taxonomy, (array) $included, true );
		}

		/**
		 * Save the list of taxonomies to include in the sitemap.
		 *
		 * @param array $taxonomies Array of taxonomy names to include.
		 * @return bool True on success, false on failure.
		 */
		public function save_included_taxonomies( $taxonomies ) {
			if ( ! is_array( $taxonomies ) ) {
				$taxonomies = array();
			}
			return update_option( 'iworks_wp_sitemap_control_taxonomies', $taxonomies );
		}

		/**
		 * Filter the list of taxonomies included in the sitemap.
		 *
		 * @param array $taxonomies Array of taxonomy names.
		 * @return array Filtered array of taxonomy names.
		 */
		public function filter_sitemap_taxonomies( $taxonomies ) {
			$included = get_option( 'iworks_wp_sitemap_control_taxonomies', array() );
			if ( empty( $included ) ) {
				return $taxonomies;
			}
			return array_intersect( $taxonomies, (array) $included );
		}
	}
}
