<?php
/**
 * This file is part of miniOrange SAML plugin.
 *
 * miniOrange SAML plugin is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * miniOrange SAML plugin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with miniOrange SAML plugin.  If not, see <http://www.gnu.org/licenses/>.
 */

include "xmlseclibs.php";
class Utilities {
	
	public static function generateID() {
		return '_' . self::stringToHex(self::generateRandomBytes(21));
	}
	
	public static function stringToHex($bytes) {
		$ret = '';
		for($i = 0; $i < strlen($bytes); $i++) {
			$ret .= sprintf('%02x', ord($bytes[$i]));
		}
		return $ret;
	}
	
	public static function generateRandomBytes($length, $fallback = TRUE) {
		assert('is_int($length)');
        return openssl_random_pseudo_bytes($length);
	}
	
	public static function createAuthnRequest($acsUrl, $issuer) {
		$requestXmlStr = '<?xml version="1.0" encoding="UTF-8"?>' .
						'<samlp:AuthnRequest xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol" ID="' . self::generateID() . 
						'" Version="2.0" IssueInstant="' . self::generateTimestamp() . 
						'" ProtocolBinding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect" AssertionConsumerServiceURL="' . $acsUrl . 
						'" ><saml:Issuer xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion">' . $issuer . 
						'</saml:Issuer></samlp:AuthnRequest>';
		$deflatedStr = gzdeflate($requestXmlStr);
		$base64EncodedStr = base64_encode($deflatedStr);
		$urlEncoded = urlencode($base64EncodedStr);
		return $urlEncoded;
	}
	
	public static function generateTimestamp($instant = NULL) {
		if($instant === NULL) {
			$instant = time();
		}
		return gmdate('Y-m-d\TH:i:s\Z', $instant);
	}
	
	public static function xpQuery(DOMNode $node, $query)
    {
        assert('is_string($query)');
        static $xpCache = NULL;

        if ($node instanceof DOMDocument) {
            $doc = $node;
        } else {
            $doc = $node->ownerDocument;
        }

        if ($xpCache === NULL || !$xpCache->document->isSameNode($doc)) {
            $xpCache = new DOMXPath($doc);
            //$xpCache->registerNamespace('soap-env', SAML2_Const::NS_SOAP);
            $xpCache->registerNamespace('saml_protocol', 'urn:oasis:names:tc:SAML:2.0:protocol');
            $xpCache->registerNamespace('saml_assertion', 'urn:oasis:names:tc:SAML:2.0:assertion');
            $xpCache->registerNamespace('saml_metadata', 'urn:oasis:names:tc:SAML:2.0:metadata');
            $xpCache->registerNamespace('ds', 'http://www.w3.org/2000/09/xmldsig#');
            $xpCache->registerNamespace('xenc', 'http://www.w3.org/2001/04/xmlenc#');
        }

        $results = $xpCache->query($query, $node);
        $ret = array();
        for ($i = 0; $i < $results->length; $i++) {
            $ret[$i] = $results->item($i);
        }

		return $ret;
    }
	
	public static function parseNameId(DOMElement $xml)
    {
        $ret = array('Value' => trim($xml->textContent));

        foreach (array('NameQualifier', 'SPNameQualifier', 'Format') as $attr) {
            if ($xml->hasAttribute($attr)) {
                $ret[$attr] = $xml->getAttribute($attr);
            }
        }

        return $ret;
    }
	
