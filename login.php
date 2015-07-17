<?php
/*
Plugin Name: miniOrange SSO using SAML 2.0
Plugin URI: http://miniorange.com/
Description: miniOrange SAML 2.0 SSO enables user to perform Single Sign On with any SAML 2.0 enabled Identity Provider. 
Version: 1.0
Author: miniOrange
Author URI: http://miniorange.com/
*/


include_once dirname( __FILE__ ) . '/mo_login_saml_sso_widget.php';
require('mo-saml-class-customer.php');
require('mo_saml_settings_page.php');

class saml_mo_login {
	
	function __construct() {
		add_action( 'admin_menu', array( $this, 'miniorange_sso_menu' ) );
		add_action( 'admin_init',  array( $this, 'miniorange_login_widget_saml_save_settings' ) );		
		add_action( 'admin_enqueue_scripts', array( $this, 'plugin_settings_style' ) );
		register_deactivation_hook(__FILE__, array( $this, 'mo_sso_saml_deactivate'));	
		add_action( 'admin_enqueue_scripts', array( $this, 'plugin_settings_script' ) );
		add_action( 'plugins_loaded',  array( $this, 'mo_login_widget_text_domain' ) );		
		remove_action( 'admin_notices', array( $this, 'mo_saml_success_message') );
		remove_action( 'admin_notices', array( $this, 'mo_saml_error_message') );
	}
	
	function  mo_login_widget_saml_options () {
		global $wpdb;
		update_option( 'mo_saml_host_name', 'https://auth.miniorange.com' );
		$host_name = get_option('mo_saml_host_name');
		
		$customerRegistered = mo_saml_is_customer_registered();
		if( $customerRegistered ) {
			mo_register_saml_sso();
		} else {
			mo_register_saml_sso();
		}	

	}
	
	function mo_saml_success_message() {
		$class = "error";
		$message = get_option('mo_saml_message');
		echo "<div class='" . $class . "'> <p>" . $message . "</p></div>";
	}

	function mo_saml_error_message() {
		$class = "updated";
		$message = get_option('mo_saml_message');
		echo "<div class='" . $class . "'> <p>" . $message . "</p></div>";
	}
		
	public function mo_sso_saml_deactivate() {
		//delete all stored key-value pairs
		delete_option('mo_saml_host_name');
		delete_option('mo_saml_new_registration');
		delete_option('mo_saml_admin_phone');
		delete_option('mo_saml_admin_password');
		delete_option('mo_saml_verify_customer');
		delete_option('mo_saml_admin_customer_key');
		delete_option('mo_saml_admin_api_key');
		delete_option('mo_saml_customer_token');
		delete_option('mo_saml_message');
		delete_option('mo_saml_registration_status');
	}
	
