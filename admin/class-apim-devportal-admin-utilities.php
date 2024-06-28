<?php

/**
 * 
 * This class defines utility functions for plugin admin side operations.
 *
 * @since      1.0.0
 * @package    Apim_Devportal
 * @subpackage Apim_Devportal/admin
 * @author     Microsoft <zmohammed@microsoft.com>
 */
class Apim_Devportal_Admin_Utilities {

	public static function update_default_pages_and_menu() {
        $create_apim_pages_option = get_option('create_apim_pages', '0');
        $create_apim_nav_menu_option = get_option('create_apim_nav_menu', '0');

        if ($create_apim_pages_option === '1') {
            self::create_default_apim_pages();

            if ($create_apim_nav_menu_option === '1') {
                self::create_custom_apim_menu();
            } else {
                self::delete_custom_apim_menu();
            }

        } else {
            self::delete_custom_apim_menu();
            self::unpublish_default_apim_pages();
        }
	}
    
	public static function create_default_apim_pages() {
		require_once plugin_dir_path( __FILE__ ) . 'class-apim-devportal-admin-page-contents.php';
		$new_home_page_id = self::create_and_publish_page('apim_welcome_page_id', 'Home', Apim_Devportal_Page_Contents::getHomePageContents());
		$result = true;
	
		// Set the new home page as the front page
		if ($new_home_page_id !== null && !is_wp_error($new_home_page_id)) {

			//Set only once when the home page is created
			$apim_show_on_front_backup = get_option('apim_show_on_front_backup');
			if ($apim_show_on_front_backup === false || $apim_show_on_front_backup === '') {
				$curr_home_page_id = get_option('page_on_front');
				$curr_show_on_front = get_option('show_on_front');

				update_option('apim_page_on_front_backup', $curr_home_page_id);
				update_option('apim_show_on_front_backup', $curr_show_on_front);

				update_option('page_on_front', $new_home_page_id);
				update_option('show_on_front', 'page');
			}
		} else {
			$result = false;
		}
	
		$result = !is_wp_error(self::create_and_publish_page('apim_api_list_page_id', 'APIs', Apim_Devportal_Page_Contents::getAPIsPageContents())) && $result;
		$result = !is_wp_error(self::create_and_publish_page('apim_product_list_page_id', 'Products', Apim_Devportal_Page_Contents::getProductsPageContents())) && $result;
		// $result = !is_wp_error(self::create_and_publish_page('apim_login_page_id', 'Login', Apim_Devportal_Page_Contents::getLoginPageContents())) && $result;
		// $result = !is_wp_error(self::create_and_publish_page('apim_logout_page_id', 'Logout', Apim_Devportal_Page_Contents::getLogoutOPageContents())) && $result;
		$result = !is_wp_error(self::create_and_publish_page('apim_api_detail_page_id', 'API Details', Apim_Devportal_Page_Contents::getAPIDetailsPageContents())) && $result;
		$result = !is_wp_error(self::create_and_publish_page('apim_product_detail_page_id', 'Product Details', Apim_Devportal_Page_Contents::getProductDetailsPageContents())) && $result;
		$result = !is_wp_error(self::create_and_publish_page('apim_profile_page_id', 'Profile', Apim_Devportal_Page_Contents::getProfilePageContents())) && $result;
		return $result;
	}
	
	public static function unpublish_default_apim_pages() {
		// restore the default home page
		self::restore_original_home_page();

		// set the default apim pages as draft
		self::set_page_as_draft('apim_welcome_page_id');
		self::set_page_as_draft('apim_api_list_page_id');
		self::set_page_as_draft('apim_product_list_page_id');
		// self::set_page_as_draft('apim_login_page_id');
		// self::set_page_as_draft('apim_logout_page_id');
		self::set_page_as_draft('apim_api_detail_page_id');
		self::set_page_as_draft('apim_product_detail_page_id');
		self::set_page_as_draft('apim_profile_page_id');
	}

	public static function delete_default_apim_pages() {
		// restore the default home page
		self::restore_original_home_page();

		// delete the default apim pages
		self::delete_page('apim_welcome_page_id');
		self::delete_page('apim_api_list_page_id');
		self::delete_page('apim_product_list_page_id');
		// self::delete_page('apim_login_page_id');
		// self::delete_page('apim_logout_page_id');
		self::delete_page('apim_api_detail_page_id');
		self::delete_page('apim_product_detail_page_id');
		self::delete_page('apim_profile_page_id');

		//delete the corresponding options
		delete_option('apim_welcome_page_id');
		delete_option('apim_api_list_page_id');
		delete_option('apim_product_list_page_id');
		// delete_option('apim_login_page_id');
		// delete_option('apim_logout_page_id');
		delete_option('apim_api_detail_page_id');
		delete_option('apim_product_detail_page_id');
		delete_option('apim_profile_page_id');
	}
	
