<?php if(!defined('ABSPATH')) exit; // Exit if accessed directly

if(class_exists('WC_Aelia_Plugin')) {
	return;
}

interface IWC_Aelia_Plugin {
	public function settings_controller();
	public function messages_controller();
	public static function instance();
	public static function settings();
	public function setup();
	public static function cleanup();
}


// Load general functions file
require_once('general_functions.php');

/**
 * Implements a base plugin class to be used to implement WooCommerce plugins.
 */
class WC_Aelia_Plugin implements IWC_Aelia_Plugin {
	// @var string The plugin version.
	public static $version = '0.8.1';

	// @var string The plugin slug
	public static $plugin_slug = 'wc-aelia-plugin';
	// @var string The plugin text domain
	public static $text_domain = 'wc-aelia-plugin';
	// @var string The plugin name
	public static $plugin_name = 'wc-aelia-plugin';

	// @var array Holds a list of the errors related to missing requirements
	public static $requirements_errors = array();

	// @var string The base name of the plugin directory
	protected $plugin_directory;

	// @var WC_Aelia_Settings The object that will handle plugin's settings.
	protected $_settings_controller;
	// @var WC_Aelia_Messages The object that will handle plugin's messages.
	protected $_messages_controller;
	// @var Aelia_SessionManager The session manager
	protected $_session;

	protected $paths = array(
		// This array will contain the paths used by the plugin
	);

	protected $urls = array(
		// This array will contain the URLs used by the plugin
	);

	/**
	 * Returns global instance of woocommerce.
	 *
	 * @return object The global instance of woocommerce.
	 */
	protected function woocommerce() {
		global $woocommerce;
		return $woocommerce;
	}

	/**
	 * Returns the session manager.
	 *
	 * @return object The global instance of woocommerce.
	 */
	protected function session() {
		if(empty($this->_session)) {
			$this->_session = new Aelia_SessionManager();
		}
		return $this->_session;
	}

	/**
	 * Returns the instance of the Settings Controller used by the plugin.
	 *
	 * @return WC_Aelia_Settings.
	 */
	public function settings_controller() {
		return $this->_settings_controller;
	}

	/**
	 * Returns the instance of the Messages Controller used by the plugin.
	 *
	 * @return WC_Aelia_Messages.
	 */
	public function messages_controller() {
		return $this->_messages_controller;
	}

	/**
	 * Returns the instance of the plugin.
	 *
	 * @return WC_Aelia_Plugin.
	 */
	public static function instance() {
		return $GLOBALS[static::$plugin_slug];
	}

	/**
	 * Returns the plugin path.
	 *
	 * @return string
	 */
	public static function plugin_path() {
		$reflection_class = new ReflectionClass(get_called_class());

		return dirname($reflection_class->getFileName());
	}

	/**
	 * Returns the Settings Controller used by the plugin.
	 *
	 * @return WC_Aelia_Settings.
	 */
	public static function settings() {
		return self::instance()->settings_controller();
	}

	/**
	 * Returns the Messages Controller used by the plugin.
	 *
	 * @return WC_Aelia_Messages.
	 */
	public static function messages() {
		return self::instance()->messages_controller();
	}

	/**
	 * Retrieves an error message from the internal Messages object.
	 *
	 * @param mixed error_code The Error Code.
	 * @return string The Error Message corresponding to the specified Code.
	 */
	public function get_error_message($error_code) {
		return $this->_messages_controller->get_error_message($error_code);
	}

	/**
	 * Triggers an error displaying the message associated to an error code.
	 *
	 * @param mixed error_code The Error Code.
	 * @param int error_type The type of Error to raise.
	 * @param array error_args An array of arguments to pass to the vsprintf()
	 * function which will format the error message.
	 * @param bool show_backtrace Indicates if a backtrace should be displayed
	 * after the error message.
	 * @return string The formatted error message.
	 */
	protected function trigger_error($error_code, $error_type = E_USER_NOTICE, array $error_args = array(), $show_backtrace = false) {
		$error_message = $this->get_error_message($error_code);

		$message = vsprintf($error_message, $error_args);
		if($show_backtrace) {
			$e = new Exception();
			$backtrace = $e->getTraceAsString();
			$message .= " \n" . $backtrace;
		}

		return trigger_error($message, $error_type);
	}

