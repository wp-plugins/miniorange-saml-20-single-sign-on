<?php
function mo_register_saml_sso() {
	?>
<div id="tab">
	<h2 class="nav-tab-wrapper">
		<a class="nav-tab nav-tab-active"
			href="admin.php?page=mo_saml_settings">Configure SAML Identity Provider for SSO</a> <a
			class="nav-tab" href="admin.php?page=mo_cross_domain_saml">SSO to another Wordpress site
			</a>
	</h2>
</div>
<div id="mo_saml_settings">

	<div class="miniorange_container">
	<table style="width:100%;">
		<tr>
			<td style="vertical-align:top;width:65%;">
		<?php
		if(_is_curl_installed())
		{
			if (get_option ( 'mo_saml_verify_customer' ) == 'true') {
				mo_saml_show_verify_password_page_saml();
			} else if (trim ( get_option ( 'mo_saml_admin_email' ) ) != '' && trim ( get_option ( 'mo_saml_admin_api_key' ) ) == '' && get_option ( 'mo_saml_new_registration' ) != 'true') {
				mo_saml_show_verify_password_page_saml();
			}else if(get_option('mo_saml_registration_status') == 'MO_OTP_DELIVERED_SUCCESS' || get_option('mo_saml_registration_status') == 'MO_OTP_VALIDATION_FAILURE' || get_option('mo_saml_registration_status') == 'MO_OTP_DELIVERED_FAILURE' ){
				mo_saml_show_otp_verification();
			}	else if (! mo_saml_is_customer_registered_saml()) {
				delete_option ( 'password_mismatch' );
				mo_saml_show_new_registration_page_saml();
			} else {
				mo_saml_apps_config_saml ();
			}
	
		}
		else{
			
			echo "CURL NOT ENABLED. You need to enable curl in your PHP to use this plugin.";
		}
	?>
			</td>
			<td style="vertical-align:top;padding-left:1%;">
						<?php echo miniorange_support_saml(); ?>	
					</td>
		</tr>
	</table>
	</div>
			
<?php			
}

function _is_curl_installed() {
    if  (in_array  ('curl', get_loaded_extensions())) {
        return 1;
    } else 
        return 0;
}


function mo_saml_show_new_registration_page_saml() {
	update_option ( 'mo_saml_new_registration', 'true' );
	?>
			<!--Register with miniOrange-->
		<form name="f" method="post" action="">
			<input type="hidden" name="option" value="mo_saml_register_customer" />
			<div class="mo_table_layout">
				<div id="toggle1" class="panel_toggle">
					<h3>Register with miniOrange</h3>
				</div>
				<div id="panel1">
					<!--<p><b>Register with miniOrange</b></p>-->
					</p>
					<table class="mo_settings_table">
						<tr>
							<td><b><font color="#FF0000">*</font>Email:</b></td>
							<td><input class="mo_table_textbox" type="email" name="email"
								required placeholder="person@example.com"
								value="<?php echo get_option('mo_saml_admin_email');?>" /></td>
						</tr>

						<tr>
							<td><b><font color="#FF0000">*</font>Phone number:</b></td>
							<td><input class="mo_table_textbox" type="tel" id="phone_contact"
								pattern="[\+]\d{11,14}|[\+]\d{1,4}[\s]\d{9,10}" class="mo_table_textbox" name="phone" required
								title="Phone with country code eg. +1xxxxxxxxxx"
								placeholder="Phone with country code eg. +1xxxxxxxxxx"
								value="<?php echo get_option('mo_saml_admin_phone');?>" /></td>
						</tr>
						<tr>
							<td><b><font color="#FF0000">*</font>Password:</b></td>
							<td><input class="mo_table_textbox" required type="password"
								name="password" placeholder="Choose your password (Min. length 8)" /></td>
						</tr>
						<tr>
							<td><b><font color="#FF0000">*</font>Confirm Password:</b></td>
							<td><input class="mo_table_textbox" required type="password"
								name="confirmPassword" placeholder="Confirm your password" /></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td><input type="submit" name="submit" value="Save"
								class="button button-primary button-large" /></td>
						</tr>
					</table>
				</div>
			</div>
		</form>
		<?php
}
function mo_saml_show_verify_password_page_saml() {
	?>
			<!--Verify password with miniOrange-->
		<form name="f" method="post" action="">
			<input type="hidden" name="option" value="mo_saml_verify_customer" />
			<div class="mo_table_layout">
				<div id="toggle1" class="panel_toggle">
					<h3>Login with miniOrange</h3>
				</div>
				<div id="panel1">
					</p>
					<table class="mo_settings_table">
						<tr>
							<td><b><font color="#FF0000">*</font>Email:</b></td>
							<td><input class="mo_table_textbox" type="email" name="email"
								required placeholder="person@example.com"
								value="<?php echo get_option('mo_saml_admin_email');?>" /></td>
						</tr>
						<tr>
						<td><b><font color="#FF0000">*</font>Password:</b></td>
						<td><input class="mo_table_textbox" required type="password"
							name="password" placeholder="Choose your password" /></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td><input type="submit" name="submit"
								class="button button-primary button-large" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a
								target="_blank"
								href="<?php echo get_option('mo_saml_host_name') . "/moas/idp/userforgotpassword"; ?>">Forgot
									your password?</a></td>
						</tr>
					</table>
				</div>
			</div>
		</form>
		<?php
}

