<?php if(!defined('ABSPATH')) exit; // Exit if accessed directly
/*
Plugin Name: WooCommerce Skrill Gateway
Plugin URI: http://wordpress.org/support/plugin/woocommerce-skrill-moneybookers-gateway
Description: A payment gateway for Skrill (https://www.skrill.com/). A Skrill merchant account is required for this gateway to work properly.
Version: 1.2.25.150811
Author: Aelia
Author URI: http://aelia.co
License: GPLv3
*/
require_once(dirname(__FILE__) . '/src/lib/classes/install/aelia-wc-skrillgateway-requirementscheck.php');
// If requirements are not met, deactivate the plugin
if(Aelia_WC_Skrill_Gateway_RequirementsChecks::factory()->check_requirements()) {
	require_once dirname(__FILE__) . '/src/plugin-main.php';
}
