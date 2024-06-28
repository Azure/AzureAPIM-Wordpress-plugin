<?php

if ( ! defined( 'WPINC' ) ) {
	die;
}

add_action( 'admin_menu', 'apimdevportal_init_menu' );
add_action( 'admin_enqueue_scripts', 'apimdevportal_admin_enqueue_scripts' );

/**
 * Init Admin Menu.
 *
 * @return void
 */
function apimdevportal_init_menu() {
    add_menu_page(
        __( 'Azure API Management Developer Portal', 'apimdevportal' ),
        __( 'Azure API Management Developer Portal', 'apimdevportal' ),
        'manage_options',
        'apimdevportal',
        'apimdevportal_admin_page',
        'dashicons-admin-post',
        '2.1'
    );
}

/**
 * Enqueue scripts and styles.
 *
 * @return void
 */
function apimdevportal_admin_enqueue_scripts() {
    wp_enqueue_style( 'apimdevportal-admin-style', plugin_dir_url( __FILE__ ) . '../build/admin.css' );
    wp_enqueue_script( 'apimdevportal-admin-script', plugin_dir_url( __FILE__ ) . '../build/admin.js', array( 'wp-element' ), '1.0.0', true );
}

/**
 * Init Admin Page.
 *
 * @return void
 */
function apimdevportal_admin_page()
{
    if (isset($_POST['save_apim_settings'])) {
        if (!current_user_can('manage_options')) {
            print 'User cannot manage options.';
            exit;
        }

        $update_result = true;
        $apim_service_name_input = '';
        if (isset($_POST['apim_service_name'])) {
            $apim_service_name_input = sanitize_text_field($_POST['apim_service_name']);
        }

        $create_apim_pages_input = '0';
        if (isset($_POST['create_apim_pages'])) {
            $create_apim_pages_input = sanitize_text_field($_POST['create_apim_pages']);
        }

        $create_apim_nav_menu_input = '0';
        if (isset($_POST['create_apim_nav_menu'])) {
            $create_apim_nav_menu_input = sanitize_text_field($_POST['create_apim_nav_menu']);
        }

        $update_result = insert_sanitized_option('apim_service_name', $apim_service_name_input) && $update_result;
        $update_result = insert_sanitized_option('create_apim_pages', $create_apim_pages_input) && $update_result;
        $update_result = insert_sanitized_option('create_apim_nav_menu', $create_apim_nav_menu_input) && $update_result;

        require_once plugin_dir_path( __FILE__ ) . 'class-apim-devportal-admin-utilities.php';
        Apim_Devportal_Admin_Utilities::update_default_pages_and_menu();
        apim_settings_saved_success_notice($update_result);
    }

    
    ob_start();
    ?>
        <div class="wrap">
            <h1>APIM Settings</h1>
            <form method="post">
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="apim_service_name">APIM service name</label></th>
                        <td>
                            <div style="position: relative; width: 300px;">
                                <input type="text" style="width: 100%;" name="apim_service_name" id="apim_service_name" value="<?= get_option('apim_service_name', '') ?>" class="regular-text" required/>
                                <label for="apim_service_name" style="position: absolute; bottom: -20px; right: 0; text-align: right; font-size: 0.9em;">.developer.azure-api.net</label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="create_apim_pages">Create default pages</label></th>
                        <td>
                            <div style="position: relative; width: 600px;">	
                                <?php $create_apim_pages_db = get_option('create_apim_pages', '0'); ?>
                                <input type="checkbox" name="create_apim_pages" id="create_apim_pages" value="<?= $create_apim_pages_db ?>" onclick="handleCheckBoxClick(this);" 
                                <?php checked( $create_apim_pages_db, '1' ); ?> />
                                <p class="description" align="justify">Enabling this option wil create the following pages:<b> /home, /apis, /api-details, /products, /product-details, /profile</b>. To ensure proper functioning of the plugin, make sure that there are no custom pages with the same path.</p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="create_apim_nav_menu">Create navigation menu (primary)</label></th>
                        <td>
                            <div style="position: relative; width: 600px;">
                                <?php $create_apim_nav_menu_db = get_option('create_apim_nav_menu', '0'); ?>
                                <input type="checkbox" name="create_apim_nav_menu" id="create_apim_nav_menu" value="<?= $create_apim_nav_menu_db ?>" onclick="handleCheckBoxClick(this);" 
                                <?php checked( $create_apim_nav_menu_db, '1' ); ?> />
                                <p class="description" align="justify">Creates a custom navigation menu with the following pages:<b> /home, /apis, /products</b>. Adds it to the theme's 'primary' location, if supported.</p>
                            </div>
                        </td>
                    </tr>
                </table>
                <p class="submit"><input type="submit" name="save_apim_settings" id="save_apim_settings" class="button button-primary" value="Save Changes"  /></p>
            </form>
        </div>
        <script>
            function handleCheckBoxClick(cb) {
                cb.value = cb.checked ? '1' : '0';
            }
        </script>
        <?php
    echo ob_get_clean();
}

function insert_sanitized_option($option_name, $sanitized_value) {
    $current_value = get_option($option_name);
    if (isset($sanitized_value)) {
        if ($current_value !== $sanitized_value) {
            update_option($option_name, $sanitized_value);
        }
        return true;
    }
    return false;
}

function apim_settings_saved_success_notice($status) {
    if ($status === true) {
         ?>
            <div class="notice notice-success is-dismissible">
                <p><?php echo 'Settings saved.'; ?></p>
            </div>
        <?php
    } else {
         ?>
            <div class="notice notice-error is-dismissible">
                <p><?php echo 'Error occurred while saving settings.'; ?></p>
            </div>
        <?php
    }
}