	public static function restore_original_home_page() {
		$original_home_page_id = get_option('apim_page_on_front_backup');
		$original_show_on_front = get_option('apim_show_on_front_backup');

		if ($original_home_page_id !== false && $original_home_page_id !== '') {
			update_option('page_on_front', $original_home_page_id);
			delete_option('apim_page_on_front_backup');
		}

		if ($original_show_on_front !== false && $original_show_on_front !== '') {
			update_option('show_on_front', $original_show_on_front);
			delete_option('apim_show_on_front_backup');
		}
	}

	public static function create_and_publish_page($page_identifier_option, $page_title, $page_content) {
		$page_id = get_option($page_identifier_option);
		$page_exists = false;
		$is_published = false;
	
		if ($page_id !== false && $page_id !== '') {
			// Check if the page exists
			$page = get_post($page_id);
			if ($page && $page !== null) {
				$page_exists = true;
				//Check if the page is published
				$is_published = $page->post_status === 'publish';
			}
		}
	
		if (!$page_exists) {
			// Create a new page
			$page_id = wp_insert_post(array(
				'post_title'   => $page_title,
				'post_content' => $page_content,
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'page_template' => '',
			));
	
			if (!is_wp_error($page_id)) {
				update_option($page_identifier_option, $page_id);
			}
			return $page_id;
	
		} else if ($page_exists && !$is_published) {
			// Publish the page
			$page_data = get_post($page_id);
			$page_data->post_status = 'publish';
			return wp_update_post($page_data);
		}
	
		return $page_id;
	}

	public static function delete_custom_apim_menu() {
		$menu_name = 'apim-custom-menu';
		$menu = wp_get_nav_menu_object($menu_name);

		if ($menu) {
			// Delete the navigation menu
			wp_delete_nav_menu($menu->term_id);
		
			// Remove the menu location
			//$locations = get_theme_mod('nav_menu_locations', array());
			//unset($locations['primary']);
			//set_theme_mod('nav_menu_locations', $locations);
		}
	}
	
	public static function create_custom_apim_menu() {
		$menu_name = 'apim-custom-menu';
		$menu = wp_get_nav_menu_object($menu_name);

		if (!$menu) {
			$menu_id = wp_create_nav_menu($menu_name);
			self::add_page_to_apim_menu($menu_id, 'apim_welcome_page_id');
			self::add_page_to_apim_menu($menu_id, 'apim_api_list_page_id');
			self::add_page_to_apim_menu($menu_id, 'apim_product_list_page_id');
			// self::add_page_to_apim_menu($menu_id, 'apim_login_page_id');
			// self::add_page_to_apim_menu($menu_id, 'apim_logout_page_id');
	
			$locations = get_theme_mod('nav_menu_locations', array());
			$locations['primary'] = $menu_id;
			set_theme_mod('nav_menu_locations', $locations);

			// Check if the navigation post exists and update it's content
			$navigation_post = get_page_by_path('navigation', OBJECT, 'wp_navigation');
			if ($navigation_post) {
				require_once plugin_dir_path( __FILE__ ) . 'class-apim-devportal-admin-page-contents.php';
				$navigation_post->post_content = Apim_Devportal_Page_Contents::getNavigationPostContents();
				wp_update_post($navigation_post);
			}
		}    
	}
	
	public static function add_page_to_apim_menu($menu_id, $page_identifier_option) {
		$page_id = get_option($page_identifier_option);
		if ($page_id !== false && $page_id !== '') {
			$menu_item_data = array(
				'menu-item-object-id' => $page_id,
				'menu-item-object' => 'page',
				'menu-item-type' => 'post_type',
				'menu-item-status' => 'publish',
			);
			wp_update_nav_menu_item($menu_id, 0, $menu_item_data);    
		}
	}

	public static function set_page_as_draft($page_identifier_option) {
		$page_id = get_option($page_identifier_option);
		if ($page_id !== false && $page_id !== '') {

			$page_data = get_post($page_id);
			if ($page_data && $page_data !== null && $page_data->post_status !== 'draft') {
				$page_data->post_status = 'draft';
				wp_update_post($page_data);
			}
		}
	}

	public static function set_page_as_draft_by_path($page_path) {
		$page_data = get_page_by_path($page_path);
		if ($page_data && $page_data !== null && $page_data->post_status !== 'draft') {
			$page_data->post_status = 'draft';
			wp_update_post($page_data);
		}
	}

	public static function delete_page($page_identifier_option) {
		$page_id = get_option($page_identifier_option);
		// Delete the created page
		if ($page_id !== false && $page_id !== '') {
			wp_delete_post($page_id, true);
		}
		// Delete the option record
		delete_option($page_identifier_option);
	}

	public static function update_permalinks_structure() {
		// Update the permalink structure
		$new_permalink_structure = '/%postname%/';
		update_option('permalink_structure', $new_permalink_structure);

		// Update and flush the rewrite rules
		if (isset($wp_rewrite)) {
			$wp_rewrite->set_permalink_structure($new_permalink_structure);
			$wp_rewrite->flush_rules();
		}
	}
}
