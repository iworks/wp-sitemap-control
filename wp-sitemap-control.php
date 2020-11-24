<?php
/**
 * Plugin Name: WP Sitemap Control
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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * static options
 */
define( 'WPSMC_VERSION', 'PLUGIN_VERSION' );
define( 'WPSMC_PREFIX', 'wpsmc_' );

/**
 * i18n
 */
load_plugin_textdomain( 'wp-sitemap-control', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );

$base   = dirname( __FILE__ );
$vendor = $base . '/vendor';

/**
 * require: Iworkssitemap Class
 */
if ( ! class_exists( 'iworks_sitemap' ) ) {
	require_once $vendor . '/iworks/sitemap.php';
}
/**
 * configuration
 */
require_once $base . '/etc/options.php';
/**
 * require: IworksOptions Class
 */
if ( ! class_exists( 'iworks_options' ) ) {
	require_once $vendor . '/iworks/options/options.php';
}

/**
 * load options
 */

global $iworks_sitemap_options;
$iworks_sitemap_options = iworks_sitemap_get_options_object();

function iworks_sitemap_get_options_object() {
	global $iworks_sitemap_options;
	if ( is_object( $iworks_sitemap_options ) ) {
		return $iworks_sitemap_options;
	}
	$iworks_sitemap_options = new iworks_options();
	$iworks_sitemap_options->set_option_function_name( 'iworks_sitemap_options' );
	$iworks_sitemap_options->set_option_prefix( WPSMC_PREFIX );
	return $iworks_sitemap_options;
}

function iworks_sitemap_options_init() {
	global $iworks_sitemap_options;
	$iworks_sitemap_options->options_init();
}

function iworks_sitemap_activate() {
	$iworks_sitemap_options = new iworks_options();
	$iworks_sitemap_options->set_option_function_name( 'iworks_sitemap_options' );
	$iworks_sitemap_options->set_option_prefix( WPSMC_PREFIX );
	$iworks_sitemap_options->activate();
	/**
	 * install tables
	 */
	$iworks_sitemap = new iworks_sitemap;
	$iworks_sitemap->db_install();
}

function iworks_sitemap_deactivate() {
	global $iworks_sitemap_options;
	$iworks_sitemap_options->deactivate();
}

global $iworks_sitemap;
$iworks_sitemap = new iworks_sitemap();

/**
 * install & uninstall
 */
register_activation_hook( __FILE__, 'iworks_sitemap_activate' );
register_deactivation_hook( __FILE__, 'iworks_sitemap_deactivate' );
/**
 * Ask for vote
 */
include_once dirname( __FILE__ ) . '/vendor/iworks/rate/rate.php';
do_action(
	'iworks-register-plugin',
	plugin_basename( __FILE__ ),
	__( 'WP Sitemap Control', 'sitemap' ),
	'sitemap'
);


