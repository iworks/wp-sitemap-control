<?php
/**
 * WP Sitemap Control
 *
 * @package           PLUGIN_NAME
 * @author            AUTHOR_NAME
 * @copyright         2020-PLUGIN_TILL_YEAR Marcin Pietrzak
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       WP Sitemap Control
 * Plugin URI:        PLUGIN_URI
 * Description:       PLUGIN_DESCRIPTION
 * Version:           PLUGIN_VERSION
 * Requires at least: PLUGIN_REQUIRES_WORDPRESS
 * Requires PHP:      PLUGIN_REQUIRES_PHP
 * Author:            AUTHOR_NAME
 * Author URI:        AUTHOR_URI
 * Text Domain:       wp-sitemap-control
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * static options
 */
define( 'WPSMC_VERSION', 'PLUGIN_VERSION' );
define( 'WPSMC_PREFIX', 'wpsmc_' );

$base     = dirname( __FILE__ );
$includes = $base . '/includes';

/**
 * require: Iworkssitemap Class
 */
if ( ! class_exists( 'sitemap_control' ) ) {
	require_once $includes . '/iworks/class-wp-sitemap-control.php';
}
/**
 * configuration
 */
require_once $base . '/etc/options.php';
/**
 * require: IworksOptions Class
 */
if ( ! class_exists( 'iworks_options' ) ) {
	require_once $includes . '/iworks/options/options.php';
}

/**
 * load options
 */

global $sitemap_control_options;
$sitemap_control_options = null;

function sitemap_control_get_options_object() {
	global $sitemap_control_options;
	if ( is_object( $sitemap_control_options ) ) {
		return $sitemap_control_options;
	}
	$sitemap_control_options = new iworks_options();
	$sitemap_control_options->set_option_function_name( 'sitemap_control_options' );
	$sitemap_control_options->set_option_prefix( WPSMC_PREFIX );
	if ( method_exists( $sitemap_control_options, 'set_plugin' ) ) {
		$sitemap_control_options->set_plugin( basename( __FILE__ ) );
	}
	return $sitemap_control_options;
}

function sitemap_control_options_init() {
	global $sitemap_control_options;
	$sitemap_control_options->options_init();
}

function sitemap_control_activate() {
	$sitemap_control_options = new iworks_options();
	$sitemap_control_options->set_option_function_name( 'sitemap_control_options' );
	$sitemap_control_options->set_option_prefix( WPSMC_PREFIX );
	$sitemap_control_options->activate();
	/**
	 * install tables
	 */
	$sitemap_control = new sitemap_control;
}

function sitemap_control_deactivate() {
	global $sitemap_control_options;
	$sitemap_control_options->deactivate();
}

global $sitemap_control;
$sitemap_control = new sitemap_control();

/**
 * install & uninstall
 */
register_activation_hook( __FILE__, 'sitemap_control_activate' );
register_deactivation_hook( __FILE__, 'sitemap_control_deactivate' );

