<?php
function mo_register_saml_sso() {
	if( isset( $_GET[ 'tab' ] ) ) {
		$active_tab = $_GET[ 'tab' ];
	} else {
		$active_tab = 'config';
	}
	?>
<!--div id="tab">
	<h2 class="nav-tab-wrapper">
		<a class="nav-tab nav-tab-active"
			href="admin.php?page=mo_saml_settings">Step 1: Configure Identity Provider</a> <a
			class="nav-tab" href="admin.php?page=mo_cross_domain_saml">SSO to another Wordpress site
			</a>
	</h2>
</div-->
<div id="mo_saml_settings">

	<div class="miniorange_container">
	<table style="width:100%;">
			
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
				mo_saml_apps_page ($active_tab);
			}
	
		}
		else{
			
			echo "CURL NOT ENABLED. You need to enable curl in your PHP to use this plugin.";
		}
	?>
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
	<tr>
		<td colspan="2"><h2>miniOrange SAML SSO</h2></td>
	</tr>
	<tr>
			<!--Register with miniOrange-->
	<td style="vertical-align:top;width:65%;">
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
								<td></td>
								<td>We will call only if you need support.</td>
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
	</td>
	<td style="vertical-align:top;padding-left:1%;">
		<?php echo miniorange_support_saml(); ?>	
	</td>
	</tr>
		<?php
}
function mo_saml_show_verify_password_page_saml() {
	?>
	<tr>
		<td colspan="2"><h2>miniOrange SAML SSO</h2></td>
	</tr>
	<tr>
			<!--Verify password with miniOrange-->
	<td style="vertical-align:top;width:65%;">
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
	</td>
	<td style="vertical-align:top;padding-left:1%;">
		<?php echo miniorange_support_saml(); ?>	
	</td>
	</tr>
		<?php
}

/*function mo_cross_domain_saml_config() {
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
}*/

