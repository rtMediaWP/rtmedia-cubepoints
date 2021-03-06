<?php

/**
 * Plugin Name: rtMedia CubePoints
 * Plugin URI: https://rtmedia.io/products/rtmedia-cubepoints/
 * Description: This plugin provides CubePoints integration with rtMedia.
 * Version: 1.1.4
 * Text Domain: rtmedia
 * Author: rtCamp
 * Author URI: http://rtcamp.com/?utm_source=dashboard&utm_medium=plugin&utm_campaign=rtmedia-cubepoints
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 *  The server file system path to the plugin directory
 */
if ( ! defined( 'RTMEDIA_CUBEPOINTS_PATH' ) ) {
	define( 'RTMEDIA_CUBEPOINTS_PATH', plugin_dir_path( __FILE__ ) );
}

/**
 * The url to the plugin directory
 */
if ( ! defined( 'RTMEDIA_CUBEPOINTS_URL' ) ) {
	define( 'RTMEDIA_CUBEPOINTS_URL', plugin_dir_url( __FILE__ ) );
}

/**
 * The base name of the plugin directory
 */
if ( ! defined( 'RTMEDIA_CUBEPOINTS_BASE_NAME' ) ) {
	define( 'RTMEDIA_CUBEPOINTS_BASE_NAME', plugin_basename( __FILE__ ) );
}

/**
 * The version of the plugin
 */
if ( ! defined( 'RTMEDIA_CUBEPOINTS_VERSION' ) ) {
	/**
	 * The version of the plugin
	 */
	define( 'RTMEDIA_CUBEPOINTS_VERSION', '1.1.4' );
}

// this is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
if ( ! defined( 'EDD_RTMEDIA_CUBEPOINTS_STORE_URL' ) ) {
	// this is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
	define( 'EDD_RTMEDIA_CUBEPOINTS_STORE_URL', 'https://rtmedia.io/' );
}

// the name of your product. This should match the download name in EDD exactly
if ( ! defined( 'EDD_RTMEDIA_CUBEPOINTS_ITEM_NAME' ) ) {
	define( 'EDD_RTMEDIA_CUBEPOINTS_ITEM_NAME', 'rtMedia CubePoints' );
}

// define RTMEDIA_DEBUG to true in wp-config.php to debug updates
if ( defined( 'RTMEDIA_DEBUG' ) && RTMEDIA_DEBUG === true ) {
	set_site_transient( 'update_plugins', null );
}

/**
 * Auto Loader Function
 *
 * Autoloads classes on instantiation. Used by spl_autoload_register.
 *
 * @param string $class_name The name of the class to autoload
 */
function rtmedia_cubepoints_autoloader( $class_name ) {
	$rtlibpath = array(
		'app/main/controllers/media/' . $class_name . '.php',
	);

	foreach ( $rtlibpath as $path ) {
		$path = RTMEDIA_CUBEPOINTS_PATH . $path;

		if ( file_exists( $path ) ) {
			include $path;

			break;
		}
	}
}

/**
 * Register the autoloader function into spl_autoload
 */
spl_autoload_register( 'rtmedia_cubepoints_autoloader' );

/**
 * @param $class_construct
 *
 * @return mixed
 */
function rtmedia_cubepoints_loader( $class_construct ) {
	/*
	 * do not load classes of rtMedia Pro is activated
	 * as it might break some functionality
	 */
	if ( defined( 'RTMEDIA_PRO_PATH' ) ) {
		add_action( 'admin_notices', 'rtmedia_pubepoints_pro_active_notice' );

		return $class_construct;
	}

	require_once RTMEDIA_CUBEPOINTS_PATH . 'app/RTMediaCubePoints.php';

	$class_construct['CubePoints']      = false;
	$class_construct['CubePointsMedia'] = false;

	return $class_construct;
}

add_filter( 'rtmedia_class_construct', 'rtmedia_cubepoints_loader' );

