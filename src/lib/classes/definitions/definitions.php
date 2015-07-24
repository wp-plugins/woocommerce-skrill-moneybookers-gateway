<?php
namespace Aelia\WC\SkrillGateway;
if(!defined('ABSPATH')) exit; // Exit if accessed directly

use \Aelia\WC\Messages;
use \WP_Error;

/**
 * Implements a base class to store and handle the messages returned by the
 * plugin. This class is used to extend the basic functionalities provided by
 * standard WP_Error class.
 */
class Definitions {
	// @var string The menu slug for plugin's settings page.
	const MENU_SLUG = 'wc_aelia_skrill_gateway';
	// @var string The plugin slug
	const PLUGIN_SLUG = 'wc-aelia-skrill-gateway';
	// @var string The plugin text domain
	const TEXT_DOMAIN = 'wc-aelia-skrill-gateway';

	const ERR_INVALID_TEMPLATE = 1001;
}
