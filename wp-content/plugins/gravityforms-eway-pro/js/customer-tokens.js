// Gravity Forms eWAY Pro customer tokens field script

(function($) {

	var has_form_initialised = false;

	/**
	* catch when the form has been rendered
	* @param {jQuery.Event} event
	* @param {String} form_id
	* @param {String} current_page
	*/
	$(document).on("gform_post_render", function(event, form_id, current_page) {
		has_form_initialised = true;

		// bail if we're on the confirmation page
		if (!current_page) {
			return;
		}

		// find checked cardlist inputs (e.g. multiple forms) and adjust displayed sub-fields
		$("#gform_wrapper_" + form_id + " .gfewaypro_cust_tokens_cardlist input:checked").each(toggleCustomerTokensVisible);
	});

	/**
	* extract form ID from field ID
	* @param {String} field_id
	* @return {String} form_id
	*/
	function extractFormId(field_id) {
		var parts = field_id.split("_");

		return parts.length > 2 ? parts[1] : "";
	}

	/**
	* extract base field ID from field ID
	* @param {String} field_id
	* @return {String}
	*/
	function extractBaseFieldId(field_id) {
		var parts = field_id.split(".");

		return parts[0];
	}

	/**
	* adjust displayed sub-fields for selected card from Remembered Cards list
	*/
	function toggleCustomerTokensVisible() {
		var field = $(this).closest(".ginput_container_gfewaypro_cust_tokens");

		if (this.value) {
			// show security code sub-field for selected card
			field.find(".gfewaypro_cust_tokens_remember").hide();
			field.find(".gfewaypro_cust_tokens_security_code").show();
		}
		else {
			// show option to remember new card
			field.find(".gfewaypro_cust_tokens_remember").show();
			field.find(".gfewaypro_cust_tokens_security_code").hide();
		}

		// tell dependent conditional fields that selected card has changed (if we're past the form init)
		if (has_form_initialised) {
			var conditional = String(field.data("gfeway_conditional_fields"));
			if (conditional) {
				gf_apply_rules(extractFormId(this.id), conditional.split(","));
			}
		}
	}

	// when selected card is changed, adjust displayed sub-fields
	$(document).on("change", ".gfewaypro_cust_tokens_cardlist input[type='radio']", toggleCustomerTokensVisible);

	/**
	* for conditional logic comparisons, just use the selected card value
	* NB: basically checking for empty string for card token, which means "Use a new card"
	* @param {Bool} is_match
	* @param {Number} form_id
	* @param {Object} rule
	* @return {Bool}
	*/
	gform.addFilter("gform_is_value_match", function(is_match, form_id, rule) {
		var field_id	= extractBaseFieldId(rule.fieldId);
		var field		= $("#input_" + form_id + "_" + field_id);

		if (rule.operator === "gfeway_new_card") {
			if (field.length === 0 || field.find("input[name='input_" + field_id + ".1']").prop("type") === "hidden") {
				// equivalent to "use new card" because Credit Card field should be shown
				is_match = true;
			}
			else if (field.hasClass("ginput_container_gfewaypro_cust_tokens")) {
				is_match = field.find("input[name='input_" + field_id + ".1']:checked").val() === "";
			}
		}

		return is_match;
	});

})(jQuery);