	function mo_login_widget_text_domain(){
		load_plugin_textdomain('flw', FALSE, basename( dirname( __FILE__ ) ) .'/languages');
	}
	private function mo_saml_show_success_message() {
		remove_action( 'admin_notices', array( $this, 'mo_saml_success_message') );
		add_action( 'admin_notices', array( $this, 'mo_saml_error_message') );
	}
	function mo_saml_show_error_message() {
		remove_action( 'admin_notices', array( $this, 'mo_saml_error_message') );
		add_action( 'admin_notices', array( $this, 'mo_saml_success_message') );
	}
	function plugin_settings_style() {
		wp_enqueue_style( 'mo_saml_admin_settings_style', plugins_url( 'includes/css/style_settings.css', __FILE__ ) );
		wp_enqueue_style( 'mo_saml_admin_settings_phone_style', plugins_url( 'includes/css/phone.css', __FILE__ ) );
	}
	function plugin_settings_script() {
		wp_enqueue_script( 'mo_saml_admin_settings_script', plugins_url( 'includes/js/settings.js', __FILE__ ) );
		wp_enqueue_script( 'mo_saml_admin_settings_phone_script', plugins_url('includes/js/phone.js', __FILE__ ) );
	}
	function miniorange_login_widget_saml_save_settings(){
		//Save saml configuration
			if(isset($_POST['option']) and $_POST['option'] == "login_widget_saml_save_settings"){
				
			//validation and sanitization
			$saml_identity_name = '';
			$saml_login_url = '';
			$saml_logout_url = '';
			$saml_issuer = '';
			$saml_x509_certificate = '';
			if( $this->mo_saml_check_empty_or_null( $_POST['saml_identity_name'] ) || $this->mo_saml_check_empty_or_null( $_POST['saml_login_url'] ) || $this->mo_saml_check_empty_or_null( $_POST['saml_issuer'] )  ) {
				update_option( 'mo_saml_message', 'All the fields are required. Please enter valid entries.');
				$this->mo_saml_show_error_message();
				return;
			}else{
				$saml_identity_name = sanitize_text_field( $_POST['saml_identity_name'] );
				$saml_login_url = sanitize_text_field( $_POST['saml_login_url'] );
				$saml_logout_url = sanitize_text_field( $_POST['saml_logout_url'] );
				$saml_issuer = sanitize_text_field( $_POST['saml_issuer'] );
				$saml_x509_certificate = sanitize_text_field( $_POST['saml_x509_certificate'] );
			}
			
			update_option('saml_identity_name', $saml_identity_name);
			update_option('saml_login_url', $saml_login_url);
			update_option('saml_logout_url', $saml_logout_url);
			update_option('saml_issuer', $saml_issuer);
			update_option('saml_x509_certificate', $_POST['saml_x509_certificate']);	
			if(isset($_POST['saml_response_signed']))
				{
				update_option('saml_response_signed' , 'checked');
				}
			else
				{
				update_option('saml_response_signed' , '');
				}
			if(isset($_POST['saml_assertion_signed']))
				{
				update_option('saml_assertion_signed' , 'checked');
				}
			else
				{
				update_option('saml_assertion_signed' , '');
				}
			
			$saveSaml = new Customersaml();
			$outputSaml = json_decode( $saveSaml->save_external_idp_config(), true );
			update_option('saml_idp_config_id', $outputSaml['id']);
			update_option('mo_saml_message', 'Identity Provider details saved successfully');
			$this->mo_saml_show_success_message();
			
			//Call to saveConfiguration.
			
			
			/*update_option( 'entity_id', $_POST['entity_id'] );
			update_option( 'sso_url', $_POST['sso_url'] );
			update_option( 'cert_fp', $_POST['cert_fp']);
			
			*/
		}
		
		//Save Wordpress SSO to another site settings
		if(isset($_POST['option']) and $_POST['option'] == "login_widget_cross_domain_save_settings"){
			
			//Validation and sanitization
			$cd_destination_site_url = '';
			$cd_shared_key = '';
			if( $this->mo_saml_check_empty_or_null( $_POST['cd_destination_site_url'] )  ) {
				update_option( 'mo_saml_message', 'All the fields are required. Please enter valid entries.');
				$this->mo_saml_show_error_message();
				return;
			}else{
				$cd_destination_site_url = sanitize_text_field( $_POST['cd_destination_site_url'] );
				$cd_shared_key = sanitize_text_field( $_POST['cd_shared_key'] );
			}
			update_option( 'cd_destination_site_url', $cd_destination_site_url );
			update_option( 'cd_shared_key', $cd_shared_key );
		}
				
		if( isset( $_POST['option'] ) and $_POST['option'] == "mo_saml_register_customer" ) {	//register the admin to miniOrange
			
			//validation and sanitization
			$email = '';
			$phone = '';
			$password = '';
			$confirmPassword = '';
			if( $this->mo_saml_check_empty_or_null( $_POST['email'] ) || $this->mo_saml_check_empty_or_null( $_POST['phone'] ) || $this->mo_saml_check_empty_or_null( $_POST['password'] ) || $this->mo_saml_check_empty_or_null( $_POST['confirmPassword'] ) ) {
				update_option( 'mo_saml_message', 'All the fields are required. Please enter valid entries.');
				$this->mo_saml_show_error_message();
				return;
			} else if( strlen( $_POST['password'] ) < 8 || strlen( $_POST['confirmPassword'] ) < 8){
				update_option( 'mo_saml_message', 'Choose a password with minimum length 8.');
				$this->mo_saml_show_error_message();
				return;
			} else{
				$email = sanitize_email( $_POST['email'] );
				$phone = sanitize_text_field( $_POST['phone'] );
				$password = sanitize_text_field( $_POST['password'] );
				$confirmPassword = sanitize_text_field( $_POST['confirmPassword'] );
			}
			
			update_option( 'mo_saml_admin_email', $email );
			update_option( 'mo_saml_admin_phone', $phone );
			if( strcmp( $password, $confirmPassword) == 0 ) {
				update_option( 'mo_saml_admin_password', $password );
				
				$customer = new CustomerSaml();
				$content = json_decode($customer->check_customer(), true);
				if( strcasecmp( $content['status'], 'CUSTOMER_NOT_FOUND') == 0 ){
					$content = json_decode($customer->send_otp_token(), true);
										if(strcasecmp($content['status'], 'SUCCESS') == 0) {
											update_option( 'mo_saml_message', ' A one time passcode is sent to ' . get_option('mo_saml_admin_email') . '. Please enter the otp here to verify your email.');
											update_option('mo_saml_transactionId',$content['txId']);
											update_option('mo_saml_registration_status','MO_OTP_DELIVERED_SUCCESS');

											$this->mo_saml_show_success_message();
										}else{
											update_option('mo_saml_message','There was an error in sending email. Please click on Resend OTP to try again.');
											update_option('mo_saml_registration_status','MO_OTP_DELIVERED_FAILURE');
											$this->mo_saml_show_error_message();
										}
				}else{
						$this->get_current_customer();
				}
				
			} else {
				update_option( 'mo_saml_message', 'Passwords do not match.');
				delete_option('mo_saml_verify_customer');
				$this->mo_saml_show_error_message();
			}
	
		}
		if(isset($_POST['option']) and $_POST['option'] == "mo_saml_validate_otp"){

			//validation and sanitization
			$otp_token = '';
			if( $this->mo_saml_check_empty_or_null( $_POST['otp_token'] ) ) {
				update_option( 'mo_saml_message', 'Please enter a value in otp field.');
				update_option('mo_saml_registration_status','MO_OTP_VALIDATION_FAILURE');
				$this->mo_saml_show_error_message();
				return;
			} else{
				$otp_token = sanitize_text_field( $_POST['otp_token'] );
			}

			$customer = new CustomerSaml();
			$content = json_decode($customer->validate_otp_token(get_option('mo_saml_transactionId'), $otp_token ),true);
			if(strcasecmp($content['status'], 'SUCCESS') == 0) {

					$this->create_customer();
			}else{
				update_option( 'mo_saml_message','Invalid one time passcode. Please enter a valid otp.');
				update_option('mo_saml_registration_status','MO_OTP_VALIDATION_FAILURE');
				$this->mo_saml_show_error_message();
			}
		}
		if( isset( $_POST['option'] ) and $_POST['option'] == "mo_saml_verify_customer" ) {	//register the admin to miniOrange
			
			//validation and sanitization
			$email = '';
			$password = '';
			if( $this->mo_saml_check_empty_or_null( $_POST['email'] ) || $this->mo_saml_check_empty_or_null( $_POST['password'] ) ) {
				update_option( 'mo_saml_message', 'All the fields are required. Please enter valid entries.');
				$this->mo_saml_show_error_message();
				return;
			} else{
				$email = sanitize_email( $_POST['email'] );
				$password = sanitize_text_field( $_POST['password'] );
			}
		
			update_option( 'mo_saml_admin_email', $email );
			update_option( 'mo_saml_admin_password', $password );
			$customer = new Customersaml();
			$content = $customer->get_customer_key();
			$customerKey = json_decode( $content, true );
			if( json_last_error() == JSON_ERROR_NONE ) {
				update_option( 'mo_saml_admin_customer_key', $customerKey['id'] );
				update_option( 'mo_saml_admin_api_key', $customerKey['apiKey'] );
				update_option( 'mo_saml_customer_token', $customerKey['token'] );
				update_option( 'mo_saml_admin_phone', $customerKey['phone'] );
				update_option('mo_saml_admin_password', '');
				update_option( 'mo_saml_message', 'Customer retrieved successfully');
				update_option('mo_saml_registration_status' , 'Existing User');
				delete_option('mo_saml_verify_customer');
				$this->mo_saml_show_success_message(); 
			} else {
				update_option( 'mo_saml_message', 'Invalid username or password. Please try again.');
				$this->mo_saml_show_error_message();		
			}
			update_option('mo_saml_admin_password', '');
		}else if( isset( $_POST['option'] ) and $_POST['option'] == "mo_saml_contact_us_query_option" ) {
			// Contact Us query
			$email = $_POST['mo_saml_contact_us_email'];
			$phone = $_POST['mo_saml_contact_us_phone'];
			$query = $_POST['mo_saml_contact_us_query'];
			$customer = new CustomerSaml();
			if ( $this->mo_saml_check_empty_or_null( $email ) || $this->mo_saml_check_empty_or_null( $query ) ) {
				update_option('mo_saml_message', 'Please fill up Email and Query fields to submit your query.');
				$this->mo_saml_show_error_message();
			} else {
				$submited = $customer->submit_contact_us( $email, $phone, $query );
				if ( $submited == false ) {
					update_option('mo_saml_message', 'Your query could not be submitted. Please try again.');
					$this->mo_saml_show_error_message();
				} else {
					update_option('mo_saml_message', 'Thanks for getting in touch! We shall get back to you shortly.');
					$this->mo_saml_show_success_message();
				}
			}
		}
		else if( isset( $_POST['option'] ) and $_POST['option'] == "mo_saml_resend_otp" ) {

					    $customer = new CustomerSaml();
						$content = json_decode($customer->send_otp_token(), true);
									if(strcasecmp($content['status'], 'SUCCESS') == 0) {
											update_option( 'mo_saml_message', ' A one time passcode is sent to ' . get_option('mo_saml_admin_email') . ' again. Please check if you got the otp and enter it here.');
											update_option('mo_saml_transactionId',$content['txId']);
											update_option('mo_saml_registration_status','MO_OTP_DELIVERED_SUCCESS');
											$this->mo_saml_show_success_message();
									}else{
											update_option('mo_saml_message','There was an error in sending email. Please click on Resend OTP to try again.');
											update_option('mo_saml_registration_status','MO_OTP_DELIVERED_FAILURE');
											$this->mo_saml_show_error_message();
									}

		}

		
	}
	
