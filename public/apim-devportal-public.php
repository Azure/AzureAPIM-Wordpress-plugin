<?php

if ( ! defined( 'WPINC' ) ) {
	die;
}

function get_sas_token($apim_service_name, $idToken) {
	$url = 'https://'.$apim_service_name.'.developer.azure-api.net/developer/identity?api-version=2022-04-01-preview';
	$headers = array(
		'Authorization: Aad id_token="' . $idToken . '"'
	);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, true); // Set to true to retrieve headers
	$response = curl_exec($ch);
	$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	$sas_token='';
	$user_uid='';
	if ($response !== false) {
		$headers = array();
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header_string = substr($response, 0, $header_size);
		$body_string = substr($response, $header_size);

		if (!empty($body_string)) {
			$decoded_body = json_decode($body_string, true);
			if ($decoded_body !== null && isset($decoded_body["id"])) {
				$user_uid = $decoded_body["id"];
			}
		}

		$header_array = explode("\r\n", $header_string);
		foreach ($header_array as $header) {
			if (!empty($header)) {
				$header_parts = explode(':', $header);
				if (count($header_parts) >= 2 && strtolower(trim($header_parts[0])) === 'ocp-apim-sas-token') {
					$sas_token = trim($header_parts[1]);
				}
			}
		}
	}
	curl_close($ch);
	return array('sas_token' => $sas_token, 'user_uid' => $user_uid);
}

function remove_sas_cookies() {
	$wp_site_url = get_site_url();
	$site_url_parts = parse_url($wp_site_url);
	$cookie_domain = $site_url_parts['host'];
	$expiry = time() - 3600;
	setcookie('Ocp-Apim-Sas-Token', '', $expiry, "/", $cookie_domain, true);
	setcookie('Ocp-Apim-User-Id', '', $expiry, "/", $cookie_domain, true);
}

function parse_user_claims($payload_b64) {
	$payload_data = json_decode(base64_decode($payload_b64), true);
	if (empty($payload_data) || !is_array($payload_data)) {
		return '';
	}

	//Extract email and use backup fields to fill it, if not present
	$email = isset($payload_data['email']) ? $payload_data['email'] : '';
	if (empty($email)) {
		$preferred_username = isset($payload_data['preferred_username']) ? $payload_data['preferred_username'] : '';
		if (!empty($preferred_username) && strpos($preferred_username, '@') !== false) {
			$email = $preferred_username;
		}

		if (empty($email)) {
			$unique_name = isset($payload_data['unique_name']) ? $payload_data['unique_name'] : '';
			if (!empty($unique_name) && strpos($unique_name, '@') !== false) {
				if (strpos($unique_name, '#') !== false) {
					$email = explode('#', $unique_name)[1];
				} else {
					$email = $unique_name;
				}
			}
		}
	}

	// Extract the first name and use backup fields to fill it, if not present
	$firstName = isset($payload_data['given_name']) ? $payload_data['given_name'] : '';
	if (empty($firstName)) {
		$firstName = isset($payload_data['name']) ? $payload_data['name'] : '';
		if (empty($firstName) && !empty($email)) {
			$firstName = $email.explode('@', $email)[0];
		}
	}

	// Extract the last name and use backup fields to fill it, if not present
	$lastName = isset($payload_data['family_name']) ? $payload_data['family_name'] : '';
	if (empty($lastName)) {
		$lastName = isset($payload_data['name']) ? $payload_data['name'] : '';
		if (empty($lastName) && !empty($firstName)) {
			$lastName = $firstName;
		}
		if (empty($lastName) && !empty($email)) {
			$lastName = $email.explode('@', $email)[0];
		}
	}

	// Extract the oid claim
	$id = isset($payload_data['oid']) ? $payload_data['oid'] : '';
	$provider = "Aad";

	// Validate if the required claims are present
	if (empty($firstName) || empty($lastName) || empty($email) || empty($id)) {
		return '';
	}

	return json_encode([
		"firstName" => $firstName,
		"lastName" => $lastName,
		"email" => $email,
		"identities" => [[
			"id" => $id,
			"provider" => $provider
		]]
	]);
}

function add_apim_user($apim_service_name, $idToken) {
	$token_parts = explode('.', $idToken);
	if ($token_parts === false || count($token_parts) !== 3 || empty($token_parts[1])) {
		return;
	}
	$user_details=parse_user_claims($token_parts[1]);
	if (empty($user_details)) {
		return;
	}
	$url = 'https://'.$apim_service_name.'.developer.azure-api.net/mapi/users?api-version=2023-03-01-preview';
	$headers = array(
		'Authorization: Aad id_token="' . $idToken . '"',
		'Content-Type:application/json',
	);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $user_details);

	$response = curl_exec($ch);
	$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	return $status_code;
}

