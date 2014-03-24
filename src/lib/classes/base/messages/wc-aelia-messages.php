<?php if(!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Implements a base class to store and handle the messages returned by the
 * plugin. This class is used to extend the basic functionalities provided by
 * standard WP_Error class.
 */
class WC_Aelia_Messages {
	const DEFAULT_TEXTDOMAIN = 'woocommerce-aelia';

	// Result constants
	const RES_OK = 0;
	const ERR_FILE_NOT_FOUND = 100;
	const ERR_NOT_IMPLEMENTED = 101;

	// @var WP_Error Holds the error messages registered by the plugin
	protected $_wp_error;

	// @var string The text domain used by the class
	protected $_text_domain = self::DEFAULT_TEXTDOMAIN;

	public function __construct($text_domain = self::DEFAULT_TEXTDOMAIN) {
		$this->_text_domain = $text_domain;
		$this->_wp_error = new WP_Error();
		$this->load_error_messages();
	}

	/**
	 * Loads all the messages used by the plugin. This class should be
	 * extended during implementation, to add all error messages used by
	 * the plugin.
	 */
	public function load_messages() {
		$this->add_message(self::ERR_FILE_NOT_FOUND, __('File not found: "%s".', $this->_text_domain));
		$this->add_message(self::ERR_NOT_IMPLEMENTED, __('Not implemented.', $this->_text_domain));

		// TODO Add here all the error messages used by the plugin
	}

	/**
	 * Registers an error message in the internal _wp_error object.
	 *
	 * @param mixed error_code The Error Code.
	 * @param string error_message The Error Message.
	 */
	public function add_message($error_code, $error_message) {
		$this->_wp_error->add($error_code, $error_message);
	}

	/**
	 * Retrieves an error message from the internal _wp_error object.
	 *
	 * @param mixed error_code The Error Code.
	 * @return string The Error Message corresponding to the specified Code.
	 */
	public function get_message($error_code) {
		return $this->_wp_error->get_error_message($error_code);
	}

	/**
	 * Calls WC_Aelia_Messages::load_messages(). Implemented for backward
	 * compatibility.
	 */
	public function load_error_messages() {
		$this->load_messages();
	}

	/**
	 * Calls WC_Aelia_Messages::add_message(). Implemented for backward
	 * compatibility.
	 */
	public function add_error_message($error_code, $error_message) {
		$this->add_message($error_code, $error_message);
	}

	/**
	 * Calls WC_Aelia_Messages::get_message(). Implemented for backward
	 * compatibility.
	 */
	public function get_error_message($error_code) {
		return $this->get_message($error_code);
	}
}
