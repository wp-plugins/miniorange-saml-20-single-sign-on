<?php
class mo_login_wid extends WP_Widget {
	private $entityId, $ssoUrl, $certFp, $identityName;
	public function __construct() {
	
		$this->entityId = get_option('entity_id');
		$this->ssoUrl = get_option('sso_url');
		$this->certFp = get_option('cert_fp');
		if(!is_null(get_option('saml_identity_name')))
			$this->identityName = get_option('saml_identity_name');
		else
			$this->identityName = 'Configured Identity Provider';
		
		parent::__construct(
	 		'Saml_Login_Widget',
			'Login with ' . $this->identityName,
			array( 'description' => __( 'This is a miniOrange SAML login widget.', 'mosaml' ), )
		);
	 }

	
	public function widget( $args, $instance ) {
		extract( $args );
		
		$wid_title = apply_filters( 'widget_title', $instance['wid_title'] );
		
		echo $args['before_widget'];
		if ( ! empty( $wid_title ) )
			echo $args['before_title'] . $wid_title . $args['after_title'];
			$this->loginForm();
		echo $args['after_widget'];
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['wid_title'] = strip_tags( $new_instance['wid_title'] );
		return $instance;
	}


	public function form( $instance ) {
		$wid_title = '';
		if(array_key_exists('wid_title', $instance))
			$wid_title = $instance[ 'wid_title' ];
		?>
		<p><label for="<?php echo $this->get_field_id('wid_title'); ?>"><?php _e('Title:'); ?> </label>
		<input class="widefat" id="<?php echo $this->get_field_id('wid_title'); ?>" name="<?php echo $this->get_field_name('wid_title'); ?>" type="text" value="<?php echo $wid_title; ?>" />
		</p>
		<?php 
	}
	
	public function loginForm(){
		global $post;
		$this->error_message();
		$this->mo_saml_load_login_script();
		
		if(!is_user_logged_in()){
		?>
		<form name="login" id="login" method="post" action="">
		<input type="hidden" name="option" value="saml_user_login" />

		<font size="+1" style="vertical-align:top;"> </font><?php
		$identity_provider = get_option('saml_identity_name');
		if(!empty($identity_provider))
			echo '<a href="' . get_option('mo_saml_host_name') . '/moas/rest/saml/request?id=' . get_option('mo_saml_admin_customer_key') . '&returnurl= ' . urlencode( site_url() . "/?option=readsamllogin" ) . '">Login with ' . $identity_provider . '</a>';
		else
			echo "Please configure the miniOrange SAML Plugin first.";
		
		if( ! $this->mo_saml_check_empty_or_null_val(get_option('mo_saml_redirect_error_code')))
		{

			echo '<div></div><div title="Login Error"><font color="red">We could not sign you in. Please contact your Administrator.</font></div>';
				
				delete_option('mo_saml_redirect_error_code');
				delete_option('mo_saml_redirect_error_reason'); 
		}
		
		?>
		
			<a href="http://miniorange.com/wordpress-ldap-login" style="display:none">Login to WordPress using LDAP</a>
		<a href="http://miniorange.com/cloud-identity-broker-service" style="display:none">Cloud Identity broker service</a>
		
			</ul>
		</form>
		<?php 
		} else {
		global $current_user;
     	get_currentuserinfo();
		$link_with_username = __('Hello, ','mosaml').$current_user->display_name;
		?>
		<?php echo $link_with_username;?> | <a href="<?php echo wp_logout_url(site_url()); ?>" title="<?php _e('Logout','mosaml');?>"><?php _e('Logout','mosaml');?></a></li>
		<?php 
		}
	}
	
	public function mo_saml_check_empty_or_null_val( $value ) {
	if( ! isset( $value ) || empty( $value ) ) {
		return true;
	}
	return false;
	}
	
	private function LoadScript(){
	?>
	<script type="text/javascript">
window.moAsyncInit = function() {
	MO.init({
	appId      : "<?php echo $this->appId?>", // replace your app id here
	status     : true, 
	cookie     : true, 
	xmoml      : true  
	});
};
(function(d){
	var js, id = 'miniorange-jssdk', ref = d.getElementsByTagName('script')[0];
	if (d.getElementById(id)) {return;}
	js = d.createElement('script'); js.id = id; js.async = true;
	js.src = "//connect.miniorange.net/en_US/all.js";
	ref.parentNode.insertBefore(js, ref);
}(document));

</script>
	<?php
	}
	
