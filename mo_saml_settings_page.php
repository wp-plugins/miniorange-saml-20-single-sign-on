<?php
function mo_register_saml_sso() {
	if( isset( $_GET[ 'tab' ] ) ) {
		$active_tab = $_GET[ 'tab' ];
	} else if(mo_saml_is_customer_registered_saml() && mo_saml_is_sp_configured()) {
		$active_tab = 'login';
	} else if(mo_saml_is_customer_registered_saml()) {
		$active_tab = 'config';
	} else {
		$active_tab = 'login';
	}
	?>
	<?php
		if(!_is_curl_installed()) {
			?>
			<p><font color="#FF0000">(Warning: <a href="http://php.net/manual/en/curl.installation.php" target="_blank">PHP cURL extension</a> is not installed or disabled)</font></p>
			<?php
		}
	?>
<div id="mo_saml_settings">
	<div class="miniorange_container">
	<table style="width:100%;">
		<tr>
			<h2 class="nav-tab-wrapper">
				<a class="nav-tab <?php echo $active_tab == 'login' ? 'nav-tab-active' : ''; ?>" href="<?php echo add_query_arg( array('tab' => 'login'), $_SERVER['REQUEST_URI'] ); ?>"><?php if(mo_saml_is_customer_registered_saml())echo 'General'; else echo 'Account Setup';?></a>
				<a class="nav-tab <?php echo $active_tab == 'config' ? 'nav-tab-active' : ''; ?>" href="<?php echo add_query_arg( array('tab' => 'config'), $_SERVER['REQUEST_URI'] ); ?>">Identity Provider</a>
				<a class="nav-tab <?php echo $active_tab == 'save' ? 'nav-tab-active' : ''; ?>" href="<?php echo add_query_arg( array('tab' => 'save'), $_SERVER['REQUEST_URI'] ); ?>">Service Provider</a>
				<a class="nav-tab <?php echo $active_tab == 'opt' ? 'nav-tab-active' : ''; ?>" href="<?php echo add_query_arg( array('tab' => 'opt'), $_SERVER['REQUEST_URI'] ); ?>">Attribute/Role Mapping</a>
				<a class="nav-tab <?php echo $active_tab == 'help' ? 'nav-tab-active' : ''; ?>" href="<?php echo add_query_arg( array('tab' => 'help'), $_SERVER['REQUEST_URI'] ); ?>">Help & FAQ</a>
			</h2>
			<td style="vertical-align:top;width:65%;">
			<?php
				if($active_tab == 'save') {
					mo_saml_apps_config_saml();
				} else if($active_tab == 'opt') {
					mo_saml_save_optional_config();
				} else if($active_tab == 'help') {
					mo_saml_save_plugin_config();
				} else if($active_tab == 'config'){
					mo_saml_configuration_steps();
				} else {
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
						mo_saml_general_login_page();
					}
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
			<div class="mo_saml_table_layout">
				<div id="toggle1" class="panel_toggle">
					<h3>Register with miniOrange</h3>
				</div>
				<div id="panel1">
					<!--<p><b>Register with miniOrange</b></p>-->
					</p>
					<table class="mo_saml_settings_table">
						<tr>
							<td><b><font color="#FF0000">*</font>Email:</b></td>
							<td><input class="mo_saml_table_textbox" type="email" name="email"
								required placeholder="person@example.com"
								value="<?php echo get_option('mo_saml_admin_email');?>" /></td>
						</tr>

						<tr>
							<td><b><font color="#FF0000">*</font>Phone number:</b></td>
							<td><input class="mo_saml_table_textbox" type="tel" id="phone_contact"
								pattern="[\+]\d{11,14}|[\+]\d{1,4}[\s]\d{9,10}" class="mo_saml_table_textbox" name="phone" required
								title="Phone with country code eg. +1xxxxxxxxxx"
								placeholder="Phone with country code eg. +1xxxxxxxxxx"
								value="<?php echo get_option('mo_saml_admin_phone');?>" /></td>
						</tr>
						<tr>
								<td></td>
								<td>We will call only if you need support.</td>
							</tr>
						<tr>
							<td><b><font color="#FF0000">*</font>Password:</b></td>
							<td><input class="mo_saml_table_textbox" required type="password"
								name="password" placeholder="Choose your password (Min. length 8)" /></td>
						</tr>
						<tr>
							<td><b><font color="#FF0000">*</font>Confirm Password:</b></td>
							<td><input class="mo_saml_table_textbox" required type="password"
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
			<div class="mo_saml_table_layout">
				<div id="toggle1" class="panel_toggle">
					<h3>Login with miniOrange</h3>
				</div>
				<div id="panel1">
					</p>
					<table class="mo_saml_settings_table">
						<tr>
							<td><b><font color="#FF0000">*</font>Email:</b></td>
							<td><input class="mo_saml_table_textbox" type="email" name="email"
								required placeholder="person@example.com"
								value="<?php echo get_option('mo_saml_admin_email');?>" /></td>
						</tr>
						<tr>
						<td><b><font color="#FF0000">*</font>Password:</b></td>
						<td><input class="mo_saml_table_textbox" required type="password"
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

function mo_saml_show_otp_verification(){
	?>
		<!-- Enter otp -->
		<form name="f" method="post" id="otp_form" action="">
			<input type="hidden" name="option" value="mo_saml_validate_otp" />
			<div class="mo_saml_table_layout">
				<table class="mo_saml_settings_table">
					<h3>Verify Your Email</h3>
					<tr>
						<td><b><font color="#FF0000">*</font>Enter OTP:</b></td>
						<td colspan="2"><input class="mo_saml_table_textbox" autofocus="true" type="text" name="otp_token" required placeholder="Enter OTP" style="width:61%;" pattern="{6,8}"/>
						 &nbsp;&nbsp;<a style="cursor:pointer;" onclick="document.getElementById('resend_otp_form').submit();">resend otp</a></td>
					</tr>
					<tr><td colspan="3"></td></tr>
					<tr>

						<td>&nbsp;</td>
						<td style="width:17%">
						<input type="submit" name="submit" value="Validate OTP" class="button button-primary button-large" /></td>

		</form>
		<form name="f" method="post">
						<td style="width:18%">
							<input type="hidden" name="option" value="mo_saml_go_back"/>
							<input type="submit" name="submit"  value="Back" class="button button-primary button-large" />
						</td>
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

function mo_saml_general_login_page() {
	?>
		<?php if(mo_saml_is_customer_registered_saml()){ ?>
			<div style="background-color:#FFFFFF; border:1px solid #CCCCCC; padding:0px 0px 0px 10px; width:98%;height:365px">
				<div>
					<h3>Sign in options</h3>
					<div style="margin-left:10px;">
						<form id="mo_saml_enable_redirect_form" method="post" action="">	
							<input type="hidden" name="option" value="mo_saml_enable_login_redirect_option"/>
							<p><input type="checkbox" name="mo_saml_enable_login_redirect" value="true" <?php checked(get_option('mo_saml_enable_login_redirect') == 'true');?> onchange="document.getElementById('mo_saml_enable_redirect_form').submit();"/> &nbsp;Redirect user to IdP when accessing <b>/wp-login.php</b> or <b>/wp-admin</b></p>
						</form>
						<form id="mo_saml_allow_wp_signin_form" method="post" action="">	
							<input type="hidden" name="option" value="mo_saml_allow_wp_signin_option"/>
							<p><input type="checkbox" name="mo_saml_allow_wp_signin" value="true" <?php checked(get_option('mo_saml_allow_wp_signin') == 'true');?> <?php if(get_option('mo_saml_enable_login_redirect') != 'true') echo 'disabled'?>
									onchange="document.getElementById('mo_saml_allow_wp_signin_form').submit();"/> &nbsp;&nbsp;Allow users to sign in using wordpress credentials.
								<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<i>(Use <b><?php echo site_url(); ?>/wp-login.php?saml_sso=false</b>)</i>
							</p>
						</form>
						<p>Use the bellow short code where you want to display the Login link:</p>
						<code>&lt;a href="<?php echo get_option('mo_saml_host_name');?>/moas/rest/saml/request?id=<?php echo get_option('mo_saml_admin_customer_key')?>&returnurl=<?php echo urlencode( site_url() . '/?option=readsamllogin' ); ?>" />Login with IdP&lt;/a></code>
						<br />
						<p style="margin-left:200px;font-size:11pt;font-weight:bold">OR</p>
						<p>Add a Widget to your page:</p>
						<ol>
							<li>Go to Appearances > Widgets.</li>
							<li>Select "Login with &lt;Identity Provider>". Drag and drop to your favourite location and save.</li>
						</ol>
					</div>
				</div>
			</div>
			<br />
			<div style="background-color:#FFFFFF; border:1px solid #CCCCCC; padding:0px 0px 0px 10px; width:98%;height:300px">
				<div>
					<h3>Your Profile</h3>
					<table border="1" style="background-color:#FFFFFF; border:1px solid #CCCCCC; border-collapse: collapse; padding:0px 0px 0px 10px; margin:2px; width:85%">
						<tr>
							<td style="width:45%; padding: 10px;">miniOrange Account Email</td>
							<td style="width:55%; padding: 10px;"><?php echo get_option('mo_saml_admin_email')?></td>
						</tr>
						<tr>
							<td style="width:45%; padding: 10px;">Customer ID</td>
							<td style="width:55%; padding: 10px;"><?php echo get_option('mo_saml_admin_customer_key')?></td>
						</tr>
						<tr>
							<td style="width:45%; padding: 10px;">API Key</td>
							<td style="width:55%; padding: 10px;"><?php echo get_option('mo_saml_admin_api_key')?></td>
						</tr>
						<tr>
							<td style="width:45%; padding: 10px;">Token Key</td>
							<td style="width:55%; padding: 10px;"><?php echo get_option('mo_saml_customer_token')?></td>
						</tr>
					</table>
					<br/>
					<p><a target="_blank" href="<?php echo get_option('mo_saml_host_name') . "/moas/idp/userforgotpassword"; ?>">Click here</a> if you forgot your password to your miniOrange account.</p>
				</div>
			</div>
	<?php }
}

function mo_saml_configuration_steps() {
	?>
	<form  name="saml_form_am" method="post" action="" id="mo_saml_idp_config">
		<input type="hidden" name="option" value="mo_saml_idp_config" />
		<div id="instructions_idp"></div>
		<table width="98%" border="0" style="background-color:#FFFFFF; border:1px solid #CCCCCC; padding:0px 0px 20px 10px;">
		<tr>
			<?php if(!mo_saml_is_customer_registered_saml()) { ?>
				<td colspan="2"><div style="display:block;margin-top:10px;color:red;background-color:rgba(251, 232, 0, 0.15);padding:5px;border:solid 1px rgba(255, 0, 9, 0.36);">Please <a href="<?php echo add_query_arg( array('tab' => 'login'), $_SERVER['REQUEST_URI'] ); ?>">Register or Login with miniOrange</a> to configure the miniOrange SAML Plugin.</div></td>
			<?php } ?>
		</tr>
		<tr>
			<td colspan="2">
				<p><b>For any help on how to configure your IdP, checkout the <a href="<?php echo add_query_arg( array('tab' => 'help'), $_SERVER['REQUEST_URI'] ); ?>">Help section</a>.</b></p>
				<h4>You will need the following information to configure your IdP:</h4>
				<table border="1" style="background-color:#FFFFFF; border:1px solid #CCCCCC; padding:0px 0px 0px 10px; margin:2px; border-collapse: collapse; width:98%">
					<tr>
						<td style="width:40%; padding: 15px;"><b>ACS (AssertionConsumerService) URL</b></td>
						<td style="width:60%;  padding: 15px;">https://auth.miniorange.com/moas/rest/saml/acs</td>
					</tr>
					<tr>
						<td style="width:40%; padding: 15px;"><b>SP-EntityID / ISSUER</b></td>
						<td style="width:60%; padding: 15px;">https://auth.miniorange.com/moas</td>
					</tr>
					<tr>
						<td style="width:40%; padding: 15px;"><b>NameID format</b></td>
						<td style="width:60%; padding: 15px;">urn:oasis:names:tc:SAML:2.0:nameid-format:persistent<br /><b>OR</b><br />urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress</td>
					</tr>
					<tr>
						<td style="width:40%; padding: 15px;"><b>Recipient URL</b></td>
						<td style="width:60%;  padding: 15px;">https://auth.miniorange.com/moas/rest/saml/acs</td>
					</tr>
					<tr>
						<td style="width:40%; padding: 15px;"><b>Destination URL</b></td>
						<td style="width:60%;  padding: 15px;">https://auth.miniorange.com/moas/rest/saml/acs</td>
					</tr>
				</table>						
				<h4>If your Identity Provider has an option to configure Default Relay State (required for IdP Initiated login flow), set the following URL:</h4>
				<code><?php echo site_url(); ?>?option=readsamllogin&mId=<?php echo get_option('mo_saml_admin_customer_key') ?></code>			
				
				<h4>You will need the following IdP information for configuring Service Provider:</h4>
				<ol>
					<li><b>X.509 certificate</b> (enclosed in <code>X509Certificate</code> tag (parent tag: <code>KeyDescriptor use="signing"</code>) in IdP-Metadata XML file)</li>
					<li><b>SAML Login URL</b> (enclosed in <code>SingleSignOnService</code> tag in IdP-Metadata XML file, binding type HTTPRedirect)</li>
					<li><b>IdP Entity ID</b> (<code>entityID</code> attribute of <code>EntityDescriptor</code> tag in IdP-Metadata XML file)</li>
					<li><b>Is Response signed</b> by your IdP?</li>
					<li><b>Is Assertion signed</b> by your IdP?</li>
					</li>
				</ol>
			</td>
		</tr>
		<!--<tr>
			<td>
				<h4><input type="checkbox" id="idp_config_complete" name="mo_saml_idp_config_complete" value="1" <?php checked(get_option('mo_saml_idp_config_complete') == 1);?> <?php if(!mo_saml_is_customer_registered_saml()) echo 'disabled'?>/>I have completed the configuration of my Identity Provider</h4>
			</td>
		</tr>
		<tr>
			<td><input type="submit" name="submit" value="Save" class="button button-primary button-large" <?php if(!mo_saml_is_customer_registered_saml()) echo 'disabled'?>/></td>
		</tr>-->
		</table>
	</form>
	
	<script type="text/javascript">
		jQuery('#idp_config_complete').change(function() {
			jQuery('#mo_saml_idp_config').submit();
		});
   function changeFunc() {
    var selectBox = document.getElementById("selectBox");
    var selectedValue = selectBox.options[selectBox.selectedIndex].value;
	var selectedAppValue = selectBox.options[selectBox.selectedIndex].innerHTML;
	var div = document.getElementById('displayguide');
	div.innerHTML = '<br><b>How to configure ' + selectedAppValue + ':<b/> ';
    if(selectedValue == 'okta')
	{
		div.innerHTML = div.innerHTML +  '<a href="http://developer.okta.com/docs/guides/setting_up_a_saml_application_in_okta.html" target="_blank">http://developer.okta.com/docs/guides/setting_up_a_saml_application_in_okta.html </a>';
	}
	if(selectedValue == 'salesforce')
	{
		div.innerHTML = div.innerHTML +  '<a href="https://help.salesforce.com/HTViewHelpDoc?id=identity_provider_enable.htm&language=en_US" target="_blank">https://help.salesforce.com/HTViewHelpDoc?id=identity_provider_enable.htm&language=en_US</a>';		
	}
	if(selectedValue == 'openam')
	{
		div.innerHTML = div.innerHTML + '<a href="http://openam.forgerock.org/openam-documentation/openam-doc-source/doc/webhelp/admin-guide/deploy-idp-discovery.html" target="_blank">http://openam.forgerock.org/openam-documentation/openam-doc-source/doc/webhelp/admin-guide/deploy-idp-discovery.html</a>'
	}
	if(selectedValue == 'microsoftazuread')
	{
		div.innerHTML = div.innerHTML + '<a href="https://msdn.microsoft.com/en-us/library/azure/dn641269.aspx" target="_blank">https://msdn.microsoft.com/en-us/library/azure/dn641269.aspx</a>';
	}
	if(selectedValue == 'simplesamlphp')
	{
		div.innerHTML = div.innerHTML + '<a href="https://simplesamlphp.org/docs/1.5/simplesamlphp-idp" target="_blank">https://simplesamlphp.org/docs/1.5/simplesamlphp-idp</a>';
	}
	if(selectedValue == 'shibboleth')
	{
		div.innerHTML = div.innerHTML + '<a href="https://shibboleth.net/products/identity-provider.html" target="_blank">https://shibboleth.net/products/identity-provider.html</a>'	
	}
	if(selectedValue == 'pingidentity')
	{
		div.innerHTML = div.innerHTML + '<a href="https://www.pingidentity.com/en/resources/articles/saml.html" target="_blank">https://www.pingidentity.com/en/resources/articles/saml.html</a>';
	}
	if(selectedValue == 'miniOrange')
	{
		div.innerHTML = div.innerHTML + '<a href="http://miniorange.com/wordpress-single-sign-on-(sso)#samlwordpress" target="_blank">http://miniorange.com/wordpress-single-sign-on-(sso)#samlwordpress</a>';
	}
	
   }

  </script>
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
		$saml_issuer = get_option('saml_issuer');
		$saml_x509_certificate = get_option('saml_x509_certificate');
		$saml_response_signed = get_option('saml_response_signed');
		if($saml_response_signed == NULL) {$saml_response_signed = 'checked'; }
		$saml_assertion_signed = get_option('saml_assertion_signed');
		if($saml_assertion_signed == NULL) {$saml_assertion_signed = 'Yes'; }
		
		$idp_config = get_option('mo_saml_idp_config_complete');
		?>
		<form name="saml_form" method="post" action="">
		<input type="hidden" name="option" value="login_widget_saml_save_settings" />
		<table width="98%" border="0" style="background-color:#FFFFFF; border:1px solid #CCCCCC; padding:0px 0px 0px 10px;">
			<tr>
				<td colspan="2">
					<h3>Configure Service Provider
					<?php if($saml_identity_name != null) { ?>
					<input type="button" name="test" onclick="showTestWindow();" <?php if(!mo_saml_is_customer_registered_saml()) echo 'disabled'?> value="Test configuration" class="button button-primary button-large" style="float: right;margin-right: 3%;"/>
					<?php } ?>
					</h3>
				</td>
			</tr>
			<?php if(!mo_saml_is_customer_registered_saml()) { ?>
				<tr>
					<td colspan="2"><div style="display:block;color:red;background-color:rgba(251, 232, 0, 0.15);padding:5px;border:solid 1px rgba(255, 0, 9, 0.36);">Please <a href="<?php echo add_query_arg( array('tab' => 'login'), $_SERVER['REQUEST_URI'] ); ?>">Register or Login with miniOrange</a> to configure the miniOrange SAML Plugin.</div></td>
				</tr>
			<?php } ?>
			<?php if(!$idp_config && mo_saml_is_customer_registered_saml()) { ?>
				<!--<tr>
					<td colspan="2"><div style="display:block;color:red;background-color:rgba(251, 251, 0, 0.43);padding:5px;border:solid 1px yellow;">You skipped a step. Please complete your Identity Provider configuration before you can enter the fields given below. If you have already completed your IdP configuration, please confirm on <a href="<?php echo add_query_arg( array('tab' => 'config'), $_SERVER['REQUEST_URI'] ); ?>">Configure Identity Provider</a> page to remove this warning.</div></td>
				</tr>-->
			<?php } ?>
			<tr>
				<td colspan="2">Enter the information gathered from your Identity Provider<br /><br /></td>
			</tr>
			<tr>
				<td style="width:200px;"><strong>Identity Provider Name <span style="color:red;">*</span>:</strong></td>
				<td><input type="text" name="saml_identity_name" placeholder="Identity Provider name like Okta, ADFS, Ping, SimpleSAML" style="width: 95%;" value="<?php echo $saml_identity_name;?>" required <?php if(!mo_saml_is_customer_registered_saml()) echo 'disabled'?> pattern="^\w*$" title="Only alphabets, numbers and underscore is allowed"/></td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<td><strong>SAML Login URL <span style="color:red;">*</span>:</strong></td>
				<td><input type="url" name="saml_login_url" placeholder="Single Sign On Service URL (HTTP-Redirect binding) of your IdP" style="width: 95%;" value="<?php echo $saml_login_url;?>" required <?php if(!mo_saml_is_customer_registered_saml()) echo 'disabled'?>/></td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<td><strong>IdP Entity ID or Issuer <span style="color:red;">*</span>:</strong></td>
				<td><input type="text" name="saml_issuer" placeholder="Identity Provider Entity ID or Issuer" style="width: 95%;" value="<?php echo $saml_issuer;?>" required <?php if(!mo_saml_is_customer_registered_saml()) echo 'disabled'?>/></td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<td><strong>X.509 Certificate <span style="color:red;">*</span>:</strong></td>
				<td><textarea rows="4" cols="5" name="saml_x509_certificate" placeholder="Copy and Paste the content from the downloaded certificate or copy the content enclosed in 'X509Certificate' tag (has parent tag 'KeyDescriptor use=signing') in IdP-Metadata XML file" style="width: 95%;" <?php if(!mo_saml_is_customer_registered_saml()) echo 'disabled'?>><?php echo $saml_x509_certificate;?></textarea></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>Format of the certificate: <br/><b>-----BEGIN CERTIFICATE-----<br/>XXXXXXXXXXXXXXXXXXXXXXXXXXX<br/>-----END CERTIFICATE-----</b></i><br/>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<td><strong>Response Signed:</strong></td>
				<td><input type="checkbox" name="saml_response_signed" value="Yes" <?php echo $saml_response_signed; ?> <?php if(!mo_saml_is_customer_registered_saml()) echo 'disabled'?>/>
				Check if your IdP is signing the SAML Response. Leave checked by default.</td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<td><strong>Assertion Signed:</strong></td>
				<td><input type="checkbox" name="saml_assertion_signed" value="Yes" <?php echo $saml_assertion_signed; ?> <?php if(!mo_saml_is_customer_registered_saml()) echo 'disabled'?>/>
				Check if the IdP is signing the SAML Assertion. Leave unchecked by default.</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><br /><input type="submit" name="submit" style="width:100px;" value="Save" class="button button-primary button-large" <?php if(!mo_saml_is_customer_registered_saml()) echo 'disabled'?>/> &nbsp; 
					<br/><br/>
				</td>
			</tr>		
		</table>
		</form>
	<?php
}

function mo_saml_save_optional_config(){
	global $wpdb;
	$entity_id = get_option('entity_id');
	if(!$entity_id) { 
		$entity_id = 'https://auth.miniorange.com/moas';
	}
	$sso_url = get_option('sso_url');
	$cert_fp = get_option('cert_fp');
	
	$saml_identity_name = get_option('saml_identity_name');
	
	//Attribute mapping
	$saml_am_username = get_option('saml_am_username');	
	if($saml_am_username == NULL) {$saml_am_username = 'NameID'; }
	$saml_am_email = get_option('saml_am_email');
	if($saml_am_email == NULL) {$saml_am_email = 'NameID'; }
	$saml_am_first_name = get_option('saml_am_first_name');
	$saml_am_last_name = get_option('saml_am_last_name');
	$saml_am_group_name = get_option('saml_am_group_name');
	?>
		<form name="saml_form_am" method="post" action="">
		<input type="hidden" name="option" value="login_widget_saml_attribute_mapping" />
		<table width="98%" border="0" style="background-color:#FFFFFF; border:1px solid #CCCCCC; padding:0px 0px 0px 10px;">
		  <tr>
			<td colspan="2">
				<h3>Attribute Mapping
					<?php if($saml_identity_name != null) { ?>
					<input type="button" name="test" onclick="showTestWindow();" <?php if(!mo_saml_is_customer_registered_saml()) echo 'disabled'?> value="Test configuration" class="button button-primary button-large" style="float: right;margin-right: 3%;"/>
					<?php } ?>
				</h3>
			</td>
		  </tr>
		  <tr><td colspan="2">Use attribute name <code>NameID</code> if Identity is in the <i>NameIdentifier</i> element of the subject statement in SAML Response.<br /><br /></td></tr>
			<tr>
				<?php if(!mo_saml_is_customer_registered_saml()) { ?>
					<td colspan="2"><div style="display:block;color:red;background-color:rgba(251, 232, 0, 0.15);padding:5px;border:solid 1px rgba(255, 0, 9, 0.36);">Please <a href="<?php echo add_query_arg( array('tab' => 'login'), $_SERVER['REQUEST_URI'] ); ?>">Register or Login with miniOrange</a> to configure the miniOrange SAML Plugin.</div></td>
				<?php } ?>
			</tr>
			  <tr>
			  <td style="width:200px;"><strong>Login/Create Wordpress account by: </strong></td>
			  <td><select name="saml_am_account_matcher" id="saml_am_account_matcher" <?php if(!mo_saml_is_customer_registered_saml()) echo 'disabled'?>>
				  <option value="email"<?php if(get_option('saml_am_account_matcher') == 'email') echo 'selected="selected"' ; ?> >Email</option>
				  <option value="username"<?php if(get_option('saml_am_account_matcher') == 'username') echo 'selected="selected"' ; ?> >Username</option>
				</select>
			  </td>
			  </tr>
			  <tr>
				<td>&nbsp;</td>
				<td><i>Users in Wordpress will be searched (existing wordpress users) or created (new users) based on this attribute. Use Email by default.</i></td>
			  </tr>
			  <tr>
				<td style="width:150px;"><strong>Username <span style="color:red;">*</span>:</strong></td>
				<td><input type="text" name="saml_am_username" placeholder="Enter attribute name for Username" style="width: 350px;" value="<?php echo $saml_am_username;?>" required <?php if(!mo_saml_is_customer_registered_saml()) echo 'disabled'?>/></td>
			  </tr>
			  <tr>
				<td><strong>Email <span style="color:red;">*</span>:</strong></td>
				<td><input type="text" name="saml_am_email" placeholder="Enter attribute name for Email" style="width: 350px;" value="<?php echo $saml_am_email;?>" required <?php if(!mo_saml_is_customer_registered_saml()) echo 'disabled'?>/></td>
			  </tr>
			  <tr>
				<td><strong>First Name:</strong></td>
				<td><input type="text" name="saml_am_first_name" placeholder="Enter attribute name for First Name" style="width: 350px;" value="<?php echo $saml_am_first_name;?>" <?php if(!mo_saml_is_customer_registered_saml()) echo 'disabled'?>/></td>
			  </tr>
			  <tr>
				<td><strong>Last Name:</strong></td>
				<td><input type="text" name="saml_am_last_name" placeholder="Enter attribute name for Last Name" style="width: 350px;" value="<?php echo $saml_am_last_name;?>" <?php if(!mo_saml_is_customer_registered_saml()) echo 'disabled'?>/></td>
			  </tr>
			  <tr>
				<td><strong>Group/Role:</strong></td>
				<td><input type="text" name="saml_am_group_name" placeholder="Enter attribute name for Group/Role" style="width: 350px;" value="<?php echo $saml_am_group_name;?>" <?php if(!mo_saml_is_customer_registered_saml()) echo 'disabled'?>/></td>
			  </tr>
			  <tr>
				<td>&nbsp;</td>
				<td><br /><input type="submit" style="width:100px;" name="submit" value="Save" class="button button-primary button-large" <?php if(!mo_saml_is_customer_registered_saml()) echo 'disabled'?>/> &nbsp; 
				<br /><br />
				</td>
			  </tr>
			 </table>
			 </form>
			 <br />
			 <form name="saml_form_am_role_mapping" method="post" action="">
				<input type="hidden" name="option" value="login_widget_saml_role_mapping" />
				<table width="98%" border="0" style="background-color:#FFFFFF; border:1px solid #CCCCCC; padding:0px 0px 0px 10px;">
					<tr>
						<td colspan="2">
							<h3>Role Mapping</h3>
						</td>
					</tr>
					<tr><td colspan="2">Role will be assigned only to new users. Existing Wordpress users' role remains same.<br /><br/></td></tr>
					<tr>
						<td><strong>Default Role:</strong></td>
						<td>
							<?php 
								$disabled = '';
								if(!mo_saml_is_customer_registered_saml())
									$disabled = 'disabled';
								echo '<select name="saml_am_default_user_role" style="width:150px;" ' . $disabled .'>';
								$default_role = get_option('saml_am_default_user_role');
								if(empty($default_role))
									$default_role = get_option('default_role');
								echo wp_dropdown_roles( $default_role ) . '</select>';
							?>
						</td>
				  	</tr>
					<tr>
						<td>&nbsp;</td>
						<td><i>Select the default role to assign to new Users.</i></td>
				  	</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<?php
						$is_disabled = "";
						if(!mo_saml_is_customer_registered_saml()) {
							$is_disabled = "disabled";
						}
						$wp_roles = new WP_Roles();
						$roles = $wp_roles->get_names();
						$roles_configured = get_option('saml_am_role_mapping');
						foreach ($roles as $role_value => $role_name) {
							echo '<tr><td><b>' . $role_name .'</b></td><td><input type="text" name="saml_am_group_attr_values_' . $role_value . '" value="' . $roles_configured[$role_value] .'" placeholder="Comma separated Group/Role value for ' . $role_name . '" style="width: 400px;"' . $is_disabled . ' /></td></tr>';
						}
					?>
					<tr>
						<td>&nbsp;</td>
						<td><br /><input type="submit" style="width:100px;" name="submit" value="Save" class="button button-primary button-large" <?php if(!mo_saml_is_customer_registered_saml()) echo 'disabled'?>/> &nbsp; 
						<br /><br />
						</td>
					</tr>
				</table>
			</form>
	<?php
}

function mo_saml_save_plugin_config() {
	?>
		<form>
		<div id="instructions_idp"></div>
		<table width="98%" border="0" style="background-color:#FFFFFF; border:1px solid #CCCCCC; padding:0px 0px 0px 10px;">
			<tr>
				<?php if(!mo_saml_is_customer_registered_saml()) { ?>
					<td colspan="2"><div style="display:block;margin-top:10px;color:red;background-color:rgba(251, 232, 0, 0.15);padding:5px;border:solid 1px rgba(255, 0, 9, 0.36);">Please <a href="<?php echo add_query_arg( array('tab' => 'login'), $_SERVER['REQUEST_URI'] ); ?>">Register or Login with miniOrange</a> to configure the miniOrange SAML Plugin.</div></td>
				<?php } ?>
			</tr>
		<tr>
			<td colspan="2">
				<br/>
				<p style="font-size:13px;">miniOrange SAML SSO Plugin enables login to WordPress through your Identity Provider (e.g. OpenAM, Shibboleth, Okta). miniOrange is a Service Provider which acts like a broker between your WordPress site and Identity Provider.</p>
				<h3>Frequently Asked Questions</h3>
				<table class="mo_saml_help">
					<tr>
						<td class="mo_saml_help_cell">
							<div id="help_steps_title" class="mo_saml_title_panel">
								<div class="mo_saml_help_title">Instructions to use miniOrange SAML plugin</div>
							</div>
							<div hidden id="help_steps_desc" class="mo_saml_help_desc">
								<ul>
									<li>Step 1:&nbsp;&nbsp;&nbsp;&nbsp;Configure your Identity Provider by following <a href="<?php echo add_query_arg( array('tab' => 'config'), $_SERVER['REQUEST_URI'] ); ?>">these steps</a>.</li>
									<li>Step 2:&nbsp;&nbsp;&nbsp;&nbsp;Download X.509 certificate from your Identity Provider.</li>
									<li>Step 3:&nbsp;&nbsp;&nbsp;&nbsp;Enter appropriate values in the fields in <a href="<?php echo add_query_arg( array('tab' => 'save'), $_SERVER['REQUEST_URI'] ); ?>">Configure Service Provider</a>.</li>
									<li>Step 4:&nbsp;&nbsp;&nbsp;&nbsp;After saving your configuration, you will be able to test your configuration using the <b>Test &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Configuration</b> button on the top of the page.</li>
									<li>Step 5:&nbsp;&nbsp;&nbsp;&nbsp;Add "Login to &lt;IdP&gt;" widget to your WordPress page.</li>
								</ul>
								For any further queries, please contact us.								
							</div>
						</td>
					</tr>
					<tr>
						<td class="mo_saml_help_cell">
							<div id="help_widget_steps_title" class="mo_saml_title_panel">
								<div class="mo_saml_help_title">Add login Link to post/page/blog</div>
							</div>
							<div hidden id="help_widget_steps_desc" class="mo_saml_help_desc">
								<ol>
									<li>Go to Appearances > Widgets.</li>
									<li>Select "Login with &lt;Identity Provider&gt;". Drag and drop to your favourite location and save.</li>
								</ol>								
							</div>
						</td>
					</tr>
					<tr>
						<td class="mo_saml_help_cell">
							<div id="help_faq_idp_config_title" class="mo_saml_title_panel">
								<div class="mo_saml_help_title">I logged in to my Identity Provider and it redirected to WordPress, but I'm not logged in. There was an error - "We could not sign you in.".</div>
							</div>
							<div hidden id="help_faq_idp_config_desc" class="mo_saml_help_desc">
								To know what actually went wrong,
								<ol>
									<li>Login to you Wordpress administrator account. And go miniOrange SAML SSO plugin's Configure Service Provider tab.</li>
									<li>Click on <b>Test Configuration</b>. A popup window will open (make sure you popup enabled in your browser).</li>
									<li>Click on <b>Login</b> button. You will be redirected to your IdP for authentication.</li>
									<li>On successful authentication, You will be redirect back with the actual error message.</li>
									<li>Here are the some frequent errors:
									<ul><br />
										<li><b>INVALID_ISSUER</b>: This means that you have NOT entered the correct Issuer or Entity ID value provided by your Identity Provider. You'll see in the error message what was the expected value (that you have configured) and what actually found in the SAML Response.</li>
										<li><b>INVALID_AUDIENCE</b>: This means that you have NOT configured Audience URL in your Identity Provider correctly. It must be set to <b>https://auth.miniorange.com/moas/rest/saml/acs</b> in your Identity Provider.</li>
										<li><b>INVALID_DESTINATION</b>: This means that you have NOT configured Destination URL in your Identity Provider correctly. It must be set to <b>https://auth.miniorange.com/moas/rest/saml/acs</b> in your Identity Provider.</li>
										<li><b>INVALID_SIGNATURE</b>: This means that the certificate you provided did NOT match the certificate found in the SAML Response. Make sure you provide the same certificate that you downloaded from your IdP. If you have your IdP's Metadata XML file then make sure you provide certificate enclosed in X509Certificate tag which has an attribute <b>use="signing"</b>.</li>
										<li><b>INVALID_CERTIFICATE</b>: This means that the certificate you provided is NOT in proper format. Make sure you have copied the entire certificate provided by your IdP. If coiped from IdP's Metadata XML file, make sure that you copied the entire value.</li>
									</ul>
								</ol>
								If you need help resolving the issue, please contact us using the support form and we will get back to you shortly.
							</div>
						</td>
					</tr>
					<tr>
						<td class="mo_saml_help_cell">
							<div id="help_faq_idp_redirect_title" class="mo_saml_title_panel">
								<div class="mo_saml_help_title">I clicked on login link but I cannot see the login page of my Identity Provider.</div>
							</div>
							<div hidden id="help_faq_idp_redirect_desc" class="mo_saml_help_desc">
								This could mean that the <b>SAML Login URL</b> you have entered is not correct. Please enter the correct <b>SAML Login URL</b> (with HTTP-Redirect binding) provided by your Identity Provider. <br/><br/>If the problem persists, please contact us using the support form. It would be helpful if you could share your Identity Provider details with us.
							</div>
						</td>
					</tr>
					<tr>
						<td class="mo_saml_help_cell">
							<div id="help_faq_404_title" class="mo_saml_title_panel">
								<div class="mo_saml_help_title">I'm getting a 404 error page when I try to login.</div>
							</div>
							<div hidden id="help_faq_404_desc" class="mo_saml_help_desc">
								This could mean that you have not entered the correct <b>SAML Login URL</b>. Please enter the correct <b>SAML Login URL</b> (with HTTP-Redirect binding) provided by your Identity Provider and try again.<br/><br/>If the problem persists, please contact us using the support form. It would be helpful if you could share your Identity Provider details with us.
							</div>
						</td>
					</tr>
					
					<tr>
						<td class="mo_saml_help_cell">
							<div id="help_curl_enable_title" class="mo_saml_title_panel">
								<div class="mo_saml_help_title">How to enable PHP cURL extension?</div>
							</div>
							<div hidden id="help_curl_enable_desc" class="mo_saml_help_desc">
								<ol>
									<li>Open php.ini file located under php installation folder.</li>
									<li>Search for extension=php_curl.dll.</li>
									<li>Uncomment it by removing the semi-colon(;) in front of it.</li>
									<li>Restart the Apache Server.</li>
								</ol>
								For any further queries, please contact us.								
							</div>
						</td>
					</tr>
					
					<tr>
						<td class="mo_saml_help_cell">
							<div id="help_working_title" class="mo_saml_title_panel">
								<div class="mo_saml_help_title">How miniOrange SAML plugin works?</div>
							</div>
							<div hidden id="help_working_desc" class="mo_saml_help_desc">
								<img src="<?php echo plugin_dir_url(__FILE__) . 'images/saml_working.png'?>" alt="Working of miniOrange SAML plugin" style="width: 85%; padding-left: 10%; padding-top: 3%;padding-bottom:2%"/>
								When a user requests to login to WordPress using miniOrange SAML 2.0 SSO plugin, the request goes through a series of steps which logs in the user. The steps are given below -
								<ol>
									<li>miniOrange SAML SSO plugin sends a login request to miniOrange Broker Service.</li>
									<li>miniOrange Broker Service creates a SAML Request and redirects the user to your Identity Provider for authentication.</li>
									<li>Upon successful authentication, your Identity Provider sends a SAML Response back to miniOrange Broker Service.</li>
									<li>miniOrange Broker Service verifies the SAML Response and sends a response status (along with the logged in user's information) back to miniOrange SAML SSO plugin. Plugin then reads the response and login the user.</li>
								</ol>
							</div>
						</td>
					</tr>
					<tr>
						<td class="mo_saml_help_cell">
							<div id="help_saml_title" class="mo_saml_title_panel">
								<div class="mo_saml_help_title">What is SAML?</div>
							</div>
							<div hidden id="help_saml_desc" class="mo_saml_help_desc">
								Security Assertion Markup Language(SAML) is an XML-based, open-standard data format for exchanging authentication and authorization data between parties, in particular, between an Identity Provider and a Service Provider. In our case, miniOrange is the Service Provider and the application which manages credentials is the Identity provider.
								<br/><br/>
								The SAML specification defines three roles: the Principal (in this case, your Wordpress user), the Identity provider (IdP), and the Service Provider (SP). The Service Provider requests and obtains an identity assertion from the Identity Provider. On the basis of this assertion, the service provider can make an access control decision Ã¢â‚¬â€œ in other words it can decide whether to allow user to login to WordPress.
								<br/><br/>
								For more details please refer to this <a href="https://en.wikipedia.org/wiki/Security_Assertion_Markup_Language" target="_blank">SAML document</a>.
							</div>
						</td>
					</tr>
					<tr>
						<td class="mo_saml_help_cell">
							<div id="help_saml_flow_title" class="mo_saml_title_panel">
								<div class="mo_saml_help_title">SP-Initiated Login vs. IdP-Initiated Login</div>
							</div>
							<div hidden id="help_saml_flow_desc" class="mo_saml_help_desc">
								The user's identity(user profile and credentials) is managed by an Identity Provider(IdP) and the user wants to login to your WordPress site.
								<br/><br/>
								<b>SP-Initiated Login</b>
								<br/>
								<ol>
									<li>The request to login is initiated through the WordPress site.</li>
									<li>Using the miniOrange SAML Plugin, the user is redirected to IdP login page.</li>
									<li>The user authenticates with the IdP.</li>
									<li>With the help of response from IdP, miniOrange SAML Plugin logs in the user to WordPress site.</li>
								</ol>
								<b>IdP-Initiated Login</b>
								<br/>
								<ol>
									<li>The user initiates login through IdP.</li>
									<li>With the help of response from IdP, miniOrange SAML Plugin logs in the user to WordPress site.</li>
								</ol>
								<!--For more details refer to <a href="https://documentation.pingidentity.com/display/PF610/SP-Initiated+SSO--POST-POST" target="_blank">SP-initiated login</a> and <a href="https://documentation.pingidentity.com/display/PF610/IdP-Initiated+SSO--POST" target="_blank">IdP-initiated login</a>.-->
							</div>
						</td>
					</tr>
				</table>
				<br/>
				
				<br/><br/>
			</td>
		</tr>
		</table>
		</form>
		
	
</div>
<?php
}

function mo_saml_get_test_url(){
	
	$url = get_option('mo_saml_host_name') . '/idptest/?id=' . get_option('mo_saml_admin_customer_key') . '&key=' . get_option('mo_saml_customer_token');
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

function mo_saml_is_sp_configured() {
	$saml_login_url = get_option('saml_login_url');
	if( !empty($saml_login_url)) {
		return 1;
	} else {
		return 0;
	}
}

function miniorange_support_saml(){
?>
	<div class="mo_saml_support_layout">
		<div>
			<h3>Support</h3>
			<p>Need any help? We can help you with configuring your Identity Provider. Just send us a query and we will get back to you soon.</p>
			<form method="post" action="">
				<input type="hidden" name="option" value="mo_saml_contact_us_query_option" />
				<table class="mo_saml_settings_table">
					<tr>
						<td><input style="width:95%" type="email" class="mo_saml_table_textbox" required name="mo_saml_contact_us_email" value="<?php echo get_option("mo_saml_admin_email"); ?>" placeholder="Enter your email"></td>
					</tr>
					<tr>
						<td><input type="tel" style="width:95%" id="contact_us_phone" pattern="[\+]\d{11,14}|[\+]\d{1,4}[\s]\d{9,10}" class="mo_saml_table_textbox" name="mo_saml_contact_us_phone" value="<?php echo get_option('mo_saml_admin_phone');?>" placeholder="Enter your phone"></td>
					</tr>
					<tr>
						<td><textarea class="mo_saml_table_textbox" style="width:95%" onkeypress="mo_saml_valid_query(this)" onkeyup="mo_saml_valid_query(this)" onblur="mo_saml_valid_query(this)" required name="mo_saml_contact_us_query" rows="4" style="resize: vertical;" placeholder="Write your query here"></textarea></td>
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