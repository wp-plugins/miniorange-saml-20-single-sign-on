<?php
/**
 * @package    miniOrange
 * @author	   miniOrange Security Software Pvt. Ltd.
 * @license    GNU/GPLv3
 * @copyright  Copyright 2015 miniOrange. All Rights Reserved.
 *
 *
 * This file is part of miniOrange SAML plugin.
 */

include 'Response.php';

class MiniOrangeAcs {

  /**
   * The function processSamlResponse.
   */
  public function processSamlResponse() {
    if (array_key_exists('SAMLResponse', $_POST)) {
      $saml_response = $_POST['SAMLResponse'];
    }
    else {
      throw new Exception('Missing SAMLRequest or SAMLResponse parameter.');
    }

    $saml_response = base64_decode($saml_response);
    $document = new DOMDocument();
    $document->loadXML($saml_response);
    $saml_response_xml = $document->firstChild;

    $saml_response = new MiniOrangeResponse($saml_response_xml);
	$acs_url = site_url() . '/?option=validate_saml';

	$issuer = 'https://auth.miniorange.com/moas';
    $cert_fingerprint = '‎e7 ca 2d 76 54 0b 55 40 cb da 8b 90 b4 08 64 79 25 07 2b 23';

    /* remove whitespaces */
		/* convert to UTF-8 character encoding*/
	$certfpFromPlugin = iconv("UTF-8", "CP1252//IGNORE", $cert_fingerprint);
	
    $cert_fingerprint = preg_replace('/\s+/', '', $certfpFromPlugin);
    $signature_data = Utilities::validateElement($saml_response_xml);
    if ($signature_data !== FALSE) {
      $valid_signature = Utilities::validateResponse($acs_url, $cert_fingerprint, $signature_data, $saml_response);
      if ($valid_signature === FALSE) {
        throw new Exception('Invalid signature.');
      }
    }
    // Verify the issuer and audience from saml response.
	/*Get Acs_Url string before the '?' character*/
	$acs_url = substr($acs_url, 0, strpos($acs_url, "?"));
	
    Utilities::validateIssuerAndAudience($saml_response, $acs_url, $issuer);
    $username = $saml_response->getAssertions()[0]->getNameId()['Value'];

	//Decrypt if encrypted
	$key = get_option('cd_shared_key');
	$lastchar =  substr($username, -1);
	if($lastchar == '=')
	{
			//Decrypt
		
			if(isset($key) || trim($key) != '')
			{
			$data = $username;
			$blocksize = 16;
			$deciphertext = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key,
										 base64_decode($data), MCRYPT_MODE_ECB);
			$username = trim($deciphertext);
			}
		
	}
	
    return $username;
  }

}

?>