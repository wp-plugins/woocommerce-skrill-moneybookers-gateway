<?php if(!defined('ABSPATH')) exit; // Exit if accessed directly

if(class_exists('WC_Aelia_Message')) {
	return;
}

/**
 * Class to represent messages generated by the plugin.
 */
class WC_Aelia_Message {
	public $level;
	public $code;
	public $message;

	/**
	 * Class constructor.
	 *
	 * @param int level The message level.
	 * @param string message The message itself.
	 * @param string code The message code, if any.
	 * @return WC_Aelia_Message
	 */
	public function __construct($level, $message, $code = '') {
		$this->level = $level;
		$this->message = $message;
		$this->code = $code;
	}
}