function mo_cross_domain_saml_config() {
	?>
<div id="tab">
	<h2 class="nav-tab-wrapper">
		<a class="nav-tab"
			href="admin.php?page=mo_saml_settings">Configure SAML Identity Provider for SSO</a> <a class="nav-tab  nav-tab-active"
			 href="admin.php?page=mo_cross_domain_saml">SSO to another Wordpress site
			</a>
	</h2>
</div>
<div id="mo_cross_domain_saml">
	<?php   if (! mo_saml_is_customer_registered_saml()) {
	?>
			<div class="miniorange_container">
				<h2>Please Register to miniOrange to use this feature.</h2>
			</div>
	<?php
	}
	else {
		?>
	<h2 class="mo_heading_margin">
			Wordpress SSO to another site
		</h2>
		<div class="miniorange_container">
		<table style="width:100%;">
		<tr>
		
			<td style="vertical-align:top;width:65%;">
			<!--Wordpress SSO to another site. Cross domain SAML-->
		<form name="f" method="post" action="">
		
		<input type="hidden" name="option" value="login_widget_cross_domain_save_settings" />
		<table width="98%" border="0" style="background-color:#FFFFFF; border:1px solid #CCCCCC; padding:0px 0px 0px 10px; margin:2px;">
		 <tr>
			<td width="60%"><h2>Configure your Wordpress domain for Single Sign On:</h2></td>
			<td width="40%">&nbsp;</td>
		  </tr>
		 <tr>
			<td><strong>Enter the absolute URL of another Wordpress Site: </strong></td>
			<td><input type="url" pattern="https?://.+" size="35" name="cd_destination_site_url" value="<?php echo get_option('cd_destination_site_url'); ?>" required/></td>
		  </tr>
		  <tr>
			<td><strong>Enter any random shared secret key</td>
			<td><input type="text" size="35" name="cd_shared_key" value="<?php echo get_option('cd_shared_key'); ?>" /></td>
		  </tr>
		  <tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="submit" value="Save" class="button button-primary button-large" /></td>
		  </tr>
		
		  <tr>
			<td colspan="2"><?php mo_login_cd_help();?></td>

		  </tr>
		</table>
		</form>
		</td>
		<td style="vertical-align:top;padding-left:1%;">
						<?php echo miniorange_support_saml(); ?>	
		</td>
		</tr>
		</table>
		</div>
	<?php } ?>
</div>
		<?php
}


