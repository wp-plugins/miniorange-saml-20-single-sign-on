jQuery(document).ready(function () {
	
	var googleEnabled = jQuery("#google_enable").is(":checked");
	var eveEnabled = jQuery("#eve_enable").is(":checked");
	var facebookEnabled = jQuery("#facebook_enable").is(":checked");
	
	if(!googleEnabled) {
		jQuery("#panel2").toggle();
	}
	if(!eveEnabled) {
		jQuery("#panel3").toggle();
	}
	if(!facebookEnabled) {
		jQuery("#panel4").toggle();
	}
	
	//show and hide instructions
    jQuery("#api_help").click(function () {
        jQuery("#api_instru").toggle();
    });
	jQuery("#eve_help").click(function () {
        jQuery("#eve_instru").toggle();
    });
	jQuery("#google_help").click(function () {
        jQuery("#google_instru").toggle();
    });
	jQuery("#facebook_help").click(function () {
        jQuery("#facebook_instru").toggle();
    });
	
	//toggle content
	jQuery("#toggle2").click(function() {
		jQuery("#panel2").toggle();
	});
	jQuery("#toggle3").click(function() {
		jQuery("#panel3").toggle();
	});
	jQuery("#toggle4").click(function() {
		jQuery("#panel4").toggle();
	});
});