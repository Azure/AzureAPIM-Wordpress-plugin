<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://-
 * @since      1.0.0
 *
 * @package    Apim_Devportal
 * @subpackage Apim_Devportal/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Apim_Devportal
 * @subpackage Apim_Devportal/includes
 * @author     Microsoft <janmach1@microsoft.com>
 */
class Apim_Devportal_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		require_once plugin_dir_path( __FILE__ ) . '../admin/class-apim-devportal-admin-utilities.php';
		Apim_Devportal_Admin_Utilities::delete_custom_apim_menu();
		Apim_Devportal_Admin_Utilities::unpublish_default_apim_pages();
	}
}