function mo_saml_apps_config_saml() {
	
		global $wpdb;
		$entity_id = get_option('entity_id');
		if(!$entity_id) { 
			$entity_id = 'https://auth.miniorange.com/moas';
		}
		$sso_url = get_option('sso_url');
		$cert_fp = get_option('cert_fp');
		
		//Broker Service

		$saml_identity_name = get_option('saml_identity_name');
		$saml_login_url = get_option('saml_login_url');
		$saml_logout_url = get_option('saml_logout_url');
		$saml_issuer = get_option('saml_issuer');
		$saml_x509_certificate = get_option('saml_x509_certificate');
		$saml_response_signed = get_option('saml_response_signed');
		if($saml_response_signed == NULL) {$saml_response_signed = 'Yes'; }
		$saml_assertion_signed = get_option('saml_assertion_signed');
		if($saml_assertion_signed == NULL) {$saml_assertion_signed = 'Yes'; }
		
		
		?>

		<form name="saml_form" method="post" action="">
		<input type="hidden" name="option" value="login_widget_saml_save_settings" />
		<table width="98%" border="0" style="background-color:#FFFFFF; border:1px solid #CCCCCC; padding:0px 0px 0px 10px; margin:2px;">
		  <tr>
			<td width="45%"><h2>Add Identity Provider:</h2></td>
			<td width="55%">&nbsp;</td>
		  </tr>
		 
		<tr>
			<td><strong>Identity Provider Name *:</strong></td>
			<td><input type="text" name="saml_identity_name" style="width: 300px;" value="<?php echo $saml_identity_name;?>" required/></td>
		  </tr>
		  <tr>
			<td><strong>Saml Login URL *:</strong></td>
			<td><input type="url" name="saml_login_url" style="width: 300px;" value="<?php echo $saml_login_url;?>" required/></td>
		  </tr>
		  <tr>
			<td><strong>Saml Logout URL:</strong></td>
			<td><input type="url" name="saml_logout_url" style="width: 300px;" value="<?php echo $saml_logout_url;?>" /></td>
		  </tr>
		  <tr>
		  <td><strong>IDP Entity ID *:</strong></td>
			<td><input type="text" name="saml_issuer" style="width: 300px;" value="<?php echo $saml_issuer;?>" required/></td>
		  </tr>
		   <tr>
		  <td><strong>Copy and Paste SAML X-509 Certificate text:</strong></td>
			<td><textarea rows="4" cols="5" name="saml_x509_certificate" style="width: 300px;"><?php echo $saml_x509_certificate;?></textarea></td>
		  </tr>
		  <tr>
		  <td><br><strong>Response Signed:</strong></td>
			<td><input type="checkbox" name="saml_response_signed" value="Yes" <?php echo $saml_response_signed; ?> /></td>
		  </tr>
		  <tr>
		  <td><br><strong>Assertion Signed:</strong></td>
			<td><input type="checkbox" name="saml_assertion_signed" value="Yes" <?php echo $saml_assertion_signed; ?> /></td>
		  </tr>
		  
		  
		  <tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="submit" value="Save" class="button button-primary button-large" /> &nbsp; 
			<?php if($saml_identity_name != null) { ?>
			<input type="button" name="test" onclick="showTestWindow();" value="Test the saved configuration" class="button button-primary button-large" />
			<?php } ?>
			</td>
		  </tr>
	
		  <tr>
			<td colspan="2"><?php mo_login_help();?></td>

		  </tr>
		</table>
		</form>
		
	
</div>
<?php
}
function mo_saml_get_test_url(){
	
	$url = get_option('mo_saml_host_name') . '/moas/rest/saml/request?id=' . get_option('mo_saml_admin_customer_key') . '&returnurl=' . urlencode( site_url() . "/?option=readsamllogin" );
	return $url;
}
function mo_saml_is_customer_registered_saml() {
			$email 			= get_option('mo_saml_admin_email');
			$phone 			= get_option('mo_saml_admin_phone');
			$customerKey 	= get_option('mo_saml_admin_customer_key');
			if( ! $email || ! $phone || ! $customerKey || ! is_numeric( trim( $customerKey ) ) ) {
				return 0;
			} else {
				return 1;
			}
}


