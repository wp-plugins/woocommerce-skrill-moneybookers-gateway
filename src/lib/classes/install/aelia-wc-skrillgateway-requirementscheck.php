<?php
if(!defined('ABSPATH')) exit; // Exit if accessed directly

require_once('aelia-wc-requirementscheck.php');

/**
 * Checks that plugin's requirements are met.
 */
class Aelia_WC_Skrill_Gateway_RequirementsChecks extends Aelia_WC_RequirementsChecks {
	// @var string The namespace for the messages displayed by the class.
	protected $text_domain = 'wc-aelia-skrill-gateway';
	// @var string The plugin for which the requirements are being checked. Change it in descendant classes.
	protected $plugin_name = 'Aelia - WooCommerce Skrill Gateway';

	// @var array An array of WordPress plugins (name => version) required by the plugin.
	protected $required_plugins = array(
		'WooCommerce' => '2.0.10',
		'Aelia Foundation Classes for WooCommerce' => array(
			'version' => '1.0.10.140819',
			'extra_info' => 'You can get the plugin <a href="http://aelia.co/downloads/wc-aelia-foundation-classes.zip">from our site</a>, free of charge.',
			'autoload' => true,
		),
	);

	/**
	 * Factory method. It MUST be copied to every descendant class, as it has to
	 * be compatible with PHP 5.2 and earlier, so that the class can be instantiated
	 * in any case and and gracefully tell the user if PHP version is insufficient.
	 *
	 * @return Aelia_WC_AFC_RequirementsChecks
	 */
	public static function factory() {
		$instance = new self();
		return $instance;
	}

	/**
	 * Display requirements errors that prevented the plugin from being loaded.
	 */
	public function plugin_requirements_notices() {
		if(empty($this->requirements_errors)) {
			return;
		}

		// Inline CSS styles have to be used because plugin is not loaded if
		// requirements are missing, therefore the plugin's CSS files are ignored
		echo '<div class="error fade">';
		echo '<h4 class="wc_aeliamessage_header" style="margin: 1em 0 0 0">';
		echo sprintf(__('Plugin "%s" could not be loaded due to missing requirements.', $this->text_domain),
								 $this->plugin_name);
		echo '</h4>';
		echo '<div class="info">';
		echo __('<b>Note</b>: even though the plugin might be showing as "<b><i>active</i></b>", it will not load ' .
						'and its features will not be available until its requirements are met. If you need assistance, ' .
						'on this matter, please use the <a href="http://wordpress.org/support/plugin/woocommerce-skrill-moneybookers-gateway">' .
						'plugin support section</a>. Please note that this plugin is subject to the ' .
						'<a href="https://wordpress.org/plugins/woocommerce-skrill-moneybookers-gateway/faq/"><b>terms of ' .
						'support described on the plugin page</b></a>.',
						$this->text_domain);
		echo '</div>';
		echo '<ul style="list-style: disc inside">';
		echo '<li>';
		echo implode('</li><li>', $this->requirements_errors);
		echo '</li>';
		echo '</ul>';
		echo '</div>';
	}
}