function mo_saml_apps_page($active_tab) {
	?>
	<tr>
	<h2 class="nav-tab-wrapper">
		<a class="nav-tab <?php echo $active_tab == 'config' ? 'nav-tab-active' : ''; ?>" href="<?php echo add_query_arg( array('tab' => 'config'), $_SERVER['REQUEST_URI'] ); ?>">Step 1: Configure Identity Provider</a>
		<a class="nav-tab <?php echo $active_tab == 'save' ? 'nav-tab-active' : ''; ?>" href="<?php echo add_query_arg( array('tab' => 'save'), $_SERVER['REQUEST_URI'] ); ?>">Step 2: Configure SAML Plugin</a>
		<a class="nav-tab <?php echo $active_tab == 'opt' ? 'nav-tab-active' : ''; ?>" href="<?php echo add_query_arg( array('tab' => 'opt'), $_SERVER['REQUEST_URI'] ); ?>">Step 3: Configure Attribute Mapping</a>
	</h2>
	<td style="vertical-align:top;width:65%;">
	<?php
	if($active_tab == 'save') {
		mo_saml_apps_config_saml();
	} else if($active_tab == 'opt') {
		mo_saml_save_optional_config();
	} else {
		mo_saml_save_plugin_config();
	}
	?>
	</td>
	<td style="vertical-align:top;padding-left:1%;">
		<?php echo miniorange_support_saml(); ?>	
	</td>
	</tr>
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
			<td colspan="2"><h3>Configure SAML Plugin</h3></td>
		  </tr>
		 <tr>
			<td colspan="2"><h4>After completing your configuration, download metadeta.xml from your Identity Provider and enter the data below.</h4></td>
		  </tr>
		  <tr>
			<td style="width:200px;"><strong>Identity Provider Name *:</strong></td>
			<td><input type="text" name="saml_identity_name" style="width: 95%;" value="<?php echo $saml_identity_name;?>" required/></td>
		  </tr>
		  <tr>
			<td>&nbsp;</td>
			<td><i>Enter the name of the Identity Provider. Example: Okta, Ping, OpenAM, Shibboleth</i><br/></td>
		  </tr>
		  <tr><td>&nbsp;</td></tr>
		  <tr>
			<td><strong>Saml Login URL *:</strong></td>
			<td><input type="url" name="saml_login_url" style="width: 95%;" value="<?php echo $saml_login_url;?>" required/></td>
		  </tr>
		   <tr>
			<td>&nbsp;</td>
			<td><i>Enter the Single Sign On service HTTP Redirect URL mentioned in the IDP Metadata.xml file or in the IDP configuration.</i></td>
		  </tr>
		  <tr><td>&nbsp;</td></tr>
		  <tr>
			<td><strong>Saml Logout URL:</strong></td>
			<td><input type="url" name="saml_logout_url" style="width: 95%;" value="<?php echo $saml_logout_url;?>" /></td>
		  </tr>
		  <tr>
			<td>&nbsp;</td>
			<td><i>Optionally enter the Identity provider SLO(Single Logout URL) endpoint. This is the URL where you want the user to be redirected to upon logout. By default, logout redirects to your WordPress homepage.</i></td>
		  </tr>
		  <tr><td>&nbsp;</td></tr>
		  <tr>
		  <td><strong>IDP Entity ID *:</strong></td>
			<td><input type="text" name="saml_issuer" style="width: 95%;" value="<?php echo $saml_issuer;?>" required/></td>
		  </tr>
		  <tr>
			<td>&nbsp;</td>
			<td><i>Enter the Identity Provider Entity ID mentioned in the IDP metadata.xml file or in the IDP configuration.</i></td>
		  </tr>
		  <tr><td>&nbsp;</td></tr>
		   <tr>
		  <td><strong>Copy and Paste SAML X-509 Certificate text:</strong></td>
			<td><textarea rows="4" cols="5" name="saml_x509_certificate" style="width: 95%;"><?php echo $saml_x509_certificate;?></textarea></td>
		  </tr>
		  <tr>
			<td>&nbsp;</td>
			<td><i>Copy and Paste the X509 Certificate from the Metadata.xml file mentioned in the certificate tags or in the IDP configuration. 
			Format of the certificate should be like: <br/>-----BEGIN CERTIFICATE-----<br/>XXXXXXXXXXXXXXXXXXXXXXXXXXX<br/>-----END CERTIFICATE-----</i></td>
		  </tr>
		  <tr><td>&nbsp;</td></tr>
		  <tr>
		  <td><br><strong>Response Signed:</strong></td>
			<td><input type="checkbox" name="saml_response_signed" value="Yes" <?php echo $saml_response_signed; ?> /></td>
		  </tr>
		  <tr>
			<td>&nbsp;</td>
			<td><i>Check this if the Identity Provider is signing the Response. Leave this unchecked by default, if no setting is provided by the IDP.</i></td>
		  </tr>
		  <tr><td>&nbsp;</td></tr>
		  <tr>
		  <td><br><strong>Assertion Signed:</strong></td>
			<td><input type="checkbox" name="saml_assertion_signed" value="Yes" <?php echo $saml_assertion_signed; ?> /></td>
		  </tr>
		  <tr>
			<td>&nbsp;</td>
			<td><i>Check this if the Identity Provider is signing the assertion. Leave this unchecked by default, if no setting is provided by the IDP.</i></td>
		  </tr>
		  
		  
		  <tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="submit" value="Save" class="button button-primary button-large" /> &nbsp; 
			<?php if($saml_identity_name != null) { ?>
			<input type="button" name="test" onclick="showTestWindow();" value="Test the saved configuration" class="button button-primary button-large" />
			<?php } ?>
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
	
	//Attribute mapping
	$saml_am_username = get_option('saml_am_username');	
	if($saml_am_username == NULL) {$saml_am_username = 'NameID'; }
	$saml_am_email = get_option('saml_am_email');
	if($saml_am_email == NULL) {$saml_am_email = 'NameID'; }
	$saml_am_first_name = get_option('saml_am_first_name');
	$saml_am_last_name = get_option('saml_am_last_name');
	$saml_am_role = get_option('saml_am_role');
	?>
		<form name="saml_form_am" method="post" action="">
		<input type="hidden" name="option" value="login_widget_saml_attribute_mapping" />
		<table width="98%" border="0" style="background-color:#FFFFFF; border:1px solid #CCCCCC; padding:0px 0px 0px 10px; margin:2px;">
		  <tr>
			<td colspan="2"><a id="toggle_am_content"><h3>Attribute Mapping (optional)</h3></a></td>
		  </tr>
		
			  <tr>
			  
			  <td colspan="2"><p>Sometimes the names of the attributes sent by the IdP not match the names used by Wordpress for the user accounts. In this section we can set the mapping between IdP fields and Wordpress fields. Notice that this mapping could be also set at IdP.</p></td>
			  </tr>
			  <tr>
			  <td style="width:200px;"><strong>Match/Create Wordpress account by: </strong></td>
			  <td><select name="saml_am_account_matcher" id="saml_am_account_matcher">
				  <option value="email"<?php if(get_option('saml_am_account_matcher') == 'email') echo 'selected="selected"' ; ?> >Email</option>
				  <option value="username"<?php if(get_option('saml_am_account_matcher') == 'username') echo 'selected="selected"' ; ?> >Username</option>
				</select>
			  </td>
			  </tr>
			  <tr>
				<td><strong>Username *:</strong></td>
				<td><input type="text" name="saml_am_username" placeholder="Enter name of the parameter from IDP for username" style="width: 350px;" value="<?php echo $saml_am_username;?>" required /></td>
			  </tr>
			  <tr>
				<td><strong>Email *:</strong></td>
				<td><input type="text" name="saml_am_email" placeholder="Enter name of the parameter from IDP for Email" style="width: 350px;" value="<?php echo $saml_am_email;?>" required /></td>
			  </tr>
			  <tr>
				<td><strong>First Name:</strong></td>
				<td><input type="text" name="saml_am_first_name" placeholder="Enter name of the parameter from IDP for First Name" style="width: 350px;" value="<?php echo $saml_am_first_name;?>" /></td>
			  </tr>
			  <tr>
				<td><strong>Last Name:</strong></td>
				<td><input type="text" name="saml_am_last_name" placeholder="Enter name of the parameter from IDP for Last Name" style="width: 350px;" value="<?php echo $saml_am_last_name;?>" /></td>
			  </tr>
			  <tr>
				<td valign="top"><strong>Role:</strong></td>
				<td>
				
				<select name="saml_am_role" id="saml_am_role">
				  <option value="administrator"<?php if(get_option('saml_am_role') == 'administrator') echo 'selected="selected"' ; ?> >Administrator</option>
				  <option value="editor"<?php if(get_option('saml_am_role') == 'editor') echo 'selected="selected"' ; ?> >Editor</option>
				  <option value="contributor"<?php if(get_option('saml_am_role') == 'contributor') echo 'selected="selected"' ; ?> >Contributor</option>
				  <option value="subscriber"<?php if(get_option('saml_am_role') == 'subscriber') echo 'selected="selected"' ; ?> >Subscriber</option>
				</select>
				<br>
				<i>The attribute that contains the role of the user, For example 'Administrator'. If Wordpress can't figure what role assign to the user, it will assign the default role defined at the general settings.</i></td>
			  </tr>
				  
			  <tr>
				<td>&nbsp;</td>
				<td><input type="submit" name="submit" value="Save" class="button button-primary button-large" /> &nbsp; 
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
		<table width="98%" border="0" style="background-color:#FFFFFF; border:1px solid #CCCCCC; padding:0px 0px 0px 10px; margin:2px;">
		  <!--tr>
			<td width="45%"><h3>Configure Identity Provider </h3></td>
			<td width="55%">&nbsp;</td>
		  </tr-->
		<tr>
			<td colspan="2"><?php mo_login_help();?></td>
		</tr>
		</table>
		</form>
		
	
</div>
<?php
}

