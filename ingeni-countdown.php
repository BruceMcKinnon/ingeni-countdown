<?php
/*
Plugin Name: Ingeni Countdown Timer
Version: 2023.01
Plugin URI: https://ingeni.net
Author: Bruce McKinnon - ingeni.net
Author URI: http://ingeni.net
Description: A simple countdown timer 
License: GPL v3

Ingeni PHP Mail
Copyright (C) 2023, Bruce McKinnon

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.


v2023.01 - 29 May 2023 - Initial release. Based on https://codepen.io/AllThingsSmitty/pen/JJavZN
*/

/*
if ( !function_exists("is_local") ) {
	function is_local() {
		$local_install = false;
		if ( ($_SERVER['SERVER_NAME']=='localhost') ) {
			$local_install = true;
		}
		return $local_install;
	}
}

if (!function_exists("fb_log")) {
	function fb_log($msg) {

		$upload_dir = wp_upload_dir();
		$outFile = $upload_dir['basedir'];
		if ( is_local() ) {
			$outFile .= '\\';
		} else {
			$outFile .= '/';
		}
		$outFile .= basename(__DIR__).'.txt';
		
		date_default_timezone_set(get_option('timezone_string'));

		// Now write out to the file
		$log_handle = fopen($outFile, "a");
		if ($log_handle !== false) {
			fwrite($log_handle, date("Y-m-d H:i:s").": ".$msg."\r\n");
			fclose($log_handle);
		}
	}	
}
*/

//
//
// Enqueue our stylesheet and javascript file
//
function ingeni_countdown_enqueue() {
	wp_register_script( 'countdown_js', plugins_url('ingeni-countdown.js', __FILE__), false, 0, true );
	wp_enqueue_script( 'countdown_js' );
}
add_action( 'wp_enqueue_scripts', 'ingeni_countdown_enqueue' );



//
// Shortcode
//

add_shortcode( "ingeni-countdown-timer", "ingeni_countdown_timer" );
function ingeni_countdown_timer( $atts ) {
	$retHtml = '';

	try {
		// Get the WP timezone and set the local timezone to that
		$tz = wp_timezone();
		$tz_name = timezone_name_get($tz);
		date_default_timezone_set( $tz_name );
	} catch (Exception $e) {
		fb_log('Invalid timzone' );
	}

	$default_date = strtotime("+7 day");

	$params = shortcode_atts( array( 
		'target_date' => date("Y-m-d", $default_date),
		'target_time' => date("H:i:s", $default_date),
		'show_days' => 1,
		'show_hours' => 1,
		'show_mins' => 1,
		'show_secs' => 1,
		'class' => 'ingeni_countdown_timer',
		), $atts );

	$now = date("Y-m-d H:i:s");
	$countdown_to = date_create($now);
	date_add( $countdown_to, date_interval_create_from_date_string("7 days") );
	try {
		$target_str = $params['target_date'].'T'.$params['target_time'];
		$target_time = strtotime($target_str);
		$countdown_to = date("Y-m-d H:i:s", $target_time);
	} catch (Exception $e) {
		fb_log('Invalid date format: '.$params['target_date'].'T'.$params['target_time'] );
	}

	 
	$retHtml .= '<div class="'.$params['class'].'">';
		$retHtml .= '<div id="ingeni_countdown_target">'.$countdown_to.'</div>';
		$retHtml .= '<div id="ingeni_countdown">';
			$retHtml .= '<ul>';
				if ( $params['show_days'] == 1) {
					$retHtml .= '<li><span id="ingeni_countdown_days"></span>Days</li>';
				}
				if ( $params['show_hours'] == 1) {
					$retHtml .= '<li><span id="ingeni_countdown_hours"></span>Hours</li>';
				}
				if ( $params['show_mins'] == 1) {
					$retHtml .= '<li><span id="ingeni_countdown_minutes"></span>Mins</li>';
				}
				if ( $params['show_secs'] == 1) {
					$retHtml .= '<li><span id="ingeni_countdown_seconds"></span>Secs</li>';
				}
			$retHtml .= '</ul>';
		$retHtml .= '</div>';
	$retHtml .= '</div>';

	return $retHtml;
}






function ingeni_update_countdown() {
	require 'plugin-update-checker/plugin-update-checker.php';
	$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
		'https://github.com/BruceMcKinnon/ingeni-countdown',
		__FILE__,
		'ingeni-countdown'
	);
	
	//Optional: If you're using a private repository, specify the access token like this:
	//$myUpdateChecker->setAuthentication('your-token-here');
	
	//Optional: Set the branch that contains the stable release.
	//$myUpdateChecker->setBranch('stable-branch-name');

}
add_action( 'init', 'ingeni_update_countdown' );



//
// Plugin registration functions
//
register_activation_hook(__FILE__, 'ingeni_countdown_activation');
function ingeni_countdown_activation() {
	flush_rewrite_rules( false );
}

register_deactivation_hook( __FILE__, 'ingeni_countdown_deactivation' );
function ingeni_countdown_deactivation() {
	// Unhook
	flush_rewrite_rules( false );
}

?>