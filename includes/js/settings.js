jQuery(document).ready(function () {	
	//show and hide attribute mapping instructions
    jQuery("#toggle_am_content").click(function () {
        jQuery("#show_am_content").toggle();
    });

	/*
	 * Help & Troubleshooting
	 */
	 
	//Enable cURL
	jQuery("#help_curl_enable_title").click(function () {
        jQuery("#help_curl_enable_desc").slideToggle(400);
    });
	
	//Widget steps
	jQuery("#help_widget_steps_title").click(function () {
        jQuery("#help_widget_steps_desc").slideToggle(400);
    });
	 
	 //Instructions
	 jQuery("#help_steps_title").click(function () {
        jQuery("#help_steps_desc").slideToggle(400);
    });
	
	//Working of plugin
	 jQuery("#help_working_title").click(function () {
        jQuery("#help_working_desc").slideToggle(400);
    });
	
	//What is SAML
	 jQuery("#help_saml_title").click(function () {
        jQuery("#help_saml_desc").slideToggle(400);
    });
	
	//SAML flows
	 jQuery("#help_saml_flow_title").click(function () {
        jQuery("#help_saml_flow_desc").slideToggle(400);
    });
	
	//FAQ - certificate
	 jQuery("#help_faq_cert_title").click(function () {
        jQuery("#help_faq_cert_desc").slideToggle(400);
    });
	
	//FAQ - 404 error
	 jQuery("#help_faq_404_title").click(function () {
        jQuery("#help_faq_404_desc").slideToggle(400);
    });
	
	//FAQ - idp not configured properly issue
	 jQuery("#help_faq_idp_config_title").click(function () {
        jQuery("#help_faq_idp_config_desc").slideToggle(400);
    });
	
	//FAQ - redirect to idp issue
	 jQuery("#help_faq_idp_redirect_title").click(function () {
        jQuery("#help_faq_idp_redirect_desc").slideToggle(400);
    });
});