function mo_saml_get_test_url(){
	
	$url = get_option('mo_saml_host_name') . '/idptest/?id=' . get_option('mo_saml_admin_customer_key') . '&key=' . get_option('mo_saml_customer_token');
	//$url = get_option('mo_saml_host_name') . '/moas/rest/saml/request?id=' . get_option('mo_saml_admin_customer_key') . '&returnurl=' . urlencode( site_url() . "/?option=readsamllogin" );
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
		<br/>
		<p><!--font color="#FF0000"><strong>Note*</strong></font-->
			miniOrange SAML SSO Plugin acts as a SAML 2.0 Service Provider which can be configured to establish trust between your wordpress site (where the plugin is installed) and your Identity Provider instance.
		</p>
		<!--h2>Help :</h2-->
	
		<p>
		
		<div id="miniorange_provider"></div>
			<h3>Pre-requisite</h3>
			<ol>
				<li>You must have login credentials of an adminisrator for your Identity Provider.</li>
				<li>You must have a good understanding of what an IdP and SP is and basic understanding of how SAML works.</li>
			</ol>

			<b>If you need help understanding any of this, please submit a query using the widget on the right hand side.</b>
			
			<br/>
			<h3>The way it works</h3>
			<table border="0" style="background-color:#FFFFFF; border:1px solid #CCCCCC; padding:0px 0px 0px 10px; margin:2px;">
				<tr>
					<td><b>Wordpress site</b></td>
					<td>----------<b>A</b>----------</td>
					<td><b>miniOrange SAML Plugin</b></td> 
					<td>------------<b>B</b>-----------</td>
					<td><b>Your IdP</b></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(Automatic)</td>
					<td>&nbsp;</td>
					<td>(User configured)</td>
					<td>&nbsp;</td>
				</tr>
			</table>
			<p>For getting (A) to work properly, you dont need to do anything. As soon as you install the plugin, this link is established. <br/>For getting (B) setup properly, you need to make the following configurations in your IdP (by logging in as an admin)</p>
			<p>All you are doing here is saying that your miniOrange account on the cloud, trusts your IdP.</p>
			
			<h3>Configuring your Identity provider</h3>
			<p>The requests for login may initiate on your wordpress site or from your Identity provider dashboard.</p>
			
			<h4>If it initiates from the wordpress site, then do the following configuration - </h4>

			<ol>
				<li>ACS Url : https://auth.miniorange.com/moas/rest/saml/acs</li>
				<li>SP-EntityID/ISSUER : https://auth.miniorange.com/moas</li>
				<li>Subject Type	: Username/Email Address</li>
			</ol>
			
			<h4>If it initiates from your Identity provider dashboard then do the following configuration - </h4>
			Copy and paste the following URL against default Relay State URL in the IdP configuration:
			<br/><br/>
			<code><?php echo site_url(); ?>?option=readsamllogin&mId=<?php echo get_option('mo_saml_admin_customer_key') ?></code>			
			
			<h3>Get data for Add Identity Provider</h3>
			<p>After configuration of Identity Provider, download <b>metadata.xml</b>. You will need this to <u>fill the form in the next step</u>.<p>
			
			<h3>Add login Link to post/page/blog</h2>
			<ol>
				<li>Go to Appearances > Widgets</li>
				<li>Select Login with you Identity Provider. Drag and drop to your favourite location and save.</li>
			</ol>
			<!--h2>Configuring the plugin.</h2>
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
			
			</ol-->
		
		
			<!--h3>Configure the Identity Provider with the following settings: </h3>
			 <p>Now you need to configure the following settings in your Identity Provider for authenticating user back to the Wordpress site from your Identity Provider.</p>

			 <ol>
				<li><b>ACS Url :</b>  https://auth.miniorange.com/moas/rest/saml/acs </li>
				<li><b>SP-EntityID/ISSUER  :</b> https://auth.miniorange.com/moas</li>
				<li><b>Subject Type</b>	 : Username/Email Address</li>
			 </ol>
			 <p>If you want an <b>IdP Initiated Login</b>, then copy and paste the following URL against default Relay State URL in the IdP configuration:</p>
			<i><?php echo site_url(); ?>?option=readsamllogin&mId=<?php echo get_option('mo_saml_admin_customer_key') ?></i>
			<h2>Add login Link to post/page/blog</h2>
			<ol>
				<li>Go to Appearances > Widgets</li>
				<li>Select Login with you Identity Provider. Drag and drop to your favourite location and save.</li>
			</ol>
	

		</p-->	  
	  
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
	<tr>
		<td colspan="2"><h2>miniOrange SAML SSO</h2></td>
	</tr>
	<tr>
		<!-- Enter otp -->
	<td style="vertical-align:top;width:65%;">
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
		<form name="f" method="post">
		<td style="width:18%">
						<input type="hidden" name="option" value="mo_saml_go_back"/>
						<input type="submit" name="submit"  value="Back" class="button button-primary button-large" /></td>
		</form>
		<form name="f" id="resend_otp_form" method="post" action="">
							<td>

							<input type="hidden" name="option" value="mo_saml_resend_otp"/>
							</td>
							</tr>
		</form>
						</table>
						</div>
	</td>
	<td style="vertical-align:top;padding-left:1%;">
		<?php echo miniorange_support_saml(); ?>	
	</td>
	</tr>
	
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
			<p>Contact us and we can help you with configuring your Identity Provider making the Plugin Integration with the IdP and SSO up and running.</p>
			<form method="post" action="">
				<input type="hidden" name="option" value="mo_saml_contact_us_query_option" />
				<table class="mo_settings_table">
					<tr>
						<td><b><font color="#FF0000">*</font>Email:</b></td>
						<td><input style="width:100%" type="email" class="mo_table_textbox" required name="mo_saml_contact_us_email" value="<?php echo get_option("mo_saml_admin_email"); ?>"></td>
					</tr>
					<tr>
						<td><b>Phone:</b></td>
						<td><input type="tel" style="width:100%" id="contact_us_phone" pattern="[\+]\d{11,14}|[\+]\d{1,4}[\s]\d{9,10}" class="mo_table_textbox" name="mo_saml_contact_us_phone" value="<?php echo get_option('mo_saml_admin_phone');?>"></td>
					</tr>
					<tr>
						<td><b><font color="#FF0000">*</font>Query:</b></td>
						<td><textarea class="mo_table_textbox" style="width:100%" onkeypress="mo_saml_valid_query(this)" onkeyup="mo_saml_valid_query(this)" onblur="mo_saml_valid_query(this)" required name="mo_saml_contact_us_query" rows="4" style="resize: vertical;"></textarea></td>
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