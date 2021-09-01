<?php
/*
Plugin Name: Geo Targeting Elementor by Benlola
Plugin URI: https://github.com/Benlola/geo_targeting_elementor_benlola
Description: Geo Targeting a Elementor element or any custom block in HTML
Version: 1.2.0
Author: Benlola Team
Author URI: https://benlola.com/
License: GPLv2 or later
Text Domain: geo_targeting_elementor_benlola
*/
/*
 * *
 * Test with GeoIP module if the country is the same (special for Cloudways host)
$REAL_IP = getenv(HTTP_X_REAL_IP);
$FORWARDED_CONTINENT = getenv(HTTP_X_FORWARDED_CONTINENT);
$FORWARDED_COUNTRY = getenv(HTTP_X_FORWARDED_COUNTRY);

echo 'Real IP  : '.$REAL_IP.'<br>';
echo 'Source IP Continent : '.$FORWARDED_CONTINENT.'<br>';
echo 'Source IP Country : '.$FORWARDED_COUNTRY.'<br>';
*/

//Get IP
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

//Get country
$details = json_decode(file_get_contents("http://ipinfo.io/{$ipaddress}/json"));
$country_code = $details->country;
if(!isset( $data->country )){
	$details = json_decode(file_get_contents("http://ip-api.com/json/{$ipaddress}"));
	$country_code = $details->countryCode;
}

define("GEO_TARGETING_IP", $ipaddress);
define("GEO_TARGETING_COUNTRY_CODE", $country_code);

add_action( 'wp_head', 'geo_targeting_elementor_benlola' );
function geo_targeting_elementor_benlola($ipaddress) {
	//Custom styles
	//Show something in this country
	echo '<style>';
	echo "body:not(.elementor-editor-active) [class*=' geo_visible_']:not(.geo_visible_".GEO_TARGETING_COUNTRY_CODE.") {";
	echo "display: none!important;";
	echo "}";
	//Hide something in this country
	echo "body:not(.elementor-editor-active) .geo_hidden_".GEO_TARGETING_COUNTRY_CODE."{";
	echo "display: none!important;";
	echo "}";
	echo '</style>';
};

add_filter( 'body_class', 'geo_targeting_new_class' );
function geo_targeting_new_class( $classes) {
	$classes[] = "geo_current_country_".GEO_TARGETING_COUNTRY_CODE;
	return $classes;
}