<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://-
 * @since      1.0.0
 *
 * @package    Apim_Devportal
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete the APIM service name from options table
delete_option('apim_service_name');
delete_option('create_apim_pages');
delete_option('create_apim_nav_menu');

require_once plugin_dir_path( __FILE__ ) . 'admin/class-apim-devportal-admin-utilities.php';
Apim_Devportal_Admin_Utilities::delete_custom_apim_menu();
Apim_Devportal_Admin_Utilities::delete_default_apim_pages();
