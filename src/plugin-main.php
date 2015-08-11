<?php
namespace Aelia\WC\SkrillGateway;
if(!defined('ABSPATH')) exit; // Exit if accessed directly

//define('SCRIPT_DEBUG', 1);
//error_reporting(E_ALL);

// Load definitions
require_once('lib/classes/definitions/definitions.php');

use Aelia\WC\Aelia_Plugin;
use Aelia\WC\Aelia_SessionManager;
use Aelia\WC\Messages;
use \WC_Gateway_Skrill;

/**
 * Main plugin class.
 */
class WC_Skrill_Gateway_Plugin extends Aelia_Plugin {
	// @var string The plugin version
	public static $version = '1.2.25.150811';

	public static $plugin_slug = Definitions::PLUGIN_SLUG;
	public static $text_domain = Definitions::TEXT_DOMAIN;
	public static $plugin_name = 'Aelia - Skrill Gateway for WooCommerce';

	/**
	 * Handler of plugins_loaded event.
	 */
	public function plugins_loaded() {
		parent::plugins_loaded();

		// The commented line below is needed for Codestyling Localization plugin to
		// understand what text domain is used by this plugin
		//load_plugin_textdomain(static::$text_domain, false, $this->path('languages') . '/');

		// Gateway classes have to be loaded manually because, when the WC Api is
		// invoked, the autoloader might not work properly. This can result in the
		// payment process getting interrupted when Skrill redirects back to the
		// ecommerce site
		$this->load_gateway_classes();

		// This filter must be added on "plugins_loaded()" for compatibility with both
		// WooCommerce 1.6 and 2.0. Adding to "set_hooks()" would work for WC 2.0,
		// but not for 1.6, which fires events in a different order
		add_filter('woocommerce_payment_gateways', array($this, 'add_skrill_gateway'));
	}

	/**
	 * Loads the gateway classes that will interface with Skrill.
	 */
	protected function load_gateway_classes() {
		$gateway_classes_path = $this->path('classes') . '/payment_gateways';

		require_once($gateway_classes_path . '/class-wc-gateway-skrill.php');
	}

	/**
	 * Sets required hooks.
	 */
	public function set_hooks() {
		parent::set_hooks();
	}

	/**
	 * Add the Skrill Connect gateway to WooCommerce.
	 *
	 * @param array methods An array of payment gateway classes.
	 * @return array
	 */
	public function add_skrill_gateway(array $methods) {
		$methods[] = 'WC_Gateway_Skrill';

		return $methods;
	}

	/**
	 * Constructor.
	 *
	 * @param \Aelia\WC\Settings settings_controller The controller that will handle
	 * the plugin settings.
	 * @param \Aelia\WC\Messages messages_controller The controller that will handle
	 * the messages produced by the plugin.
	 */
	public function __construct($settings_controller,
															$messages_controller) {
		// Load Composer autoloader
		require_once(__DIR__ . '/vendor/autoload.php');

		parent::__construct($settings_controller, $messages_controller);
	}

	/**
	 * Factory method.
	 *
	 * @return WC_Skrill_Gateway_Plugin
	 */
	public static function factory() {
		// Load Composer autoloader
		require_once(__DIR__ . '/vendor/autoload.php');

		$messages_controller = new Messages();

		$plugin_instance = new self(null, $messages_controller);
		return $plugin_instance;
	}
}


$GLOBALS[WC_Skrill_Gateway_Plugin::$plugin_slug] = WC_Skrill_Gateway_Plugin::factory();
