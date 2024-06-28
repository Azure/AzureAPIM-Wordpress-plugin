<?php

/**
 * Fired during plugin activation
 *
 * @link       https://-
 * @since      1.0.0
 *
 * @package    Apim_Devportal
 * @subpackage Apim_Devportal/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Apim_Devportal
 * @subpackage Apim_Devportal/includes
 * @author     Microsoft <janmach1@microsoft.com>
 */
class Apim_Devportal_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		//Ensure that no leftover data is present
		delete_option('apim_page_on_front_backup');
		delete_option('apim_show_on_front_backup');

		add_option('apim_service_name', '');
		add_option('create_apim_pages', '0');
		add_option('create_apim_nav_menu', '0');

		require_once plugin_dir_path( __FILE__ ) . '../admin/class-apim-devportal-admin-utilities.php';
		Apim_Devportal_Admin_Utilities::update_permalinks_structure();
		Apim_Devportal_Admin_Utilities::set_page_as_draft_by_path('sample-page');
		Apim_Devportal_Admin_Utilities::update_default_pages_and_menu();
	}
}
