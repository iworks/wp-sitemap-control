<?php
/**
 * Plugin Name: WP Sitemap Controll
 * Plugin URI: http://iworks.pl/tag/wp-sitemap
 * Description: Allow to controll WordPress build-in sitemap.xml
 * Version: PLUGIN_VERSION
 * Author: iworks
 * Author URI: http://iworks.pl/
 * Requires at least: 5.5
 * Tested up to: 5.5
 * Requires PHP: 7.0
 * Text Domain: wp-sitemap-control
 * Domain Path: /languages/
 * License: GPL2+
 *
 * @package wp-sitemap-control
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * static options
 */
define( 'WPSMC_VERSION', 'PLUGIN_VERSION' );
define( 'WPSMC__PREFIX', 'wpsmc_' );

/**
 * i18n
 */
load_plugin_textdomain( 'wp-sitemap-control', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );

$base   = dirname( __FILE__ );
$vendor = $base . '/vendor';