		/**
	 * Sets the hook handlers for WooCommerce and WordPress.
	 */
	protected function set_hooks() {
		add_action('admin_enqueue_scripts', array($this, 'load_admin_scripts'));
		add_action('wp_enqueue_scripts', array($this, 'load_frontend_scripts'));
	}

	/**
	 * Returns the full path corresponding to the specified key.
	 *
	 * @param key The path key.
	 * @return string
	 */
	public function path($key) {
		return get_value($key, $this->paths, '');
	}

	/**
	 * Builds and stores the paths used by the plugin.
	 */
	protected function set_paths() {
		$this->paths['plugin'] = WP_PLUGIN_DIR . '/' . $this->plugin_dir()  . '/src';
		$this->paths['lib'] = $this->path('plugin') . '/lib';
		$this->paths['views'] = $this->path('plugin') . '/views';
		$this->paths['admin_views'] = $this->path('views') . '/admin';
		$this->paths['classes'] = $this->path('lib') . '/classes';
		$this->paths['widgets'] = $this->path('classes') . '/widgets';
		$this->paths['vendor'] = $this->path('plugin') . '/vendor';

		$this->paths['design'] = $this->path('plugin') . '/design';
		$this->paths['css'] = $this->path('design') . '/css';
		$this->paths['images'] = $this->path('design') . '/images';

		$this->paths['js'] = $this->path('plugin') . '/js';
		$this->paths['js_admin'] = $this->path('js') . '/admin';
		$this->paths['js_frontend'] = $this->path('js') . '/frontend';
	}

	/**
	 * Builds and stores the URLs used by the plugin.
	 */
	protected function set_urls() {
		$this->urls['plugin'] = plugins_url() . '/' . $this->plugin_dir() . '/src';

		$this->urls['design'] = $this->url('plugin') . '/design';
		$this->urls['css'] = $this->url('design') . '/css';
		$this->urls['images'] = $this->url('design') . '/images';
		$this->urls['js'] = $this->url('plugin') . '/js';
		$this->urls['js_admin'] = $this->url('plugin') . '/admin';
		$this->urls['js_frontend'] = $this->url('js') . '/frontend';
	}

	/**
	 * Returns the URL corresponding to the specified key.
	 *
	 * @param key The URL key.
	 * @return string
	 */
	public function url($key) {
		return get_value($key, $this->urls, '');
	}

	/**
	 * Returns the directory in which the plugin is stored. Only the base name of
	 * the directory is returned (i.e. without path).
	 *
	 * @return string
	 */
	public function plugin_dir() {
		if(empty($this->plugin_directory)) {
			$reflector = new ReflectionClass($this);
			$this->plugin_directory = basename(dirname(dirname($reflector->getFileName())));
		}

		return $this->plugin_directory;
	}

	/**
	 * Constructor.
	 *
	 * @param WC_Aelia_Settings settings_controller The controller that will handle
	 * the plugin settings.
	 * @param WC_Aelia_Messages messages_controller The controller that will handle
	 * the messages produced by the plugin.
	 */
	public function __construct($settings_controller = null, $messages_controller = null) {
		// Set plugin's paths
		$this->set_paths();
		// Set plugin's URLs
		$this->set_urls();

		$this->_settings_controller = $settings_controller;
		$this->_messages_controller = $messages_controller;

		// Uncomment line below to debug the activation hook when using symlinks
		//register_activation_hook(basename(dirname(__FILE__)).'/'.basename(__FILE__), array($this, 'setup'));
		register_activation_hook(__FILE__, array($this, 'setup'));
		register_uninstall_hook(__FILE__, array(get_class($this), 'cleanup'));

		// called only after woocommerce has finished loading
		add_action('init', array($this, 'wordpress_loaded'));
		add_action('woocommerce_init', array($this, 'woocommerce_loaded'), 1);

		// called after all plugins have loaded
		add_action('plugins_loaded', array($this, 'plugins_loaded'));

		// called just before the woocommerce template functions are included
		add_action('init', array($this, 'include_template_functions'), 20);

		// indicates we are running the admin
		if(is_admin()) {
			// ...
		}

		// indicates we are being served over ssl
		if(is_ssl()) {
			// ...
		}
	}

	/**
	 * Run the updates required by the plugin. This method runs at every load, but
	 * the updates are executed only once. This allows the plugin to run the
	 * updates automatically, without requiring deactivation and rectivation.
	 *
	 * @return bool
	 */
	protected function run_updates() {
		$installer_class = get_class($this) . '_Install';
		if(!class_exists($installer_class)) {
			return;
		}

		$installer = new $installer_class();
		return $installer->update(static::$plugin_slug, static::$version);
	}