// Set SAS token and user id as cookies for authenticated users
// Create user if not already created
function set_sastoken_cookie() {

	if (!isset($_COOKIE['Ocp-Apim-Sas-Token']) || !isset($_COOKIE['Ocp-Apim-User-Id'])) {
    		$apim_service_name=get_option('apim_service_name', '');

			$idToken = null;
			if (array_key_exists('HTTP_X_MS_TOKEN_AAD_ID_TOKEN', $_SERVER)) {
				$idToken = $_SERVER['HTTP_X_MS_TOKEN_AAD_ID_TOKEN'];
			}
			// $accessToken='';
			// if (array_key_exists('HTTP_X_MS_TOKEN_AAD_ACCESS_TOKEN', $_SERVER)) {
			// 	$accessToken = $_SERVER['HTTP_X_MS_TOKEN_AAD_ACCESS_TOKEN'];
			// }

			if (isset($apim_service_name) && $apim_service_name !== '' && isset($idToken) &&  $idToken !== '') {
				$token_data = get_sas_token($apim_service_name, $idToken);
				if (empty($token_data['sas_token']) || empty($token_data['user_uid'])) {
					add_apim_user($apim_service_name, $idToken);
				}

				$token_data=get_sas_token($apim_service_name, $idToken);
				$sas_token = $token_data['sas_token'];
				$user_uid = $token_data['user_uid'];

				if (empty($sas_token) || empty($user_uid)) {
					remove_sas_cookies();
					return;
				}

				$wp_site_url = get_site_url();
				$site_url_parts = parse_url($wp_site_url);
				$cookie_domain = $site_url_parts['host'];
				$expiry = time() + 1800;
				setcookie('Ocp-Apim-Sas-Token', $sas_token, $expiry, "/", $cookie_domain, true);
				setcookie('Ocp-Apim-User-Id', $user_uid, $expiry, "/", $cookie_domain, true);

			} else {
				remove_sas_cookies();
			}

	} else if ((isset($_COOKIE['Ocp-Apim-Sas-Token']) || isset($_COOKIE['Ocp-Apim-User-Id'])) && (!isset($_SERVER['HTTP_X_MS_TOKEN_AAD_ID_TOKEN']) || $_SERVER['HTTP_X_MS_TOKEN_AAD_ID_TOKEN'] === '')) {
        // Remove cookies if the user is not authenticated
		remove_sas_cookies();
	}
}
add_action( 'wp', 'set_sastoken_cookie');


/**************************************************************************
		Disabling default /login & /logout pages
***************************************************************************
// Styling and script for the login component
function enqueue_login_page_script() {
    wp_enqueue_style( 'login_page-style', plugin_dir_url( __FILE__ ) . 'css/login_page.css' );
    wp_enqueue_script(
        'login_page-script',
        plugin_dir_url( __FILE__ ) . 'js/login_page.js',
        array('wp-element'),
        '1.0.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'enqueue_login_page_script');


// Define the login button 
function create_apim_login_button() {
    ob_start();
    ?>
	<div>
		<a href="/.auth/login/aad" class="login-button">
    		<span class="icon-svg-aad"></span>
   			 Azure Active Directory
		</a>
	</div>
    <?php
    return ob_get_clean();
}
add_shortcode('Login_Button', 'create_apim_login_button');


// Redirect logout page to /.auth/logout 
function apim_logout_redirect() {
    if (is_page('logout')) {
        wp_redirect('/.auth/logout');
        exit();
    }
}
add_action('template_redirect', 'apim_logout_redirect');


// Update visibility of login/logout pages in apim navigation menu based on user authentication
function update_apim_menu_visibility($items, $args) {
	if ($args->menu->name !== 'apim-custom-menu') {
        return $items;
    }
	
	$page_id=null;
	if (!isset($_SERVER['HTTP_X_MS_TOKEN_AAD_ID_TOKEN']) || $_SERVER['HTTP_X_MS_TOKEN_AAD_ID_TOKEN'] === '') {
		$page_id=get_option('apim_logout_page_id');
	} else {
		$page_id=get_option('apim_login_page_id');
	}

	if ($page_id) {
		foreach ($items as $key => $item) {
			if ($item->object_id == $page_id) {
				unset($items[$key]);
				break;
			}
		}
	}

	return $items;
}
add_filter('wp_nav_menu_objects', 'update_apim_menu_visibility', 10, 2);
***************************************************************************
		Disabling default /login & /logout pages
***************************************************************************/