function mo_login_help(){ ?>
		<p><font color="#FF0000"><strong>Note*</strong></font>
		<br />
		miniOrange SAML SSO Plugin acts as a SAML 2.0 Service Provider which can be configured to establish the trust between the plugin and various SAML 2.0 supported Identity Providers to securely authenticate the user to the Wordpress site.

		</p>
		<h2>Help :</h2>
	
		<p>
		
		<div id="miniorange_provider"></div>
			<h2>Configuring the plugin.</h2>
			<ol>
			<li>Login to the Identity Provider.</li>
			<li>Download the metadata.xml from your Identity provider if provided or else search for, "Configuring it as an IDP with SAML 2.0"</li>
			<li>Find the information in the metadata and enter it in the plugin configuration settings</li>
			<li><b>Identity Provider</b> : Enter the name of the Identity Provider. Example:  Salesforce.
			<br><b>Saml Login URL</b> :  Enter the Single Sign On service HTTP Redirect URL mentioned in the IDP Metadata.xml file or in the IDP configuration.
			<br><b>Saml Logout URL</b> : Enter the logout URL. Where you want the user to be redirected upon logout.
			<br><b>IDP Entity ID</b> : Enter the Identity Provider Entity ID mentioned in the IDP metadata.xml file or in the IDP configuration.
			<br><b>SAML X509 Certificate</b> : Copy and Paste the X509 Certificate from the Metadata.xml file mentioned in the certificate tags or in the IDP configuration.
			<br>Format of the certificate should be like:
			<br>-----BEGIN CERTIFICATE-----
			<br>XXXXXXXXXXXXXXXXXXXXXXXXXXX
			<br>-----END CERTIFICATE-----
			<br><b>Response Signed</b> : Check this if the Identity Provider is signing the Response Or Leave this unchecked by default, if no setting are provided by the IDP.
			<br><b>Assertion Signed</b>: Check this if the Identity Provider is signing the assertion Or Leave this unchecked by default, if no setting are provided by the IDP.
			</li>
			
			</ol>
		
		
			<h2>Configure the Identity Provider with the following settings: </h2>
			 

			 <ol>
				<li><b>ACS Url :</b>  https://auth.miniorange.com/moas/rest/saml/acs </li>
				<li><b>SP-EntityID/ISSUER  :</b> https://auth.miniorange.com/moas</li>
				<li><b>Subject Type</b>	 : Username/Email Address</li>
			 </ol>

	

		</p>	  
	  
	<?php }

function mo_login_cd_help(){ ?>
	<p><font color="#FF0000"><strong>Note*</strong></font>
			<br />
		 Here you can share the login between two Wordpress website(Cross domain,same domain or sub-domain) by simply adding the other Wordpress site URL in the settings, where you want the login to be shared. And adding the HTML button on the current Wordpress site which will login to the other Wordpress domain.Please make sure you have installed and enabled this plugin on both the Wordpress sites.
</p>
		  <p>
		 <b>Steps</b>
		 <ol>
			<li>Add the other Wordpress site URL in Wordpress SSO input field you want to share the login. </li>
			<li>Now you can add link anywhere in this Wordpress site(articles, post, widgets..) which will login you to other Wordpress Domain(Please note. The user must be logged in to the base website). Copy and paste the following link anywhere in your site to login into a different Wordpress DOMAIN. </li>
			<br><b>&lt;a href=&quot;<?php echo site_url(); ?>/?saml_redirect &quot; target=&quot;_blank&quot;&gt;Login to other domain&lt;/a&gt;</b>
			<br><br><li>Please make sure you have installed and enabled this plugin on both the Wordpress sites.</li>
		</ol>

		

		  </p>
		  
<?php }