	public static function xsDateTimeToTimestamp($time)
    {
        $matches = array();

        // We use a very strict regex to parse the timestamp.
        $regex = '/^(\\d\\d\\d\\d)-(\\d\\d)-(\\d\\d)T(\\d\\d):(\\d\\d):(\\d\\d)(?:\\.\\d+)?Z$/D';
        if (preg_match($regex, $time, $matches) == 0) {
            throw new Exception(
                'Invalid SAML2 timestamp passed to xsDateTimeToTimestamp: ' . $time
            );
        }

        // Extract the different components of the time from the  matches in the regex.
        // intval will ignore leading zeroes in the string.
        $year   = intval($matches[1]);
        $month  = intval($matches[2]);
        $day    = intval($matches[3]);
        $hour   = intval($matches[4]);
        $minute = intval($matches[5]);
        $second = intval($matches[6]);

        // We use gmmktime because the timestamp will always be given
        //in UTC.
        $ts = gmmktime($hour, $minute, $second, $month, $day, $year);

        return $ts;
    }
	
	public static function extractStrings(DOMElement $parent, $namespaceURI, $localName)
    {
        assert('is_string($namespaceURI)');
        assert('is_string($localName)');

        $ret = array();
        for ($node = $parent->firstChild; $node !== NULL; $node = $node->nextSibling) {
            if ($node->namespaceURI !== $namespaceURI || $node->localName !== $localName) {
                continue;
            }
            $ret[] = trim($node->textContent);
        }

        return $ret;
    }
	
	public static function validateElement(DOMElement $root)
    {
        /* Create an XML security object. */
        $objXMLSecDSig = new XMLSecurityDSig();

        /* Both SAML messages and SAML assertions use the 'ID' attribute. */
        $objXMLSecDSig->idKeys[] = 'ID';

        /* Locate the XMLDSig Signature element to be used. */
        $signatureElement = self::xpQuery($root, './ds:Signature');
        if (count($signatureElement) === 0) {
            /* We don't have a signature element to validate. */
            return FALSE;
        } elseif (count($signatureElement) > 1) {
            throw new Exception('XMLSec: more than one signature element in root.');
        }
        $signatureElement = $signatureElement[0];
        $objXMLSecDSig->sigNode = $signatureElement;

        /* Canonicalize the XMLDSig SignedInfo element in the message. */
        $objXMLSecDSig->canonicalizeSignedInfo();

        /* Validate referenced xml nodes. */
        if (!$objXMLSecDSig->validateReference()) {
            throw new Exception('XMLsec: digest validation failed');
        }

        /* Check that $root is one of the signed nodes. */
        $rootSigned = FALSE;
        /** @var DOMNode $signedNode */
        foreach ($objXMLSecDSig->getValidatedNodes() as $signedNode) {
            if ($signedNode->isSameNode($root)) {
                $rootSigned = TRUE;
                break;
            } elseif ($root->parentNode instanceof DOMDocument && $signedNode->isSameNode($root->ownerDocument)) {
                /* $root is the root element of a signed document. */
                $rootSigned = TRUE;
                break;
            }
        }
        if (!$rootSigned) {
            throw new Exception('XMLSec: The root element is not signed.');
        }

        /* Now we extract all available X509 certificates in the signature element. */
        $certificates = array();
        foreach (self::xpQuery($signatureElement, './ds:KeyInfo/ds:X509Data/ds:X509Certificate') as $certNode) {
            $certData = trim($certNode->textContent);
            $certData = str_replace(array("\r", "\n", "\t", ' '), '', $certData);
            $certificates[] = $certData;
        }

        $ret = array(
            'Signature' => $objXMLSecDSig,
            'Certificates' => $certificates,
            );
        return $ret;
    }
	
	/*public static function validateSignature($signatureData, $certDataToValidateAgainst) {
		$certData = str_replace(array("-----BEGIN CERTIFICATE-----", "-----END CERTIFICATE-----", "\r", "\n", " "), '', $certDataToValidateAgainst);
		
		$signature = $signatureData['Certificates'][0];
		$signature = str_replace(array("-----BEGIN CERTIFICATE-----", "-----END CERTIFICATE-----", "\r", "\n", " "), '', $signature);
		
		if(strcmp($signature, $certData) === 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}*/
	
