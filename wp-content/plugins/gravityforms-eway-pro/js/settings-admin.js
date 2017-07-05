// Gravity Forms eWAY Pro settings admin script

(function($) {

	// show button appropriate for status
	switch ($("#gfewaypro-license-status").data("license-status")) {

		case "valid":
			$("#gfewaypro-license-status").hide();
			$("#gfewaypro-license-deactivate").show();
			break;

		case "inactive":
		case "site_inactive":
			$("#gfewaypro-license-status").hide();
			$("#gfewaypro-license-activate").show();
			break;

	}

	var oldLicense = $("#eddLicense_key").val();
	var changeHandled = false;

	/**
	* if license is changed, require settings to be saved before activate/deactivate buttons can be clicked
	*/
	$("#eddLicense_key").on("change keyup", function() {
		if (!changeHandled && this.value !== oldLicense) {
			changeHandled = true;
			$("#gfewaypro-license-activate,#gfewaypro-license-deactivate,#gfewaypro-license-status").remove();
		}
	});

	/**
	* attempt to activate the license
	* @param {jQuery.Event} event
	*/
	$("#gfewaypro-license-activate").on("click", function(event) {
		event.preventDefault();
		$(this).css("cursor", "wait");

		$.getJSON(ajaxurl, { action: "gfewaypro-license-activate" }, licenseResponse);
	});

	/**
	* attempt to deactivate the license
	* @param {jQuery.Event} event
	*/
	$("#gfewaypro-license-deactivate").on("click", function(event) {
		event.preventDefault();
		$(this).css("cursor", "wait");

		$.getJSON(ajaxurl, { action: "gfewaypro-license-deactivate" }, licenseResponse);
	});

	/**
	* handle response to license action
	* @param {Object} response
	*/
	function licenseResponse(response) {

		$("#gfewaypro-license-activate,#gfewaypro-license-deactivate").css("cursor", "pointer");

		switch (response.status) {

			case "valid":
				$("#gfewaypro-license-activate").hide();
				$("#gfewaypro-license-deactivate").show();
				break;

			case "deactivated":
				$("#gfewaypro-license-activate").show();
				$("#gfewaypro-license-deactivate").hide();
				break;

			default:
				window.alert(response.status);
				break;

		}

	}

})(jQuery);