	/**
	 * Returns an instance of the class. This method should be implemented by
	 * descendant classes to return a pre-configured instance of the plugin class,
	 * complete with the appropriate settings controller.
	 *
	 * @return WC_Aelia_Plugin
	 * @throws Aelia_NotImplementedException
	 */
	public static function factory() {
		throw new Aelia_NotImplementedException();
	}

	/**
	 * Take care of anything that needs to be done as soon as WordPress finished
	 * loading.
	 */
	public function wordpress_loaded() {
		$this->register_common_frontend_scripts();
	}

	/**
	 * Performs operation when woocommerce has been loaded.
	 */
	public function woocommerce_loaded() {
		// Set all required hooks
		$this->set_hooks();

		// Run updates only when in Admin area. This should occur automatically when
		// plugin is activated, since it's done in the Admin area
		if(is_admin()) {
			$this->run_updates();
		}
	}

	/**
	 * Performs operation when all plugins have been loaded.
	 */
	public function plugins_loaded() {
		$class = get_class($this);
		load_plugin_textdomain(static::$text_domain, false, dirname(plugin_basename(__FILE__)) . '/languages/');
	}

	/**
	 * Override any of the template functions from woocommerce/woocommerce-template.php
	 * with our own template functions file
	 */
	public function include_template_functions() {

	}

	/**
	 * Registers a widget class.
	 *
	 * @param string widget_class The class to register.
	 * @param bool stop_on_error Indicates if the function should raise an error
	 * if the Widget Class doesn't exist or cannot be loaded.
	 * @return bool True, if the Widget was registered correctly, False otherwise.
	 */
	protected function register_widget($widget_class, $stop_on_error = true) {
		$file_to_load = $this->path('widgets') . '/' . str_replace('_', '-', strtolower($widget_class)) . '.php';

		if(!file_exists($file_to_load)) {
			if($stop_on_error === true) {
				$this->trigger_error(WC_Aelia_Messages::ERR_FILE_NOT_FOUND, E_USER_ERROR, array($file_to_load), true);
			}
			return false;
		}
		require_once($file_to_load);
		register_widget($widget_class);

		return true;
	}

	/**
	 * Registers all the Widgets used by the plugin.
	 */
	public function register_widgets() {
		// Register the required widgets
		//$this->register_widget('WC_Aelia_Template_Widget');
	}

	/**
	 * Determines if one of plugin's admin pages is being rendered. Override it
	 * if plugin implements pages in the Admin section.
	 *
	 * @return bool
	 */
	protected function rendering_plugin_admin_page() {
		return false;
	}

	/**
	 * Registers the script and style files required in the backend (even outside
	 * of plugin's pages). Extend in descendant plugins.
	 */
	protected function register_common_admin_scripts() {
		// Dummy
	}

	/**
	 * Registers the script and style files needed by the admin pages of the
	 * plugin. Extend in descendant plugins.
	 */
	protected function register_plugin_admin_scripts() {
		// Admin scripts
		wp_register_script(static::$plugin_slug . '-admin',
											 $this->url('plugin') . '/js/admin/admin.js',
											 array('jquery'),
											 null,
											 false);
		// Admin styles
		wp_register_style(static::$plugin_slug . '-admin',
											$this->url('plugin') . '/design/css/admin.css',
											array(),
											null,
											'all');
	}

	/**
	 * Registers the script and style files required in the frontend (even outside
	 * of plugin's pages).
	 */
	protected function register_common_frontend_scripts() {
		// Scripts
		wp_register_script(static::$plugin_slug . '-frontend',
											 $this->url('plugin') . '/js/frontend/frontend.js',
											 array('jquery'),
											 null,
											 true);
		// Styles
		wp_register_style(static::$plugin_slug . '-frontend',
											$this->url('plugin') . '/design/css/frontend.css',
											array(),
											null,
											'all');
	}

	/**
	 * Loads Styles and JavaScript for the Admin pages.
	 */
	public function load_admin_scripts() {
		// Register common JS for the backend
		$this->register_common_admin_scripts();
		if($this->rendering_plugin_admin_page()) {
			// Load Admin scripts only on plugin settings page
			$this->register_plugin_admin_scripts();

			// Styles - Enqueue styles required for plugin Admin page
			wp_enqueue_style(static::$plugin_slug . '-admin');

			// JavaScript - Enqueue scripts required for plugin Admin page
			// Enqueue the required Admin scripts
			wp_enqueue_script(static::$plugin_slug . '-admin');
		}
	}