	public static function validateSignature(array $info, XMLSecurityKey $key)
    {
        assert('array_key_exists("Signature", $info)');

        /** @var XMLSecurityDSig $objXMLSecDSig */
        $objXMLSecDSig = $info['Signature'];

        $sigMethod = self::xpQuery($objXMLSecDSig->sigNode, './ds:SignedInfo/ds:SignatureMethod');
        if (empty($sigMethod)) {
            throw new Exception('Missing SignatureMethod element.');
        }
        $sigMethod = $sigMethod[0];
        if (!$sigMethod->hasAttribute('Algorithm')) {
            throw new Exception('Missing Algorithm-attribute on SignatureMethod element.');
        }
        $algo = $sigMethod->getAttribute('Algorithm');

        if ($key->type === XMLSecurityKey::RSA_SHA1 && $algo !== $key->type) {
            $key = self::castKey($key, $algo);
        }
		
        /* Check the signature. */
        if (! $objXMLSecDSig->verify($key)) {
            throw new Exception("Unable to validate Signature");
        }
    }
	
	public static function validateResponse($currentURL, $certFingerprint, $signatureData,
		MiniOrangeResponse $response) {
		assert('is_string($currentURL)');
		assert('is_string($certFingerprint)');
		
		
		/* Validate Response-element destination. */
		$msgDestination = $response->getDestination();
		if ($msgDestination !== NULL && $msgDestination !== $currentURL) {
			throw new Exception('Destination in response doesn\'t match the current URL. Destination is "' .
				$msgDestination . '", current URL is "' . $currentURL . '".');
		}
		
		$responseSigned = self::checkSign($certFingerprint, $signatureData);
		/* Returning boolean $responseSigned */
		return $responseSigned;
	}
	
	public static function checkSign($certFingerprint, $signatureData) {
		

		$certificates = $signatureData['Certificates'];
		if (count($certificates) === 0) {
			return FALSE;
		} 

		$fpArray = array();
		$fpArray[] = $certFingerprint;
		$pemCert = self::findCertificate($fpArray, $certificates);
		
		$lastException = NULL;
		
		$key = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, array('type'=>'public'));
		$key->loadKey($pemCert);
				
		try {
			/*
			 * Make sure that we have a valid signature
			 */
			assert('$key->type === XMLSecurityKey::RSA_SHA1');
			self::validateSignature($signatureData, $key);
			return TRUE;
		} catch (Exception $e) {
			$lastException = $e;
		}
		
		
		/* We were unable to validate the signature with any of our keys. */
		if ($lastException !== NULL) {
			throw $lastException;
		} else {
			return FALSE;
		}
	
	}
	
	public static function validateIssuerAndAudience($samlResponse, $acsUrl, $issuerToValidateAgainst) {
		//$issuer = $samlResponse->getAssertions()[0]->getIssuer();
		//$audience = $samlResponse->getAssertions()[0]->getValidAudiences()[0];
		
		$issuer = current($samlResponse->getAssertions())->getIssuer();
		$audience = current(current($samlResponse->getAssertions())->getValidAudiences());
		
		if(strcmp($issuerToValidateAgainst, $issuer) === 0) {
		
			if(strcmp($audience, $acsUrl) === 0) {
				return TRUE;
			} else {
				throw new Exception('Invalid audience.');
			}
		} else {
			throw new Exception('Issuer cannot be verified.');
		}
	}
	
	private static function findCertificate(array $certFingerprints, array $certificates) {

		$candidates = array();
		
		foreach ($certificates as $cert) {
			$fp = strtolower(sha1(base64_decode($cert)));
			if (!in_array($fp, $certFingerprints, TRUE)) {
				$candidates[] = $fp;
				continue;
			}

			/* We have found a matching fingerprint. */
			$pem = "-----BEGIN CERTIFICATE-----\n" .
				chunk_split($cert, 64) .
				"-----END CERTIFICATE-----\n";
			return $pem;
		}

		throw new Exception('Unable to find a certificate matching the configured fingerprint.');
	}
	
}
?>