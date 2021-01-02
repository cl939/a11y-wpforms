<?php

/**
* Plugin Name: A11y WPForms
* Description: Make WPForms more accessible
* Version: 0.1.0
* Author: Cornell AAD WSUX / Chris Lastovicka
* Text Domain: a11y-wpforms
*/


if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }

// Following code thanks to Amaury Balmer: https://github.com/herewithme/wp-filters-extras/blob/master/wp-filters-extras.php

function remove_filters_with_method_name( $hook_name = '', $method_name = '', $priority = 0 ) {
	global $wp_filter;

	// Take only filters on right hook name and priority
	if ( ! isset( $wp_filter[ $hook_name ][ $priority ] ) || ! is_array( $wp_filter[ $hook_name ][ $priority ] ) ) {
		return false;
	}

	// Loop on filters registered
	foreach ( (array) $wp_filter[ $hook_name ][ $priority ] as $unique_id => $filter_array ) {
		// Test if filter is an array ! (always for class/method)
		if ( isset( $filter_array['function'] ) && is_array( $filter_array['function'] ) ) {
			// Test if object is a class and method is equal to param !
			if ( is_object( $filter_array['function'][0] ) && get_class( $filter_array['function'][0] ) && $filter_array['function'][1] == $method_name ) {
				// Test for WordPress >= 4.7 WP_Hook class (https://make.wordpress.org/core/2016/09/08/wp_hook-next-generation-actions-and-filters/)
				if ( is_a( $wp_filter[ $hook_name ], 'WP_Hook' ) ) {
					unset( $wp_filter[ $hook_name ]->callbacks[ $priority ][ $unique_id ] );
				} else {
					unset( $wp_filter[ $hook_name ][ $priority ][ $unique_id ] );
				}
			}
		}

	}

	return false;
}

/**
 * Allow to remove method for an hook when, it's a class method used and class don't have variable, but you know the class name :)
 */
function remove_filters_for_anonymous_class( $hook_name = '', $class_name = '', $method_name = '', $priority = 0 ) {
	global $wp_filter;

	// Take only filters on right hook name and priority
	if ( ! isset( $wp_filter[ $hook_name ][ $priority ] ) || ! is_array( $wp_filter[ $hook_name ][ $priority ] ) ) {
		return false;
	}

	// Loop on filters registered
	foreach ( (array) $wp_filter[ $hook_name ][ $priority ] as $unique_id => $filter_array ) {
		// Test if filter is an array ! (always for class/method)
		if ( isset( $filter_array['function'] ) && is_array( $filter_array['function'] ) ) {
			// Test if object is a class, class and method is equal to param !
			if ( is_object( $filter_array['function'][0] ) && get_class( $filter_array['function'][0] ) && get_class( $filter_array['function'][0] ) == $class_name && $filter_array['function'][1] == $method_name ) {
				// Test for WordPress >= 4.7 WP_Hook class (https://make.wordpress.org/core/2016/09/08/wp_hook-next-generation-actions-and-filters/)
				if ( is_a( $wp_filter[ $hook_name ], 'WP_Hook' ) ) {
					unset( $wp_filter[ $hook_name ]->callbacks[ $priority ][ $unique_id ] );
				} else {
					unset( $wp_filter[ $hook_name ][ $priority ][ $unique_id ] );
				}
			}
		}

	}

	return false;
}

// End Amaury Balmer code

// Rewrite WPForms reCAPTCHA to include aria-hidden and tabindex attributes on hidden input

function recaptcha_attr( $form_data, $deprecated, $title, $description, $errors ) {

		// Check that recaptcha is configured in the settings.
		$site_key   = wpforms_setting( 'recaptcha-site-key' );
		$secret_key = wpforms_setting( 'recaptcha-secret-key' );
		$type       = wpforms_setting( 'recaptcha-type', 'v2' );
		if ( ! $site_key || ! $secret_key ) {
			return;
		}
		
		// Check that the recaptcha is configured for the specific form.
		if (
			! isset( $form_data['settings']['recaptcha'] ) ||
			'1' != $form_data['settings']['recaptcha']
		) {
			return;
		}

		$data    = array(
			'sitekey' => trim( sanitize_text_field( $site_key ) ),
		);

		echo '<div class="wpforms-recaptcha-container">';

		echo '<div ' . wpforms_html_attributes( '', array( 'g-recaptcha' ), $data ) . '></div>';

		if ( 'invisible' !== $type ) {
			echo '<input aria-hidden="true" tabindex="-1" type="text" name="g-recaptcha-hidden" class="wpforms-recaptcha-hidden" style="position:absolute!important;clip:rect(0,0,0,0)!important;height:1px!important;width:1px!important;border:0!important;overflow:hidden!important;padding:0!important;margin:0!important;" required';
		}

		if ( ! empty( $errors['recaptcha'] ) ) {
			WPForms_Frontend::form_error( 'recaptcha', $errors['recaptcha'] );
		}

		echo '</div>';
	}

?>
