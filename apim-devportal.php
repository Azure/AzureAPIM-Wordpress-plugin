<?php
/**
 * Plugin Name:       Azure API Management Developer Portal
 * Description:       A plug-in to integrate your API Management service.
 * Requires at least: 5.8
 * Requires PHP:      7.0
 * Version:           0.1.2
 * Author:            Microsoft
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       apim-devportal
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'APIM_DEVPORTAL_VERSION', '0.1.2' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-apim-devportal-activator.php
 */
function activate_apim_devportal() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-apim-devportal-activator.php';
	Apim_Devportal_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-apim-devportal-deactivator.php
 */
function deactivate_apim_devportal() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-apim-devportal-deactivator.php';
	Apim_Devportal_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_apim_devportal' );
register_deactivation_hook( __FILE__, 'deactivate_apim_devportal' );


// function apim_custom_menu_order($query) {
//     // Make sure we show menu in order of Home, APIs, Products, Login
//     if (!is_admin() && $query->get('post_type') == 'page') {
//         $query->set('orderby', 'ID');
//         $query->set('order', 'ASC');
//     }
// }

function use_apim_custom_menu() {
    // Check if the apim menu exists
    $menu_object = wp_get_nav_menu_object('apim-custom-menu');
    if ($menu_object) {
        $menu_id = $menu_object->term_id;
        $locations = get_theme_mod('nav_menu_locations', array());

        // Set the apim menu as the primary menu
        $locations['primary'] = $menu_id;
        set_theme_mod('nav_menu_locations', $locations);
    }

    // Check if the navigation post exists and update it's content
    $navigation_post = get_page_by_path('navigation', OBJECT, 'wp_navigation');
    if ($navigation_post) {
        require_once plugin_dir_path( __FILE__ ) . 'admin/class-apim-devportal-admin-page-contents.php';
        $navigation_post->post_content = Apim_Devportal_Page_Contents::getNavigationPostContents();
        wp_update_post($navigation_post);
    }
    
}

//add_action('pre_get_posts', 'apim_custom_menu_order');
add_action('after_switch_theme', 'use_apim_custom_menu');


/**
 * Initialize the admin menu
 */
require_once plugin_dir_path( __FILE__ ) . 'admin/apim-devportal-admin.php';


/**
 * shortcode for apis list handling
 */
function apis_list_widget() {
    return '<div id="apim-apis-list"></div>';
}
add_shortcode('APIs_List', 'apis_list_widget');

function enqueue_apis_list_widget_script() {
    wp_enqueue_style( 'apis_list_widget-style', plugin_dir_url( __FILE__ ) . 'build/apisList.css' );
    wp_enqueue_script(
        'apis_list_widget-script',
        plugin_dir_url( __FILE__ ) . 'build/apisList.js',
        array('wp-element'),
        APIM_DEVPORTAL_VERSION,
        true // Load script in footer
    );

    // Passing data to javascript - Method 1
    $apim_service_name=get_option('apim_service_name', '');
    wp_add_inline_script( 'apis_list_widget-script', 'var apim_service_name = \'' . $apim_service_name . '\';' , 'before');

    // Pass data to JavaScript - Method 2
    // wp_localize_script('apis_list_widget-script', 'apimData', array(
    //     'apim_service_name' => $apim_service_name,
    //     'ajax_url' => admin_url('admin-ajax.php'),
    // ));
}
add_action('wp_enqueue_scripts', 'enqueue_apis_list_widget_script');


/**
 * shortcode for api detail handling
 */
function api_detail_widget() {
    return '<div id="apim-api-details"></div>';
}
add_shortcode('API_Detail', 'api_detail_widget');

function enqueue_api_detail_widget_script() {
    wp_enqueue_style( 'api_detail_widget-style', plugin_dir_url( __FILE__ ) . 'build/apiDetail.css' );
    wp_enqueue_script(
        'api_detail_widget-script',
        plugin_dir_url( __FILE__ ) . 'build/apiDetail.js',
        array('wp-element'),
        APIM_DEVPORTAL_VERSION,
        true // Load script in footer
    );

    // Passing data to javascript
    $apim_service_name=get_option('apim_service_name', '');
    wp_add_inline_script( 'apis_list_widget-script', 'var apim_service_name = \'' . $apim_service_name . '\';' , 'before');
}
add_action('wp_enqueue_scripts', 'enqueue_api_detail_widget_script');


/**
* shortcode for products list handling
*/
function products_list_widget() {
    return '<div id="apim-products-list"></div>';
}
add_shortcode('Products_List', 'products_list_widget');

