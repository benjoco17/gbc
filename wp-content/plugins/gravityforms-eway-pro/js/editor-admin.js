// Gravity Forms eWAY Pro settings admin script

(function($) {

	var TYPE_CUSTOMER_TOKENS = "gfewaypro_cust_tokens";

	// set strings used by Gravity Forms admin keyed by string tokens
	gf_vars.gfeway_new_card = gfewaypro_editor_admin_strings.customer_tokens.gfeway_new_card;

	/**
	* prevent multiple instances of Customer Tokens field on form
	* @param {bool} can_be_added
	* @param {String} field_type
	* @return {bool}
	*/
	gform.addFilter("gform_form_editor_can_field_be_added", function(can_be_added, field_type) {
		if (field_type === TYPE_CUSTOMER_TOKENS && GetFieldsByType([field_type]).length > 0) {
			can_be_added = false;
			window.alert(gfewaypro_editor_admin_strings.customer_tokens.only_one);
		}

		return can_be_added;
	});

	/**
	* customise available logic operators for custom field types
	* @param {Object} operators map of operator to label
	* @param {String} object_type
	* @param {Number} field_id
	* @return {Object}
	*/
	gform.addFilter("gform_conditional_logic_operators", function(operators, object_type, field_id) {
		var field = GetFieldById(field_id);

		if (field && field.type === TYPE_CUSTOMER_TOKENS) {
			operators = { "gfeway_new_card": "gfeway_new_card" };
		}

		return operators;
	});

	/**
	* customise conditional logic value field for custom field types
	* @param {String} inputs
	* @param {String} object_type
	* @param {Number} rule_index
	* @param {Number} field_id
	* @return {Object}
	*/
	gform.addFilter("gform_conditional_logic_values_input", function(inputs, object_type, rule_index, field_id) {
		var field = GetFieldById(field_id);

		if (field && field.type === TYPE_CUSTOMER_TOKENS) {
			// hidden field, no need for a value here
			var input_name = object_type + "_rule_value_" + rule_index;
			inputs = '<input type="hidden" value="" name="' + input_name + '" id="' + input_name + '" />';
		}

		return inputs;
	});

})(jQuery);