	/**
	 * Loads Styles and JavaScript for the frontend. Extend as needed in
	 * descendant classes.
	 */
	public function load_frontend_scripts() {
		// Enqueue the required Frontend stylesheets
		//wp_enqueue_style(static::$plugin_slug . '-frontend');

		// JavaScript
		//wp_enqueue_script(static::$plugin_slug . '-frontend');
	}

	/**
	 * Checks that one or more PHP extensions are loaded.
	 *
	 * @param array required_extensions An array of extension names.
	 * @return array An array of error messages containing one entry for each
	 * extension that is not loaded.
	 */
	protected static function check_required_extensions(array $required_extensions) {
		$errors = array();
		foreach($required_extensions as $extension) {
			if(!extension_loaded($extension)) {
				$errors[] = sprintf(__('Plugin requires "%s" PHP extension.', AELIA_CS_PLUGIN_TEXTDOMAIN),
														$extension);
			}
		}

		return $errors;
	}

	/**
	 * Checks that plugin requirements are satisfied.
	 *
	 * @return bool
	 */
	public static function check_requirements() {
		static::$requirements_errors = array();
		if(PHP_VERSION < '5.3') {
			static::$requirements_errors[] = __('Plugin requires PHP 5.3 or greater.', static::$text_domain);
		}

		// Check for WooCommerce presence
		if(!self::is_woocommerce_active()) {
			static::$requirements_errors[] = __('WooCommerce plugin must be installed and activated.', static::$text_domain);
		}

		// Check that all required extensions are loaded
		$required_extensions = array(
			//'curl',
		);
		$extension_errors = self::check_required_extensions($required_extensions);

		static::$requirements_errors = array_merge(static::$requirements_errors, $extension_errors);

		return empty(static::$requirements_errors);
	}

	/**
	 * Display requirements errors that prevented the plugin from being loaded.
	 */
	public static function plugin_requirements_notices() {
		if(empty(static::$requirements_errors)) {
			return;
		}

		// Inline CSS styles have to be used because plugin is not loaded if
		// requirements are missing, therefore the plugin's CSS files are ignored
		echo '<div class="error fade">';
		echo '<h4 class="wc_aelia message_header" style="margin: 1em 0 0 0">';
		echo sprintf(__('Plugin %s could not be loaded due to missing requirements', static::$text_domain),
								 static::$plugin_name);
		echo '</h4>';
		echo '<ul style="list-style: disc inside">';
		echo '<li>';
		echo implode('</li><li>', static::$requirements_errors);
		echo '</li>';
		echo '</ul>';
		echo '</div>';
	}

	/**
	 * Setup function. Called when plugin is enabled.
	 */
	public function setup() {
		if(!empty(static::$requirements_errors)) {
			die(implode('<br>', static::$requirements_errors));
		}
	}

	/**
	 * Cleanup function. Called when plugin is uninstalled.
	 */
	public static function cleanup() {
		if(!defined('WP_UNINSTALL_PLUGIN')) {
			return;
		}
	}

	/**
	 * Checks if WooCommerce plugin is active, either for the single site or, in
	 * case of WPMU, for the whole network.
	 *
	 * @return bool
	 */
	public static function is_woocommerce_active() {
		if(defined('WOOCOMMERCE_ACTIVE')) {
			return WOOCOMMERCE_ACTIVE;
		}

		$woocommerce_plugin_key = 'woocommerce/woocommerce.php';
		$result = self::is_plugin_active($woocommerce_plugin_key);

		define('WOOCOMMERCE_ACTIVE', $result);

		return WOOCOMMERCE_ACTIVE;
	}

	/**
	 * Checks if a plugin is active and returns a value to indicate it.
	 *
	 * @param string plugin_key The key of the plugin to check.
	 * @return bool
	 */
	public static function is_plugin_active($plugin_key) {
		$result = in_array($plugin_key, get_option('active_plugins'));

		if(!$result && function_exists('is_multisite') && is_multisite()) {
			$result = array_key_exists($plugin_key, get_site_option('active_sitewide_plugins'));
		}

		return $result;
	}
}