function mo_saml_show_otp_verification(){
	?>
		
		<!-- Enter otp -->
		<form name="f" method="post" id="otp_form" action="">
		
			<input type="hidden" name="option" value="mo_saml_validate_otp" />
				<div class="mo_table_layout">
				
					
						<table class="mo_settings_table">
							<h3>Verify Your Email</h3>
							<tr>
								<td><b><font color="#FF0000">*</font>Enter OTP:</b></td>
								<td colspan="2"><input class="mo_table_textbox" autofocus="true" type="text" name="otp_token" required placeholder="Enter OTP" style="width:61%;" pattern="{6,8}"/>
								 &nbsp;&nbsp;<a style="cursor:pointer;" onclick="document.getElementById('resend_otp_form').submit();">resend otp</a></td>
							</tr>
							<tr><td colspan="3"></td></tr>
							<tr>

								<td>&nbsp;</td>
								<td style="width:17%">
								<input type="submit" name="submit" value="Validate OTP" class="button button-primary button-large" /></td>

		</form>
		<form name="f" id="resend_otp_form" method="post" action="">
							<td>

							<input type="hidden" name="option" value="mo_saml_resend_otp"/>
							</td>
							</tr>
		</form>
						</table>
						</div>

	
<?php
}


function miniorange_support_saml(){
?>
	<div class="mo_support_layout">
		<!--<h3>Support</h3>
		<div >
			<p>Your general questions can be asked in the plugin <a href="https://wordpress.org/support/plugin/miniorange-login-with-eve-online-google-facebook" target="_blank">support forum</a>.</p>
		</div>
		<div style="text-align:center;">
			<h4>OR</h4>
		</div>-->
		<div>
			<h3>Contact Us</h3>
			<form method="post" action="">
				<input type="hidden" name="option" value="mo_saml_contact_us_query_option" />
				<table class="mo_settings_table">
					<tr>
						<td><b><font color="#FF0000">*</font>Email:</b></td>
						<td><input type="email" class="mo_table_textbox" required name="mo_saml_contact_us_email" value="<?php echo get_option("mo_saml_admin_email"); ?>"></td>
					</tr>
					<tr>
						<td><b>Phone:</b></td>
						<td><input type="tel" id="contact_us_phone" pattern="[\+]\d{11,14}|[\+]\d{1,4}[\s]\d{9,10}" class="mo_table_textbox" name="mo_saml_contact_us_phone" value="<?php echo get_option('mo_saml_admin_phone');?>"></td>
					</tr>
					<tr>
						<td><b><font color="#FF0000">*</font>Query:</b></td>
						<td><textarea class="mo_table_textbox" onkeypress="mo_saml_valid_query(this)" onkeyup="mo_saml_valid_query(this)" onblur="mo_saml_valid_query(this)" required name="mo_saml_contact_us_query" rows="4" style="resize: vertical;"></textarea></td>
					</tr>
				</table>
				<div style="text-align:center;">
					<input type="submit" name="submit" style="margin:15px; width:100px;" class="button button-primary button-large" />
				</div>
			</form>
		</div>
	</div>
	<script>
		jQuery("#contact_us_phone").intlTelInput();
		jQuery("#phone_contact").intlTelInput();
		function mo_saml_valid_query(f) {
			!(/^[a-zA-Z?,.\(\)\/@ 0-9]*$/).test(f.value) ? f.value = f.value.replace(
					/[^a-zA-Z?,.\(\)\/@ 0-9]/, '') : null;
		}
		function showTestWindow() {
		var myWindow = window.open("<?php echo mo_saml_get_test_url(); ?>", "TEST SAML IDP", "width=600, height=600");	
		}
	</script>
<?php
}

?>