/**
 * rtMedia Pro notice
 */
function rtmedia_pubepoints_pro_active_notice() {
	?>
		<div class="error">
			<p>
				<strong>rtMedia CubePoints</strong> plugin cannot be activated with rtMedia Pro. Please <strong><a href="https://rtmedia.io/blog/rtmedia-pro-splitting-major-change/" target="_blank">read this</a></strong> for more details. You may <strong><a href="https://rtmedia.io/premium-support/" target="_blank">contact support for help</a></strong>.
			</p>
		</div>
	<?php
	// automatic deactivate plugin if rtMedia Pro is active and current user can deactivate plugin.
	if ( current_user_can( 'activate_plugins' ) ) {
		deactivate_plugins( RTMEDIA_CUBEPOINTS_BASE_NAME );
	}
}

/*
 * EDD License
 */
include_once( RTMEDIA_CUBEPOINTS_PATH . 'lib/rt-edd-license/RTEDDLicense.php' );

$rtmedia_cubepoints_details = array(
	'rt_product_id'                  => 'rtmedia_cubepoints',
	'rt_product_name'                => 'rtMedia CubePoints',
	'rt_product_href'                => 'rtmedia-cubepoints',
	'rt_license_key'                 => 'edd_rtmedia_cubepoints_license_key',
	'rt_license_status'              => 'edd_rtmedia_cubepoints_license_status',
	'rt_nonce_field_name'            => 'edd_rtmedia_cubepoints_nonce',
	'rt_license_activate_btn_name'   => 'edd_rtmedia_cubepoints_license_activate',
	'rt_license_deactivate_btn_name' => 'edd_rtmedia_cubepoints_license_deactivate',
	'rt_product_path'                => RTMEDIA_CUBEPOINTS_PATH,
	'rt_product_store_url'           => EDD_RTMEDIA_CUBEPOINTS_STORE_URL,
	'rt_product_base_name'           => RTMEDIA_CUBEPOINTS_BASE_NAME,
	'rt_product_version'             => RTMEDIA_CUBEPOINTS_VERSION,
	'rt_item_name'                   => EDD_RTMEDIA_CUBEPOINTS_ITEM_NAME,
	'rt_license_hook'                => 'rtmedia_license_tabs',
	'rt_product_text_domain'         => 'rtmedia',
);

new RTEDDLicense_rtmedia_cubepoints( $rtmedia_cubepoints_details );

/*
 * One click install/activate rtMedia.
 */
include_once( RTMEDIA_CUBEPOINTS_PATH . 'lib/plugin-installer/RTMPluginInstaller.php' );

global $rtm_plugin_installer;

if ( empty( $rtm_plugin_installer ) ) {
	$rtm_plugin_installer = new RTMPluginInstaller();
}

/**
 * Add Settings/Docs link to plugins area.
 *
 * @since 1.1.4
 *
 * @param array  $links Links array in which we would prepend our link.
 * @param string $file Current plugin basename.
 *
 * @return array Processed links.
 */
function rtmedia_cubepoints_action_links( $links, $file ) {
	// Return normal links if not plugin.
	if ( plugin_basename( __FILE__ ) !== $file ) {
		return $links;
	}

	// Add a few links to the existing links array.
	return array_merge( $links, array(
		'settings' => '<a href="' . esc_url( admin_url( 'admin.php?page=rtmedia-settings#rtmedia-cubepoints' ) ) . '">' . esc_html__( 'Settings', 'rtmedia' ) . '</a>',
		'docs'     => '<a target="_blank" href="' . esc_url( 'https://rtmedia.io/docs/addons/cubepoints/' ) . '">' . esc_html__( 'Docs', 'rtmedia' ) . '</a>',
	) );
}

add_filter( 'plugin_action_links', 'rtmedia_cubepoints_action_links', 11, 2 );
add_filter( 'network_admin_plugin_action_links', 'rtmedia_cubepoints_action_links', 11, 2 );
