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
class Apim_Devportal_Page_Contents {

	public static function getHomePageContents() {
		return '<!-- wp:buttons -->
		<div class="wp-block-buttons"><!-- wp:button /--></div>
		<!-- /wp:buttons -->

		<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50","right":"var:preset|spacing|50"},"margin":{"top":"0","bottom":"0"}}},"backgroundColor":"base","layout":{"type":"constrained"}} -->
		<div class="wp-block-group alignwide has-base-background-color has-background" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)"><!-- wp:columns {"verticalAlignment":"center","align":"wide","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|50","left":"var:preset|spacing|50"}}}} -->
		<div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"50%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:50%"><!-- wp:heading -->
		<h2 class="wp-block-heading">Welcome to the developer portal</h2>
		<!-- /wp:heading -->

		<!-- wp:list {"style":{"typography":{"lineHeight":"1.75"}},"className":"is-style-checkmark-list"} -->
		<ul style="line-height:1.75" class="is-style-checkmark-list"><!-- wp:list-item -->
		<li>Find comprehensive API documentation.</li>
		<!-- /wp:list-item -->

		<!-- wp:list-item -->
		<li>Integrate our services into your applications.</li>
		<!-- /wp:list-item -->

		<!-- wp:list-item -->
		<li>Accelerate your application\'s development process.</li>
		<!-- /wp:list-item --></ul>
		<!-- /wp:list -->

		<!-- wp:buttons -->
		<div class="wp-block-buttons"><!-- wp:button -->
		<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/apis">Search APIs</a></div>
		<!-- /wp:button -->

		<!-- wp:button {"className":"is-style-outline"} -->
		<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="/products">View our products</a></div>
		<!-- /wp:button --></div>
		<!-- /wp:buttons --></div>
		<!-- /wp:column -->

		<!-- wp:column {"verticalAlignment":"center","width":"50%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:50%"></div>
		<!-- /wp:column --></div>
		<!-- /wp:columns --></div>
		<!-- /wp:group -->';
	}

	public static function getAPIsPageContents() {
		return '
		<!-- wp:heading -->
		<h2 class="wp-block-heading">APIs</h2>
		<!-- /wp:heading -->

		<p style="padding: 0.5em 0 2em">List of APIs available</p>
		<p>[APIs_List]</p>'; // Adding short-code for APIs List widget
	}

	public static function getProductsPageContents() {
		return '
		<!-- wp:heading -->
		<h2 class="wp-block-heading">Products</h2>
		<!-- /wp:heading -->
		
		<p style="padding: 0.5em 0 2em">List of products available.</p>
		<p>[Products_List]</p>'; // Adding short-code for Products List widget
	}

	public static function getAPIDetailsPageContents() {
		// Adding short-code for API Detail widget
		return '[API_Detail]';
	}

	public static function getProductDetailsPageContents() {
		// Adding short-code for Product Detail widget
		return '[Product_Detail]';
	}

	public static function getLoginPageContents() {
		// Adding short-code for Login Button widget
		return '[Login_Button]';
	}

	public static function getLogoutOPageContents() {
		return '';
	}

	public static function getProfilePageContents() {
		return '[Profile]';
	}

	public static function getNavigationPostContents() {
		$wp_site_url = get_site_url();
		$welcome_page_id = get_option('apim_welcome_page_id');
		$api_list_page_id = get_option('apim_api_list_page_id');
		$product_list_page_id = get_option('apim_product_list_page_id');

		return '<!-- wp:navigation-link {"label":"Home","type":"page","id":'.$welcome_page_id.',"url":"'.$wp_site_url.'/","kind":"post-type"} /-->
		<!-- wp:navigation-link {"label":"APIs","type":"page","id":'.$api_list_page_id.',"url":"'.$wp_site_url.'/apis/","kind":"post-type"} /-->
		<!-- wp:navigation-link {"label":"Products","type":"page","id":'.$product_list_page_id.',"url":"'.$wp_site_url.'/products/","kind":"post-type"} /-->';
	}
}
