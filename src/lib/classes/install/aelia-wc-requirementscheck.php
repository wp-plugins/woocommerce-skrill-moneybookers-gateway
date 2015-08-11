<?php
if(!defined('ABSPATH')) exit; // Exit if accessed directly

// Do not try to redeclare the class
if(!class_exists('Aelia_WC_RequirementsChecks')) {
	/**
	 * Checks that plugin's requirements are met.
	 */
	class Aelia_WC_RequirementsChecks {
		/**
		 * The text domain for the messages displayed by the class. This property must
		 * be overridden by descendant classes and it should match the text domain
		 * of the plugin that is going to check the requirements.
		 *
		 * @var string
		 */
		protected $text_domain = 'wc_aelia';

		/**
		 * The name of the plugin for which the requirements are being checked. This
		 * property must be overridden by descendant classes and it must match the
		 * plugin name exactly.
		 *
		 * @var string
		 */
		protected $plugin_name = 'WC Template Plugin';

		/**
		 * The path to the directory containing the admin-install.js file. It must
		 * not have a leading or trailing slash.
		 *
		 * @var string
		 * @see Aelia_WC_RequirementsChecks::js_url
		 * @since 1.5.1.150305
		 */
		protected $js_dir = 'src/js/admin';

		/**
		 * The minimum version of PHP required by the plugin.
		 *
		 * @var string
		 */
		protected $required_php_version = '5.3';

		// @var array An array of PHP extensions required by the plugin
		protected $required_extensions = array(
			//'curl',
		);

		// @var array A list of all the installed plugins.
		protected static $_installed_plugins;

		// @var array An array of WordPress plugins (name => version) required by the plugin.
		protected $required_plugins = array(
			'WooCommerce' => '2.0.10',
			//'Aelia Foundation Classes for WooCommerce' => array(
			//	'version' => '1.0.0.140508',
			//	'extra_info' => 'You can get the plugin <a href="http://dev.pathtoenlightenment.net/downloads/wc-aelia-foundation-classes.zip">from our site</a>, free of charge.',
			//	The URL from where the plugin can be downloaded and installed automatically
			//	'url' => 'http://somesite.com/some/path/plugin.zip',
			//),
		);

		// @var array An array with the details of the required plugins.
		protected $required_plugins_info = array();

		// @var array Holds a list of the errors related to missing requirements
		protected $requirements_errors = array();

		protected $plugin_actions = array();

		public function __construct() {
			// Require necessary WP Core files
			if(!function_exists('get_plugins')) {
				require_once(ABSPATH . 'wp-admin/includes/plugin.php');
			}

			// Ensure that all plugin requirements are in array format
			$this->normalize_plugin_requirements();

			// Ajax hooks
			add_action('wp_ajax_' . $this->get_ajax_action('install'), array($this, 'wp_ajax_install_plugin'));
			add_action('wp_ajax_' . $this->get_ajax_action('activate'), array($this, 'wp_ajax_activate_plugin'));
		}

		/**
		 * Normalizes the format of the plugin requirements list. This operation is
		 * necessary for backward compatibility, as the plugin requirements might
		 * just contain the plugin version, rather than an array of plugin details.
		 *
		 * @since 1.5.4.150316
		 */
		protected function normalize_plugin_requirements() {
			foreach($this->required_plugins as $plugin_name => $plugin_requirements) {
				// If plugin_details is not an array, it's assumed to be a string containing
				// the required plugin version
				if(!is_array($plugin_requirements)) {
					$this->required_plugins[$plugin_name] = array(
						'version' => $plugin_requirements,
					);
				}
			}
		}

		/**
		 * Factory method. It MUST be copied to every descendant class, as it has to
		 * be compatible with PHP 5.2 and earlier, so that the class can be instantiated
		 * in any case and and gracefully tell the user if PHP version is insufficient.
		 *
		 * @return Aelia_WC_RequirementsChecks
		 */
		public static function factory() {
			$instance = new self();
			return $instance;
		}

		/**
		 * Builds and returns the action ID to install or activate a plugin.
		 *
		 * @param string action The action key ("install" or "activate").
		 * @return string
		 * @since 1.5.0.150225
		 */
		protected function get_ajax_action($action) {
			return $action . '_plugin_' . sha1(get_class($this));
		}

		/**
		 * Checks that one or more PHP extensions are loaded.
		 *
		 * @return array An array of error messages containing one entry for each
		 * extension that is not loaded.
		 */
		protected function check_required_extensions() {
			foreach($this->required_extensions as $extension) {
				if(!extension_loaded($extension)) {
					$this->requirements_errors[] = sprintf(__('Plugin requires "%s" PHP extension.', $this->text_domain),
																								 $extension);
				}
			}
		}

		/**
		 * Returns the URL that will trigger the installation of a plugin.
		 *
		 * @param string plugin_name The plugin name.
		 * @return string
		 * @since 1.5.0.150225
		 */
		protected function get_plugin_install_url($plugin_name) {
			$plugin_slug = sanitize_title($plugin_name);
			$plugin_install_url = wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=' . $plugin_slug), 'install-plugin_' . $plugin_slug);
			return $plugin_install_url;
		}

		/**
		 * Returns the Ajax URL that will trigger the installation of a plugin.
		 *
		 * @param string plugin_name The plugin name.
		 * @return string
		 * @since 1.5.0.150225
		 */
		protected function get_ajax_url($plugin_name, $action) {
			$plugin_slug = sanitize_title($plugin_name);
			$ajax_args = http_build_query(array(
				'action' => $this->get_ajax_action($action),
				'plugin' => $plugin_slug,
				'_ajax_nonce' => wp_create_nonce('aelia-plugin-' . $action . '-' . $plugin_slug),
			));
			$ajax_url = admin_url('admin-ajax.php', 'absolute') . '?' . $ajax_args;
			return $ajax_url;
		}

		/**
		 * Checks that the necessary plugins are installed, and that their version is
		 * the expected one.
		 *
		 * @param bool autoload_plugins Indicates if the required plugins should be
		 * loaded automatically, if requirements checks pass.
		 */
		protected function check_required_plugins($autoload_plugins = true) {
			foreach($this->required_plugins as $plugin_name => $plugin_requirements) {
				$plugin_info = $this->get_wp_plugin_info($plugin_name);

				$message = '';
				if(!is_array($plugin_info)) {
					// Plugin is not installed. Check if it can be installed automatically
					// and provide a button to do it
					if(isset($plugin_requirements['url']) && filter_var($plugin_requirements['url'], FILTER_VALIDATE_URL)) {
						// Debug
						//var_dump($plugin_requirements['url']);
						$this->plugin_actions[$plugin_name] = 'install';
					}
				}
				else {
					if(!$plugin_info['active']) {
						// If plugin can be activated, provide a button to do it
						$this->plugin_actions[$plugin_name] = 'activate';
					}
				}

				if(is_array($plugin_info) && ($plugin_info['active'] == true)) {
					if(version_compare($plugin_info['version'], $plugin_requirements['version'], '<')) {
						$message = sprintf(__('Plugin "%s" must be version "%s" or later.', $this->text_domain),
															 $plugin_name,
															 $plugin_requirements['version']);
					}
					else {
						// If plugin must be loaded automatically, without waiting for WordPress to load it,
						// add it to the autoload queue
						if(isset($plugin_requirements['autoload']) && ($plugin_requirements['autoload'] == true)) {
							$this->required_plugins_info[$plugin_name] = $plugin_info;
						}
					}
				}
				else {
					$message = sprintf(__('Plugin "<strong>%s</strong>" must be installed and activated.', $this->text_domain),
														 $plugin_name);
				}

				if(!empty($message)) {
					if(isset($plugin_requirements['extra_info'])) {
						$message .= ' ' . $plugin_requirements['extra_info'];
					}
					$this->requirements_errors[$plugin_name] = $message;
				}
			}
		}

		/**
		 * Checks that plugin requirements are satisfied.
		 *
		 * @return bool
		 */
		public function check_requirements() {
			$this->requirements_errors = array();
			if(PHP_VERSION < $this->required_php_version) {
				$this->requirements_errors[] = sprintf(__('Plugin requires PHP %s or greater.', $this->text_domain),
																							 $this->required_php_version);
			}

			$this->check_required_extensions();
			$this->check_required_plugins();

			$result = empty($this->requirements_errors);

			if($result) {
				$this->load_required_plugins();
			}
			else {
				// If requirements are missing, display the appropriate notices
				add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
				add_action('admin_notices', array($this, 'plugin_requirements_notices'));
			}
			return $result;
		}

		/**
		 * Checks if WC plugin is active, either for the single site or, in
		 * case of WPMU, for the whole network.
		 *
		 * @return bool
		 */
		public static function is_wc_active() {
			if(defined('WC_ACTIVE')) {
				return WC_ACTIVE;
			}

			// Test if WC is installed and active
			if(self::factory()->is_plugin_active('WooCommerce')) {
				define('WC_ACTIVE', true);
				return true;
			}
			return false;
		}

		/**
		 * Clears the chached list of installed plugins. This is necessary after a
		 * new plugin is installed, to allow WordPress to find it.
		 */
		protected function clear_plugin_cache() {
			wp_cache_set('plugins', false, 'plugins');
			self::$_installed_plugins = null;
		}

		/**
		 * Returns a list of the installed plugins.
		 *
		 * @return array
		 */
		protected function installed_plugins() {
			if(empty(self::$_installed_plugins)) {
				self::$_installed_plugins = get_plugins();
			}
			return self::$_installed_plugins;
		}

		/**
		 * Returns the details of a plugin from $this->required_plugins property.
		 *
		 * @param string $plugin_slug_with_hash A plugin slug. The slug must match
		 * one of the plugin names from the required_plugins property.
		 * @return array|false
		 * @since 1.5.0.150225
		 */
		protected function get_plugin_info($plugin_slug) {
			foreach($this->required_plugins as $plugin_name => $plugin_info) {
				if($plugin_slug === sanitize_title($plugin_name)) {
					$plugin_info['name'] = $plugin_name;
					return $plugin_info;
				}
			}
			return false;
		}

		/**
		 * Queries WordPress and returns the details of an installed plugin.
		 *
		 * @param string plugin_name The plugin name.
		 * @return array|bool An array of plugin details, or false if the plugin is
		 * not installed.
		 * @since 1.5.0.150225
		 */
		protected function get_wp_plugin_info($plugin_name) {
			foreach($this->installed_plugins() as $path => $plugin_info){
				if(strcasecmp($plugin_info['Name'], $plugin_name) === 0) {
					$plugin_info['path'] = $path;
					$plugin_info['active'] = is_plugin_active($path);
					$plugin_info = array_change_key_case($plugin_info, CASE_LOWER);
					return $plugin_info;
				}
			}
			return false;
		}

		/**
		 * Returns the URL to the folder containing the JavaScript that will be used
		 * for the frontend.
		 *
		 * @return string|null An URL, or null if the URL cannot be determined.
		 * @since 1.5.0.150225
		 */
		protected function js_url() {
			$plugin_info = $this->get_wp_plugin_info($this->plugin_name);
			if($plugin_info == false) {
				trigger_error(sprintf(__('%s - Plugin not found: "%s". ' .
																 'This could be due by an incorrect name specified in ' .
																 'class "%s". Please report the issue to support.',
																 $this->text_domain),
															get_class($this),
															$this->plugin_name,
															get_class($this)),
											E_USER_WARNING);
				return null;
			}
			return plugin_dir_url($plugin_info['path']) . $this->js_dir;
		}

		/**
		 * Returns the details of a plugin, showing if it's installed and active.
		 *
		 * @param string plugin_name The name of the plugin to check.
		 * @return bool
		 * @deprecated since 1.5.0.150225
		 */
		public function is_plugin_active($plugin_name) {
			return $this->get_wp_plugin_info($plugin_name);
		}

		/**
		 * Returns the snippet of HTML code that allows to automatically install or
		 * activate a plugin. The method returns an empty string if none of such
		 * operations is possible.
		 *
		 * @param string plugin_name The name of the plugin to be installed or
		 * activated automatically.
		 * @return string The HTML snippet with the appropriate action (install/activate),
		 * or an empty string if neither action is possible.
		 * @since 1.5.4.150316
		 */
		protected function get_plugin_action_html($plugin_name) {
			if(empty($this->plugin_actions[$plugin_name])) {
				return '';
			}

			$plugin_action = $this->plugin_actions[$plugin_name];
			$plugin_slug = sanitize_title($plugin_name);
			$action_html = '';
			switch($plugin_action) {
				case 'install':
					$plugin_install_url = $this->get_ajax_url($plugin_name, 'install');
					$plugin_action = '<a href="' . $plugin_install_url . '" ' .
													 'class="plugin_action button" ' .
													 'plugin_slug="' . $plugin_slug . '" ' .
													 'prompt="install" ' .
													 'ajax_url="' . $plugin_install_url . '">';
					$plugin_action .= __('Install plugin', $this->text_domain);
					$plugin_action .= '</a>';
					break;
				case 'activate':
					// Plugin is installed, but not active. Add button to activate it
					$plugin_action = '<a href="#" class="plugin_action button" ' .
													 'plugin_slug="' . $plugin_slug . '" ' .
													 'prompt="activate" ' .
													 'ajax_url="' . $this->get_ajax_url($plugin_name, 'activate') . '">';
					$plugin_action .= __('Activate plugin', $this->text_domain);
					$plugin_action .= '</a>';
					break;
				default:
					// Nothing to do
			}

			if(!empty($plugin_action)) {
				// Build the plugin wrapper that will contain the link to install or
				// activate the plugin
				$plugin_action_wrapper = '<span class="plugin_action_wrapper">';
				$plugin_action_wrapper .= '<span class="spinner"></span>%s';
				$plugin_action_wrapper .= '<div class="plugin_action_result"><pre class="messages"></pre></div>';
				$plugin_action_wrapper .= '</span>';

				$action_html = sprintf($plugin_action_wrapper, $plugin_action);
			}
			return $action_html;
		}

		/**
		 * Display requirements errors that prevented the plugin from being loaded.
		 */
		public function plugin_requirements_notices() {
			if(empty($this->requirements_errors)) {
				return;
			}

			// For each missing plugin, check if it's possible to install or activate
			// it automatically. If it's possible, render a button to allow the administrator
			// to do so
			foreach($this->requirements_errors as $plugin_name => $message) {
				if(is_string($plugin_name)) {
					$this->requirements_errors[$plugin_name] .= $this->get_plugin_action_html($plugin_name);
				}
			}
			?>
			<style type="text/css">
				.wc_aelia.message .spinner,
				.wc_aelia.message .button {
					vertical-align: middle;
					margin-left: 0.5em;
					float: none;
				}

				.wc_aelia.message .spinner {
					display: none;
				}

				.wc_aelia.message .spinner.visible {
					display: inline-block;
					visibility: visible;
				}
			</style>
			<div class="wc_aelia message error fade">
				<h4 class="wc_aeliamessage_header" style="margin: 1em 0 0 0"><?php
					echo sprintf(__('Plugin "%s" could not be loaded due to missing requirements.',
													$this->text_domain),
											 $this->plugin_name);
				?></h4>
			<ul style="list-style: disc inside">
				<li><?php
					echo implode('</li><li>', $this->requirements_errors);
				?></li>
			</ul>
			<p class="info"><?php
				echo __('Please review the missing requirements listed above, and ensure ' .
								'that all necessary plugins and PHP extensions are installed and ' .
								'loaded correctly. This plugin will work automatically as soon as all the ' .
								'requirements are met. If you need assistance on this matter, please ' .
								'<a href="https://aelia.freshdesk.com/helpdesk/tickets/new">contact our ' .
								'Support team</a>.',
								$this->text_domain);
				?></p>
			</div>
			<?php
		}

		/**
		 * Enqueues the scripts required by the requirement checker class.
		 * @since 1.5.0.150225
		 */
		public function admin_enqueue_scripts() {
			$js_url = $this->js_url();
			if(empty($js_url) || wp_script_is('aelia-install', 'enqueued')) {
				return false;
			}
			wp_enqueue_script('aelia-install',
												$js_url . '/aelia-install.js',
												array('jquery'),
												null,
												true);

			$aelia_wc_requirementchecks_params = array(
				'user_interface' => array(
					'plugin_install_prompt' => __('This will install and activate the plugin ' .
																				'automatically. Would you like to proceed?', $this->text_domain),
					'plugin_activate_prompt' => __('This will activate the plugin. Would you like to proceed?',
																				 $this->text_domain),
				),
			);
			wp_localize_script('aelia-install',
												 'aelia_wc_requirementchecks_params',
												 $aelia_wc_requirementchecks_params);
		}

		/**
		 * Loads the required plugins.
		 */
		public function load_required_plugins() {
			foreach($this->required_plugins_info as $plugin_name => $plugin_info) {
				// Debug
				//var_dump(WP_PLUGIN_DIR . '/' . $plugin_info['path']);
				require_once(WP_PLUGIN_DIR . '/' . $plugin_info['path']);
			}
		}

		/**
		 * Installs a plugin from the specified URL.
		 *
		 * @param string plugin_url The URL to a plugin ZIP file.
		 * @param string plugin_name The plugin name.
		 * @return array An array with the result of the installation.
		 * @since 1.5.0.150225
		 */
		protected function install_plugin($plugin_url, $plugin_name) {
			$result = new stdClass();
			$result->status = false;
			$result->messages = array(
				sprintf(__('Installing plugin "%s"...', $this->text_domain), $plugin_name)
			);

			$wp_plugin_info = $this->get_wp_plugin_info($plugin_name);
			if(is_array($wp_plugin_info)) {
				$result->messages[] = __('Plugin is already installed.', $this->text_domain);
				$result->status = true;
				return $result;
			}

			// If we reach this point, the plugin has to be installed
			include_once(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');
			$skin = new Automatic_Upgrader_Skin();
			$upgrader = new Plugin_Upgrader($skin);

			$install_result = $upgrader->install($plugin_url);
			$result->messages = array_merge($result->messages, $skin->get_upgrade_messages());

			if($install_result instanceof WP_Error) {
				$result->status = false;
				$result->messages[] = __('Plugin installation failed. See errors below.', $this->text_domain);
				$result->messages = array_merge($result->messages, $install_result->get_error_messages());
				$result->messages[] = sprintf(__('Please try installing the plugin manually. You can ' .
																				 'download it from %s.', $this->text_domain),
																			$plugin_url);
			}
			else {
				$result->status = true;
			}
			return $result;
		}

		/**
		 * Installs a plugin from the specified URL.
		 *
		 * @param string plugin_url The URL to a plugin ZIP file.
		 * @param string plugin_name The plugin name.
		 * @return array An array with the result of the installation.
		 * @since 1.5.0.150225
		 */
		protected function activate_plugin($plugin_name) {
			$result = new stdClass();
			$result->messages = array(
				sprintf(__('Activating plugin "%s"...', $this->text_domain), $plugin_name),
			);

			$wp_plugin_info = $this->get_wp_plugin_info($plugin_name);
			if(!is_array($wp_plugin_info)) {
				$result->messages[] = __('Plugin is not installed.', $this->text_domain);
				$result->status = false;
				return $result;
			}

			if($wp_plugin_info['active']) {
				$result->messages[] = __('Plugin already active.', $this->text_domain);
				$result->status = true;
				return $result;
			}

			$activation_result = activate_plugin($wp_plugin_info['path'], null, false);
			if($activation_result instanceof WP_Error) {
				$result->status = false;
				$result->messages[] = __('Activation failed.', $this->text_domain);
				$result->messages = array_merge($result->messages, $activation_result->get_error_messages());
				return $result;
			}

			$result->messages[] = __('Plugin activated successfully. Please refresh this page to ' .
															 'hide the requirement notices and this message.', $this->text_domain);
			$result->status = true;
			return $result;
		}

		/**
		 * Validates Ajax requests.
		 *
		 * @param string ajax_referer_key Indicates which URL argument contains the
		 * Ajax nonce.
		 * @return bool
		 * @since 1.5.0.150225
		 */
		protected function validate_ajax_request($ajax_nonce_key) {
			$result = true;
			if(!current_user_can('manage_options')) {
				$message = 'HTTP/1.1 400 Bad Request';
				header($message, true, 403);
				$result = false;
			}

			// Handle invalid requests (e.g. a request missing the "plugin" argument)
			if($result && empty($_REQUEST['plugin'])) {
				$message = 'HTTP/1.1 400 Bad Request';
				header($message, true, 400);
				$result = false;
			}

			if($result && !check_ajax_referer($ajax_nonce_key, '_ajax_nonce', false)) {
				header('HTTP/1.1 400 Bad Request', true, 400);
				$message = 'Ajax referer check failed';
				$result = false;
			};

			if($result == false) {
				wp_send_json(array(
					'result' => $result,
					'messages' => array($message),
				));
			}
		}

		/**
		 * Handles the Ajax request to install a plugin.
		 *
		 * @since 1.5.0.150225
		 */
		public function wp_ajax_install_plugin() {
			$plugin_slug = isset($_REQUEST['plugin']) ? $_REQUEST['plugin'] : '';
			$this->validate_ajax_request('aelia-plugin-install-' . $plugin_slug);

			$result = array(
				'status' => true,
				'messages' => array(),
			);

			// Get the plugin information from the required plugins list
			$plugin_info = $this->get_plugin_info($plugin_slug);
			if($plugin_info === false) {
				$result['status'] = false;
				$result['messages'][] = __('Invalid plugin specified: "%s".', $this->text_domain);
			}

			// Ensure that the plugin has a URL from which it can be downloaded
			if(empty($plugin_info['url'])) {
				$result['status'] = false;
				$result['messages'][] = __('Plugin does not have a URL from which it can be ' .
																	 'downloaded automatically.', $this->text_domain);
			}

			// Query WordPress to check if plugin is installed. If not, install it
			if($result['status']) {
				$install_result = $this->install_plugin($plugin_info['url'], $plugin_info['name']);
				$result['status'] = $install_result->status;
				$result['messages'] = array_merge($result['messages'], $install_result->messages);
			}

			// If installation went well, activate the plugin
			if($result['status']) {
				$this->clear_plugin_cache();
				$activation_result = $this->activate_plugin($plugin_info['name']);
				$result['status'] = $activation_result->status;
				$result['messages'] = array_merge($result['messages'], $activation_result->messages);
			}

			wp_send_json($result);
		}

		/**
		 * Handles the Ajax request to activate a plugin.
		 *
		 * @since 1.5.0.150225
		 */
		public function wp_ajax_activate_plugin() {
			$plugin_slug = isset($_REQUEST['plugin']) ? $_REQUEST['plugin'] : '';
			$this->validate_ajax_request('aelia-plugin-activate-' . $plugin_slug);

			$result = array(
				'status' => true,
				'messages' => array(),
			);

			// Get the plugin information from the required plugins list
			$plugin_info = $this->get_plugin_info($plugin_slug);
			if($plugin_info === false) {
				$result['status'] = false;
				$result['messages'][] = __('Invalid plugin specified: "%s".', $this->text_domain);
			}

			// Check if plugin is installed. If it is, activate it
			if($result['status']) {
				$activation_result = $this->activate_plugin($plugin_info['name']);
				$result['status'] = $activation_result->status;
				$result['messages'] = array_merge($result['messages'], $activation_result->messages);
			}
			wp_send_json($result);
		}
	}
}