function enqueue_products_list_widget_script() {
    wp_enqueue_style( 'products_list_widget-style', plugin_dir_url( __FILE__ ) . 'build/productsList.css' );
    wp_enqueue_script(
        'products_list_widget-script',
        plugin_dir_url( __FILE__ ) . 'build/productsList.js',
        array('wp-element'),
        APIM_DEVPORTAL_VERSION,
        true // Load script in footer
    );

    // Passing data to javascript
    $apim_service_name=get_option('apim_service_name', '');
    wp_add_inline_script( 'products_list_widget-script', 'var apim_service_name = \'' . $apim_service_name . '\';' , 'before');
}
add_action('wp_enqueue_scripts', 'enqueue_products_list_widget_script');


/**
 * shortcode for product detail handling
 */
function product_detail_widget() {
    return '<div id="apim-product-details"></div>';
}
add_shortcode('Product_Detail', 'product_detail_widget');

function enqueue_product_detail_widget_script() {
    wp_enqueue_style( 'product_detail_widget-style', plugin_dir_url( __FILE__ ) . 'build/productDetail.css' );
    wp_enqueue_script(
        'product_detail_widget-script',
        plugin_dir_url( __FILE__ ) . 'build/productDetail.js',
        array('wp-element'),
        APIM_DEVPORTAL_VERSION,
        true // Load script in footer
    );

    // Passing data to javascript
    $apim_service_name=get_option('apim_service_name', '');
    wp_add_inline_script( 'product_detail_widget-style', 'var apim_service_name = \'' . $apim_service_name . '\';' , 'before');
}
add_action('wp_enqueue_scripts', 'enqueue_product_detail_widget_script');

/**
 * shortcode for users profile
 */
function profile_widget() {
    return '<div id="apim-profile"></div>';
}
add_shortcode('Profile', 'profile_widget');

function enqueue_profile_widget_script() {
    wp_enqueue_style( 'profile_widget-style', plugin_dir_url( __FILE__ ) . 'build/profile.css' );
    wp_enqueue_script(
        'profile_widget-script',
        plugin_dir_url( __FILE__ ) . 'build/profile.js',
        array('wp-element'),
        APIM_DEVPORTAL_VERSION,
        true // Load script in footer
    );

    // Passing data to javascript
    $apim_service_name=get_option('apim_service_name', '');
    wp_add_inline_script( 'profile_widget-style', 'var apim_service_name = \'' . $apim_service_name . '\';' , 'before');
}
add_action('wp_enqueue_scripts', 'enqueue_profile_widget_script');

/**
 * shortcode for sign in / profile button
 */
function signin_widget() {
    return '<nav id="apim-signIn"></nav>';
}
add_shortcode('SignInButton', 'signin_widget');

function enqueue_signin_widget_script() {
    wp_enqueue_style( 'signin_widget-style', plugin_dir_url( __FILE__ ) . 'build/signInButton.css' );
    wp_enqueue_script(
        'signin_widget-script',
        plugin_dir_url( __FILE__ ) . 'build/signInButton.js',
        array('wp-element'),
        APIM_DEVPORTAL_VERSION,
        true // Load script in footer
    );

    // Passing data to javascript
    $apim_service_name=get_option('apim_service_name', '');
    wp_add_inline_script( 'signin_widget-style', 'var apim_service_name = \'' . $apim_service_name . '\';' , 'before');
}
add_action('wp_enqueue_scripts', 'enqueue_signin_widget_script');


/*
 * Try hiding page title from theme
 */
function disable_title_css() {

    $options_array = get_options(array(
        'apim_welcome_page_id',
        'apim_api_list_page_id',
        'apim_product_list_page_id',
        'apim_api_detail_page_id',
        'apim_product_detail_page_id',
        'apim_profile_page_id',
        //'apim_login_page_id';
		//'apim_logout_page_id';
    ));

    if (!empty($options_array)) {
        $page_id_array = array_values($options_array);
        if (is_page($page_id_array)) {
            // Adding commonly used css classes to hide title. It may not work for all themes.
            echo '<style>.wp-block-post-title { display: none; }</style>';
            echo '<style>.entry-title { display: none; }</style>';
            echo '<style>.page-title { display: none; }</style>';
            echo '<style>.page-header-title { display: none; }</style>';
        }
    }
}
add_action('wp_head', 'disable_title_css');


/**
 * Create login/logout pages and enable AAD authentication
 */
require_once plugin_dir_path( __FILE__ ) . 'public/apim-devportal-public.php';