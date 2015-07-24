<?php if(!defined('ABSPATH')) exit; // Exit if accessed directly
	if(!defined('AELIA_WC_SKRILL_SHOW_FIELDS')) {
		define('AELIA_WC_SKRILL_SHOW_FIELDS', false);
	}

	// View is included by the Skrill gateway instance, which is what
	// "$this" refers to. This assignment is done just to improve code
	// readability
	$gateway_skrill = $this;

	if($gateway_skrill->testmode) {
		$skrill_url = $gateway_skrill->test_url;
	}
	else {
		$skrill_url = $gateway_skrill->live_url;
	}

	if($gateway_skrill->debug_mode()) {
		echo '<p class="debug">';
		echo __('Debug mode is active, therefore automatic redirection to Skrill is disabled. ' .
						'please click on the button below to submit the payment to Skrill.',
						$gateway_skrill->textdomain);
		echo '</p>';
	}
	else {
		// JavaScript to automatically submit the form is loaded only when not debugging
		$skrill_js_url = $gateway_skrill->url('js_frontend') . '/skrill.js';
		echo '<script type="text/javascript" src="' . $skrill_js_url . '"></script>';
	}
?>

<form id="skrill_form" method="post" action="<?php echo $skrill_url; ?>">
	<?php
		// When debugging, display the fields
		$field_type = (AELIA_WC_SKRILL_SHOW_FIELDS === true) ? 'text' : 'hidden';
		$skrill_fields = array();
		// $skrill_args is a variable loaded just before including this view, thus
		// it's in this code's scope
		foreach($skrill_args as $field_name => $value) {
			$skrill_fields[] = '<input type="' . $field_type . '" name="'. esc_attr($field_name) .
													 '" value="' . esc_attr($value) . '" />';
		}
		echo implode("\n", $skrill_fields);
	?>
	<input id="skrill_submit" type="submit" value="<?php echo __('Pay via Skrill', $gateway_skrill->textdomain); ?>">
</form>
