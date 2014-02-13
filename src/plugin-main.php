<?php if(!defined('ABSPATH')) exit; // Exit if accessed directly

//define('SCRIPT_DEBUG', 1);
//error_reporting(E_ALL);

// Load Composer autoloader
require_once(__DIR__ . '/vendor/autoload.php');

/**
 * Main plugin class.
 */
class WC_Skrill_Gateway_Plugin extends WC_Aelia_Plugin {
	// @var string The plugin version
	public static $version = '1.0.5.130214';

	// @var string The plugin instance key, used to retrieve the plugin instance
	public static $plugin_slug = 'wc-skrill-gateway';
	public static $text_domain = 'wc-skrill-gateway';

	/**
	 * Handler of plugins_loaded event.
	 */
	public function plugins_loaded() {
		parent::plugins_loaded();

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
	 * @param WC_Aelia_Settings settings_controller The controller that will handle
	 * the plugin settings.
	 * @param WC_Aelia_Messages messages_controller The controller that will handle
	 * the messages produced by the plugin.
	 */
	public function __construct(WC_Aelia_Settings $settings_controller,
															WC_Aelia_Messages $messages_controller) {
		parent::__construct($settings_controller, $messages_controller);
	}

	/**
	 * Factory method.
	 *
	 * @return WC_Skrill_Gateway_Plugin
	 */
	public static function factory() {
		$settings_key = self::$plugin_slug;

		$settings_page_renderer = new WC_Aelia_Settings_Renderer();
		$settings_controller = new WC_Aelia_Settings($settings_key,
																								 self::$text_domain,
																								 $settings_page_renderer);
		$messages_controller = new WC_Aelia_Messages();

		$plugin_instance = new WC_Skrill_Gateway_Plugin($settings_controller, $messages_controller);
		return $plugin_instance;
	}
}


if(WC_Skrill_Gateway_Plugin::check_requirements() == true) {
	// Instantiate plugin and add it to the set of globals
	$GLOBALS[WC_Skrill_Gateway_Plugin::$plugin_slug] = WC_Skrill_Gateway_Plugin::factory();
}
else {
	// If requirements are missing, display the appropriate notices
	add_action('admin_notices', array('WC_Skrill_Gateway_Plugin', 'plugin_requirements_notices'));
}
