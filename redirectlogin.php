<?php
		try {
				$user_email = $_POST["NameID"];
				
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
?>