	public function error_message(){
		if(isset($_SESSION['msg']) and $_SESSION['msg']){
			echo '<div class="'.$_SESSION['msg_class'].'">'.$_SESSION['msg'].'</div>';
			unset($_SESSION['msg']);
			unset($_SESSION['msg_class']);
		}
	}
	

	
	private function mo_saml_load_login_script() {
	?>
	<script type="text/javascript">
		function moSAMLLogin(app_name) {
			window.location.href = "<?php echo get_option('mo_saml_host_name');?>/moas/rest/saml/request?id=" + "<?php echo get_option('mo_saml_admin_customer_key')?>" + "&returnurl=" + "<?php echo urlencode( site_url() . '/?option=readsamllogin' ); ?>";
		
		}
	</script>
	<?php
	}
	
	public function register_plugin_styles() {
		wp_enqueue_style( 'style_login_widget', plugins_url( 'miniorange-login-saml-service-provider/includes/css/style_login_widget.css' ) );
	}
	
} 

function plugin_settings_script_widget() {
	wp_enqueue_script('jquery');
	wp_enqueue_script( 'mo_saml_admin_settings_script_widget', plugins_url( 'includes/js/settings.js', __FILE__ ) );
	wp_enqueue_script( 'mo_saml_admin_popup_script_widget', plugins_url( 'includes/js/jquery-impromptu.min.js', __FILE__ ) );
}

function plugin_settings_style_widget() {
	wp_enqueue_style( 'mo_saml_popup_style', plugins_url( 'includes/css/jquery-impromptu.min.css', __FILE__ ) );
}