	function create_customer(){
			$customer = new CustomerSaml();
			$customerKey = json_decode( $customer->create_customer(), true );
			if( strcasecmp( $customerKey['status'], 'CUSTOMER_USERNAME_ALREADY_EXISTS') == 0 ) {
						$this->get_current_customer();
			} else if( strcasecmp( $customerKey['status'], 'SUCCESS' ) == 0 ) {
											update_option( 'mo_saml_admin_customer_key', $customerKey['id'] );
											update_option( 'mo_saml_admin_api_key', $customerKey['apiKey'] );
											update_option( 'mo_saml_customer_token', $customerKey['token'] );
											update_option('mo_saml_admin_password', '');
											update_option( 'mo_saml_message', 'Registration complete!');
											update_option('mo_saml_registration_status','MO_OPENID_REGISTRATION_COMPLETE');
											delete_option('mo_saml_verify_customer');
											delete_option('mo_saml_new_registration');
											$this->mo_saml_show_success_message();
			}
			update_option('mo_saml_admin_password', '');
	}

	function get_current_customer(){
			$customer = new CustomerSaml();
			$content = $customer->get_customer_key();
			$customerKey = json_decode( $content, true );
						if( json_last_error() == JSON_ERROR_NONE ) {
								
								update_option( 'mo_saml_admin_customer_key', $customerKey['id'] );
								update_option( 'mo_saml_admin_api_key', $customerKey['apiKey'] );
								update_option( 'mo_saml_customer_token', $customerKey['token'] );
								update_option('mo_saml_admin_password', '' );
								update_option( 'mo_saml_message', 'Your account has been retrieved successfully.' );
								delete_option('mo_saml_verify_customer');
								delete_option('mo_saml_new_registration');
								$this->mo_saml_show_success_message();

					} else {
								update_option( 'mo_saml_message', 'You already have an account with miniOrange. Please enter a valid password.');
								update_option('mo_saml_verify_customer', 'true');
								delete_option('mo_saml_new_registration');
								$this->mo_saml_show_error_message();

					}

	}

	public function mo_saml_check_empty_or_null( $value ) {
	if( ! isset( $value ) || empty( $value ) ) {
		return true;
	}
	return false;
	}
	
	function miniorange_sso_menu() {
		
		//Add miniOrange SAML SSO
		$page = add_menu_page( 'MO SAML Settings ' . __( 'Configure SAML Identity Provider for SSO', 'mo_saml_settings' ), 'miniOrange SAML 2.0 SSO', 'administrator', 'mo_saml_settings', array( $this, 'mo_login_widget_saml_options' ) );

		//Cross domain setup
		$page = add_submenu_page( 'mo_saml_settings', 'MO Login ' . __('Wordpress SSO to another site'), __('Wordpress SSO to another site'), 'administrator', 'mo_cross_domain_saml', 'mo_cross_domain_saml_config' );
		
		global $submenu;
		if ( is_array( $submenu ) AND isset( $submenu['mo_saml_settings'] ) )
		{
			$submenu['mo_saml_settings'][0][0] = __( 'Configure SAML Identity Provider for SSO', 'mo_saml_login' );
		}
	}
	
	
	
}
new saml_mo_login;