<?php
/*
Plugin Name: Geo Targeting Elementor by Benlola
Plugin URI: https://benlola.com/
Description: Geo Targeting a Elementor element or any custom block in HTML
Version: 1.0.0
Author: Benlola Team
Author URI: https://benlola.com/
License: GPLv2 or later
Text Domain: geo_targeting_elementor_benlola
*/

add_action( 'wp_head', 'geo_targeting_elementor_benlola' );
function geo_targeting_elementor_benlola() {
	$ipaddress = '';//default value
	if ( getenv( 'HTTP_CLIENT_IP' ) ) {
		$ipaddress = getenv( 'HTTP_CLIENT_IP' );
	} elseif ( getenv( 'HTTP_X_FORWARDED_FOR' ) ) {
		$ipaddress = getenv( 'HTTP_X_FORWARDED_FOR' );
		// HTTP_X_FORWARDED_FOR sometimes returns internal or local IP address, which is not usually useful. Also, it would return a comma separated list if it was forwarded from multiple ipaddresses.
		$addr      = explode( ',', $ipaddress );
		$ipaddress = $addr[0];
	} elseif ( getenv( 'HTTP_X_FORWARDED' ) ) {
		$ipaddress = getenv( 'HTTP_X_FORWARDED' );
	} elseif ( getenv( 'HTTP_FORWARDED_FOR' ) ) {
		$ipaddress = getenv( 'HTTP_FORWARDED_FOR' );
	} elseif ( getenv( 'HTTP_FORWARDED' ) ) {
		$ipaddress = getenv( 'HTTP_FORWARDED' );
	} elseif ( getenv( 'REMOTE_ADDR' ) ) {
		$ipaddress = getenv( 'REMOTE_ADDR' );
	} else {
		$ipaddress = 'UNKNOWN';
	}

	//Get IP
	$agent_ip = $ipaddress;

	//Get country
	$details = json_decode(file_get_contents("http://ipinfo.io/{$agent_ip}/json"));
	$country_code = $details->country;
	if(!isset( $data->country )){
		$details = json_decode(file_get_contents("http://ip-api.com/json/{$agent_ip}"));
		$country_code = $details->countryCode;
	}

	//Custom styles
	echo '<style>';
	echo "[class*=' geo_visible_']:not(.geo_visible_$country_code) {";
	echo "display: none!important;";
	echo "}";
	echo '</style>';
}
?>