function mo_login_validate(){
	if(isset($_POST['option']) and $_POST['option'] == "saml_user_login"){
		global $post;
		if($_POST['user_username'] != "" and $_POST['user_password'] != ""){
			$creds = array();
			$creds['user_login'] = $_POST['user_username'];
			$creds['user_password'] = $_POST['user_password'];
			$creds['remember'] = true;
		
			$user = wp_signon( $creds, true );
			if($user->ID == ""){
				$_SESSION['msg_class'] = 'error_wid_login';
				$_SESSION['msg'] = __('Error in login!','flw');
			} else{
				wp_set_auth_cookie($user->ID);
				wp_redirect( site_url() );
				exit;
			}
		} else {
			$_SESSION['msg_class'] = 'error_wid_login';
			$_SESSION['msg'] = __('Username or password is empty!','flw');
		}
		
	}
	
		
		if( isset( $_REQUEST['option'] ) and strpos( $_REQUEST['option'], 'generate_samlreq' ) !== false ) {
			
			require_once dirname(__FILE__) . '/includes/lib/AuthnRequest.php';

			$auth_request = new MiniOrangeAuthnRequest();
			$auth_request->initiateLogin();

			exit();
		}
		
		if( isset( $_REQUEST['option'] ) and strpos( $_REQUEST['option'], 'validate_saml' ) !== false ) {
			require_once dirname(__FILE__) . '/includes/lib/Acs.php';
			// Get the email of the user.
			$miniorange_acs = new MiniOrangeAcs();
			try {
			$user_email = $miniorange_acs->processSamlResponse();
			}
			catch (Exception $e) {
				echo sprintf("An error occurred while processing the SAML Response.");
				exit;
			}
			if( email_exists( $user_email ) ) { // user is a member 
				$user 	= get_user_by('email', $user_email );
				$user_id 	= $user->ID;
				wp_set_auth_cookie( $user_id, true );
				wp_redirect( site_url() );
				exit;
			} else { // this user is a guest
				$random_password = wp_generate_password( 10, false );
				$user_id = wp_create_user( $user_email, $random_password, $user_email );
				wp_set_auth_cookie( $user_id, true );
				wp_redirect( site_url() );
				exit;
			}
		}
		
		if( isset( $_REQUEST['option'] ) and strpos( $_REQUEST['option'], 'readsamllogin' ) !== false ) {
			
			// Get the email of the user.
			require_once dirname(__FILE__) . '/includes/lib/encryption.php';
			
			if(isset($_POST['STATUS']) && $_POST['STATUS'] == 'ERROR')
			{
				update_option('mo_saml_redirect_error_code', $_POST['ERROR_REASON']);
				update_option('mo_saml_redirect_error_reason' , $_POST['ERROR_MESSAGE']);
			}
			else if(isset($_POST['STATUS']) && $_POST['STATUS'] == 'SUCCESS'){
					
				delete_option('mo_saml_redirect_error_code');
				delete_option('mo_saml_redirect_error_reason');
				
				try {
					
					//Get enrypted user_email
					$emailAttribute = get_option('saml_am_email');
					$usernameAttribute = get_option('saml_am_username');
					$firstName = get_option('saml_am_first_name');
					$lastName = get_option('saml_am_last_name');
					$groupName = get_option('saml_am_group_name');
					$defaultRole = get_option('saml_am_default_user_role');
					$checkIfMatchBy = get_option('saml_am_account_matcher');
					$user_email = '';
					$userName = '';
					//Attribute mapping. Check if Match/Create user is by username/email:

					if(!empty($firstName) && array_key_exists($firstName, $_POST) )
						$firstName = $_POST[$firstName];
				
					if(!empty($lastName) && array_key_exists($lastName, $_POST) )
						$lastName = $_POST[$lastName];
					
					if(!empty($usernameAttribute) && array_key_exists($usernameAttribute, $_POST))
						$userName = $_POST[$usernameAttribute];
					
					if(!empty($groupName) && array_key_exists($groupName, $_POST) )
						$groupName = $_POST[$groupName];
					
					
					//Check whether the match is by email or username
					if($checkIfMatchBy == 'email')
					{
						if(!empty($emailAttribute) && array_key_exists($emailAttribute, $_POST) )
						{
						$user_email = $_POST[$emailAttribute];
						}
						else
						{
						$user_email = $_POST['NameID'];
						}
					}
					else
					{
						if(!empty($usernameAttribute))
						{
						$user_email = $_POST[$usernameAttribute];
						}
						else
						{
						$user_email = $_POST['NameID'];
						}
						
					}
		
					//Decrypt email now.
					
					//Get customer token as a key to decrypt email
					$key = get_option('mo_saml_customer_token');
				
					if(isset($key) || trim($key) != '')
					{
					$deciphertext = AESEncryption::decrypt_data($user_email, $key);							 
					$user_email = $deciphertext;				
					}
					
					//Decrypt firstname and lastName and username
					
					if(!empty($firstName) && !empty($key))
					{
						$decipherFirstName = AESEncryption::decrypt_data($firstName, $key);	
						$firstName = $decipherFirstName;
					}
					if(!empty($lastName) && !empty($key))
					{
						$decipherLastName = AESEncryption::decrypt_data($lastName, $key);	
						$lastName = $decipherLastName;
					}
					if(!empty($userName) && !empty($key))
					{
						$decipherUserName = AESEncryption::decrypt_data($userName, $key);	
						$userName = $decipherUserName;
					}
					if(!empty($groupName) && !empty($key))
					{
						$decipherGroupName = AESEncryption::decrypt_data($groupName, $key);	
						$groupName = $decipherGroupName;
					}
				}
				catch (Exception $e) {
					echo sprintf("An error occurred while processing the SAML Response.");
					exit;
				}
				
				
				if( email_exists( $user_email ) || username_exists($user_email) ) { // user is a member 
				
					if($checkIfMatchBy == 'username')
					{		
					$user 	= get_user_by('login', $user_email);
					}
					else
					{
					$user 	= get_user_by('email', $user_email );
					}
				
					$user_id 	= $user->ID;
					
			
					if(!empty($firstName))
					{
					$user_id = wp_update_user( array( 'ID' => $user_id, 'first_name' => $firstName ) );
					}
					if(!empty($lastName))
					{
					$user_id = wp_update_user( array( 'ID' => $user_id, 'last_name' => $lastName ) );					
					}
					if(!empty($userName))
					{
					$user_id = wp_update_user( array( 'ID' => $user_id, 'user_login' => $userName ) );					
					}
					
					
					
					wp_set_auth_cookie( $user_id, true );
					wp_redirect( site_url() );
					exit;
				} else { // this user is a guest
					$random_password = wp_generate_password( 10, false );
					if(!empty($userName))
					{
						$user_id = wp_create_user( $userName, $random_password, $user_email );
					}
					else
					{
						$user_id = wp_create_user( $user_email, $random_password, $user_email );
					}
					
					// Assign role
					$role_mapping = get_option('saml_am_role_mapping');
					if(!empty($groupName) && !empty($role_mapping)) {
						$role_to_assign = '';
						$found = false;
						foreach ($role_mapping as $role_value => $group_names) {
							$groups = explode(",", $group_names);
							foreach ($groups as $group) {
								if(trim($group) == trim($groupName)) {
									$found = true;
									$role_to_assign = $role_value;
									$user_id = wp_update_user( array( 'ID' => $user_id, 'role' => $role_to_assign ) );
									break 2;
								}
							}
						}
						if($found !== true && !empty($defaultRole)) {
							$user_id = wp_update_user( array( 'ID' => $user_id, 'role' => $defaultRole ) );
						} 
					} 
					elseif(!empty($defaultRole)) {
						$user_id = wp_update_user( array( 'ID' => $user_id, 'role' => $defaultRole ) );
					}
					
					if(!empty($firstName))
					{
					$user_id = wp_update_user( array( 'ID' => $user_id, 'first_name' => $firstName ) );
					}
					if(!empty($lastName))
					{
					$user_id = wp_update_user( array( 'ID' => $user_id, 'last_name' => $lastName ) );					
					}
					if(!empty($userName))
					{
					$user_id = wp_update_user( array( 'ID' => $user_id, 'user_login' => $userName ) );					
					}
					wp_set_auth_cookie( $user_id, true );
					wp_redirect( site_url() );
					exit;
				}
			}

		}

		
		if( isset( $_REQUEST['option'] ) and strpos( $_REQUEST['option'], 'saml_redirect' ) !== false ) {
			 global $current_user;
			 get_currentuserinfo();
			 require_once dirname(__FILE__) . '/includes/lib/Utilities.php';
			
			
					$relayState = '';
					if(array_key_exists('redirect_uri', $_GET)) {
						$relayState = $_GET['redirect_uri'];
					} else {
						$relayState = '';
					}
					
					//Get data
					$destinationUrl = get_option('cd_destination_site_url');
					$key = get_option('cd_shared_key');
					$emailId = $current_user->user_email;
					
					//Encrypt Email if key exists
					if($key != null && $key != '')
					{
					$data = $emailId;
					$blocksize = 16;
					$ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key,
												 $data, MCRYPT_MODE_ECB);
					$emailId = base64_encode($ciphertext);
					}
					
					
					//Generate SAML Response XML
					
						$testFileName = dirname(__FILE__) . '/test.xml';
						//Read File
						$finalSamlTest =  htmlspecialchars(file_get_contents($testFileName), ENT_QUOTES);
				
				
						$xmlTest=new SimpleXMLElement(html_entity_decode($finalSamlTest));
						//echo "XYZNAME" . $xml->getName();
						$xmlTest['Destination'] = $destinationUrl . '/?option=validate_saml';
						$xmlTest->Assertion->Subject->NameID = $emailId;
						$xmlTest->Assertion->Conditions->AudienceRestriction->Audience = $destinationUrl . '/';
						$xmlTest->asXML($testFileName);
						
						
						//Sign XML
						$privKey = dirname(__FILE__) . '/resources/RSAPrivateKey.pem';
						$doc = new DOMDocument();
						$doc->load($testFileName);
						$objDSig = new XMLSecurityDSig();
						$objDSig->setCanonicalMethod(XMLSecurityDSig::EXC_C14N);
						$encHash1 = 'abc';
						$objDSig->addReference($doc, XMLSecurityDSig::SHA1, array('http://www.w3.org/2000/09/xmldsig#enveloped-signature'), array('force_uri' => true,'uri_context'=>$encHash1));
						$objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, array('type'=>'private'));
						/* load private key */
						$objKey->loadKey( $privKey, TRUE);
						$objDSig->sign($objKey);
						/* Add associated public key */
						$objDSig->add509Cert(file_get_contents(dirname(__FILE__) . '/resources/RSACert.cer'));
						$objDSig->insertSignature($doc->documentElement, $doc->documentElement->firstChild);
		
						$samlresponsexml = $doc->saveXML();
					
						//$finalSaml =  htmlspecialchars(file_get_contents("test.xml.out"), ENT_QUOTES);
						$samlResponse = base64_encode($samlresponsexml);
						$samlArr = array("SAMLResponse" => $samlResponse, "RelayState" => $relayState);
			
						//Send Redirect
						$redirect = $destinationUrl . '/?option=validate_saml';
	
						redirect_post_saml($redirect, $samlArr);
						
						
		}
		
	}
	function pkcs5_unpad($text) 
	{ 	
		$pad = ord($text{strlen($text) - 1});
		if ($pad > strlen($text)) return false;
		if (strspn($text, $text{strlen($text) - 1}, strlen($text) - $pad) != $pad) {
			return false;
		}
		return substr($text, 0, -1 * $pad);
	}
	function mo_saml_is_customer_registered() {
		$email 			= get_option('mo_saml_admin_email');
		$phone 			= get_option('mo_saml_admin_phone');
		$customerKey 	= get_option('mo_saml_admin_customer_key');
		if( ! $email || ! $phone || ! $customerKey || ! is_numeric( trim( $customerKey ) ) ) {
			return 0;
		} else {
			return 1;
		}
	}
function redirect_post_saml($url, array $data)
{
    ?>
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <script type="text/javascript">
            function closethisasap() {
                document.forms["redirectpost"].submit();
            }
        </script>
    </head>
    <body onload="closethisasap();">
    <form name="redirectpost" method="post" action="<?php echo $url; ?>">
        <?php
        if ( !is_null($data) ) {
            foreach ($data as $k => $v) {
                echo '<input type="hidden" name="' . $k . '" value="' . $v . '"> ';
            }
        }
        ?>
    </form>
    </body>
    </html>
    <?php
    exit;
}


add_action( 'widgets_init', create_function( '', 'register_widget( "mo_login_wid" );' ) );
add_action( 'wp_enqueue_scripts', 'plugin_settings_style_widget' );
add_action( 'wp_enqueue_scripts', 'plugin_settings_script_widget' );
add_action( 'init', 'mo_login_validate' );
?>