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

class AESEncryption {
	/**
	* @param string $data - the key=value pairs separated with & 
	* @return string
	*/
	public static function encrypt_data($data, $key) {
		$strIn = AESEncryption::pkcs5_pad($data);	
		$strCrypt = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $strIn, MCRYPT_MODE_ECB);
		return base64_encode($strCrypt);
	}


	/**
	* @param string $data - crypt response from Sagepay
	* @return string
	*/
	public static function decrypt_data($data, $key) {
		$strIn = base64_decode($data);
		return AESEncryption::pkcs5_unpad(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $strIn, MCRYPT_MODE_ECB));
	}

	private static function pkcs5_pad($text) {
		$size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
		$pad = $size - (strlen($text) % $size);
		return $text . str_repeat(chr($pad), $pad);
	}

	private static function pkcs5_unpad($text) {
		$pad = ord($text{strlen($text) - 1});
		if ($pad > strlen($text)) return false;
		if (strspn($text, $text{strlen($text) - 1}, strlen($text) - $pad) != $pad) {
			return false;
		}
		return substr($text, 0, -1 * $pad);
	}
}
?>