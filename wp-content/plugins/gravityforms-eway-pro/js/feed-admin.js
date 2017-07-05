// Gravity Forms eWAY Pro feed admin script

(function($) {

	/**
	* hide/reveal elements by selector
	* @param {bool} isVisible
	* @param {String} selector
	*/
	function setVisibility(isVisible, selector) {
		var elements = $(selector);

		if (isVisible) {
			elements.show();
		}
		else {
			elements.hide();
		}
	}

	/**
	* show/hide Recurring Times mapped fields
	*/
	function recurringTimesMapped() {
		setVisibility($("#recurringTimes_times").val() === "-1", "#recurringTimes_mapped");
	}

	/**
	* show/hide Recurring Billing Cycle mapped fields
	*/
	function billingCycleMapped() {
		setVisibility($("#billingCycle_length").val() === "-1", "#billingCycle_mapped");
	}

	/**
	* show/hide Recurring Start mapped fields
	*/
	function recurringStartMapped() {
		setVisibility($("#recurringStart_length").val() === "-1", "#recurringStart_mapped");
	}

	/**
	* hide Remember Customer Without Transaction field
	*/
	function rememberCustomerNoTx() {
		setVisibility($("input[name='_gaddon_setting_rememberCustomer']:checked").val() !== "off", "#gaddon-setting-checkbox-choice-remembercustomer_no_tx");
	}

	/**
	* hide Recurring Start length fields
	*/
	function hideRecurringStartLength() {
		$("#recurringStart_length").val(1).hide();
	}

	/**
	* hide/reveal custom connection fields
	*/
	$("#custom_connection").change(function() {
		setVisibility(this.checked, "#gfewaypro-settings-connection");
	}).trigger("change");

	/**
	* hide/reveal based on integration method option
	*/
	$("input[name='_gaddon_setting_feedMethod']").change(function() {
		// only process for the selected input
		if (!this.checked) return;

		function paymentMethod(isVisible) {
			setVisibility(isVisible, "#gaddon-setting-row-paymentMethod");
		}

		function tokens(isVisible) {
			setVisibility(isVisible, "#gfewaypro-settings-token-payments");

			if (isVisible) {
				rememberCustomerNoTx();
			}
		}

		function sharedPage(isVisible) {
			setVisibility(isVisible, "#gfewaypro-settings-shared");

			// only show deprecated notification settings if shared page and have delayNotify set
			var deprecatedNotify = isVisible ? +$("input[name='_gaddon_setting_delayNotify']").val() : false;
			setVisibility(deprecatedNotify, "#gfewaypro-settings-deprecated-notification");
		}

		function shipping(isVisible) {
			if (!isVisible) {
				$("input[name='_gaddon_setting_shippingAddress'][value='empty']").prop("checked", true).change();
			}
			setVisibility(isVisible, "#gaddon-setting-row-shippingAddress");
		}

		function recurring(isVisible) {
			setVisibility(isVisible, "#gfewaypro-settings-recurring");

			if (isVisible) {
				recurringTimesMapped();
			}
		}

		function options(isVisible) {
			setVisibility(isVisible, "#gaddon-setting-row-mappedFields tr:has(select[id^='mappedFields_option'])");
		}

		switch (this.value) {

			case "shared":
				paymentMethod(true);
				tokens(true);
				sharedPage(true);
				shipping(true);
				recurring(false);
				options(true);
				break;

			case "recurxml":
				paymentMethod(false);
				tokens(false);
				sharedPage(false);
				shipping(false);
				recurring(true);
				options(false);
				break;

			default:
				paymentMethod(true);
				tokens(true);
				sharedPage(false);
				shipping(true);
				recurring(false);
				options(true);
				break;

		}
	}).trigger("change");

	/**
	* hide/reveal recurring times fields on value changes
	*/
	$("#recurringTimes_times").change(recurringTimesMapped);

	/**
	* hide/reveal recurring times fields on value changes
	*/
	$("#billingCycle_length").change(billingCycleMapped);

	/**
	* hide/reveal recurring times fields on value changes
	*/
	$("#recurringStart_length").change(recurringStartMapped);

	/**
	* hide/reveal Remember Customer options
	*/
	$("input[name='_gaddon_setting_rememberCustomer']").change(rememberCustomerNoTx);

	/**
	* hide/reveal shipping address field mappings
	*/
	$("input[name='_gaddon_setting_shippingAddress']").change(function() {
		// only process for the selected input
		if (!this.checked) return;

		var shippingRows = $("#gaddon-setting-row-mappedFields select[id^='mappedFields_ship']").closest("tr");

		if (this.value === "mapped") {
			shippingRows.show();
		}
		else {
			shippingRows.hide();
		}
	}).trigger("change");

	/**
	* change billing cycle length options when billing cycle units change
	*/
	$("#billingCycle_unit").on("change", function() {
		var unit = $(this).val();
		var args = gfewaypro_feed.billing;
		var cycle = args.cycles[unit];

		var options = "<option value='-1'>" + args.msg.mapped + "</option><option value='" + cycle.min + "' selected='selected'>" + cycle.min + "</option>";

		for (var i = cycle.min + 1; i <= cycle.max; i++) {
			options += "<option value='" + i + "'>" + i + "</option>";
		}

		$("#billingCycle_length").html(options);
		billingCycleMapped();
	});

	/**
	* change recurring start options when recurring start units change
	*/
	$("#recurringStart_unit").on("change", function() {
		var unit = $(this).val();
		var args = gfewaypro_feed.startbilling;
		var cycle = args.cycles[unit];

		switch (unit) {

			case "now":
				hideRecurringStartLength();
				setVisibility(false, "#recurringStart_date");
				break;

			case "mapped":
				hideRecurringStartLength();
				setVisibility(true, "#recurringStart_date");
				break;

			default:
				var options = "<option value='-1'>" + args.msg.mapped + "</option><option value='" + cycle.min + "' selected='selected'>" + cycle.min + "</option>";

				for (var i = cycle.min + 1; i <= cycle.max; i++) {
					options += "<option value='" + i + "'>" + i + "</option>";
				}

				$("#recurringStart_length").html(options).show();
				setVisibility(false, "#recurringStart_date");
				break;

		}

		recurringStartMapped();
	});

	/**
	* hide/show delayed notifications
	*/
	$("#delaynotify").on("change", function() {
		setVisibility(this.checked, "#gaddon-setting-row-delayNotifications");
	}).trigger("change");

	/**
	* record changes in delay notifications individual checkboxes
	*/
	$("input.gfeway-notification-checkbox").on("click change", function() {
		var notifications = {};

		$('.gfeway-notification-checkbox').each(function() {
			notifications[this.value] = this.checked ? 1 : 0;
		});

		$('#delayNotifications').val($.toJSON(notifications));
	});

})(jQuery);
