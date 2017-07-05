// Gravity Forms eWAY Pro script supporting eWAY's Client Side Encryption

(function($) {

	/**
	* if form has Client Side Encryption key, hook its submit action for maybe encrypting
	* @param {jQuery.Event} event
	* @param {Number} form_id int ID of Gravity Forms form
	*/
	$(document).on("gform_post_render", function(event, form_id) {
		$("#gform_" + form_id + "[data-gfeway-encrypt-key]").on("submit", maybeEncryptForm);
	});

	/**
	* check form for conditions to encrypt sensitive fields
	*/
	function maybeEncryptForm() {

		var frm = $(this);
		var key = frm.data("gfeway-encrypt-key");

		function maybeEncryptField() {
			var field = $(this);
			var value = field.val().trim();

			if (value.length) {
				var encrypted = eCrypt.encryptValue(value, key);
				$("<input type='hidden'>").attr("name", field.data("gfeway-encrypt-name")).val(encrypted).appendTo(frm);
				field.val("");
			}
		}

		function extractFormId(form_element_id) {
			var parts = form_element_id.split("_");

			return parts.length > 1 ? parts[1] : "";
		}

		// don't encrypt if this transaction is being processed as a Recurring Payments XML feed
		if (!isRecurringPaymentsXML(extractFormId(this.id))) {

			$("input[data-gfeway-encrypt-name]").filter(":visible").each(maybeEncryptField);

		}

		return true;

	}

	/**
	* check for Recurring Payments XML feed with conditional logic that has been met (will be executed on submit)
	* @param {String} form_id
	* @return {Bool}
	*/
	function isRecurringPaymentsXML(form_id) {
		if (gfewaypro_recurring_rules && form_id in gfewaypro_recurring_rules) {

			var feeds = gfewaypro_recurring_rules[form_id];

			for (var i = 0, len = feeds.length; i < len; i++) {
				if (feedRulesMatch(form_id, feeds[i])) {
					return true;
				}
			}

		}

		// no matches
		return false;
	}

	/**
	* check whether feed conditional logic rules match, meaning that feed will be executed on submit
	* @param {String} form_id
	* @param {Object} logic
	* @return {Bool}
	*/
	function feedRulesMatch(form_id, logic) {

		function meetsAny(form_id, rules) {
			for (var i = 0, len = rules.length; i < len; i++) {
				if (gf_is_match(form_id, rules[i])) {
					return true;
				}
			}
			return false;
		}

		function meetsAll(form_id, rules) {
			for (var i = 0, len = rules.length; i < len; i++) {
				if (!gf_is_match(form_id, rules[i])) {
					return false;
				}
			}
			return true;
		}

		if (logic.logicType === "any") {
			return meetsAny(form_id, logic.rules);
		}
		else {
			return meetsAll(form_id, logic.rules);
		}
	}

})(jQuery);

