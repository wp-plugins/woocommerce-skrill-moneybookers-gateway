/* Common JavaScript for Admin section */
jQuery(document).ready(function($) {
	var $messages = $('.wc_aelia.message');
	var ui_params = aelia_wc_requirementchecks_params.user_interface;

	$messages.find('.plugin_action').on('click', function(e) {
		e.stopPropagation();

		var $action = $(this);

		// Ask administrator to confirm the action before proceeding
		var prompt_key = $action.attr('prompt');
		if(prompt_key != '') {
			prompt_key = 'plugin_' + prompt_key + '_prompt';
			if(ui_params[prompt_key] && !window.confirm(ui_params[prompt_key])) {
				return false;
			}
		}

		var action_url = $action.attr('ajax_url');
		var plugin_slug = $action.attr('plugin_slug');

		var $related_actions = $messages.find('.plugin_action[plugin_slug="' + plugin_slug + '"]');
		if($related_actions.length > 0) {
			$related_actions.hide();
			var $spinners = $related_actions.siblings('.spinner').addClass('visible');
		}

		$.ajax({
			type: 'POST',
			url: action_url,
			dataType: 'json',
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				console.log(XMLHttpRequest.responseText);
				console.log(textStatus);
				console.log(errorThrown);
			},
			success: function(json) {
				console.log(json);
				var $result_elem = $action.siblings('.plugin_action_result').first().find('.messages');
				$result_elem.html(json.messages.join("\n"));
			},
			complete: function(XMLHttpRequest, textStatus) {
				if($spinners) {
					$spinners.removeClass('visible');
				}
				console.log('complete');
			}
		});
		return false;
	});
});
