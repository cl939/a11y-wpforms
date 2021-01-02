<?php
/**
 * Theme functions file.
 *
 */

 
/**
 * Hide WPForms reCAPTCHA input from screen readers
 *
 * Uses a11y-wpforms custom plugin
 */

if ( function_exists('remove_filters_for_anonymous_class') && class_exists('WPForms')) {

remove_filters_for_anonymous_class( 'wpforms_frontend_output', 'WPForms_Frontend', 'recaptcha', 20, 5 );

add_action('wpforms_frontend_output', 'recaptcha_attr', 20, 5 );

}
