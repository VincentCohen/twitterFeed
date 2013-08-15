<?php

/**
 * Retrieves an array of twitter posts 
 *
 * @author Vincent Cohen 2013
 **/
class SimpleTwitterFeed{

	// holds the token
	private $strToken = "";

	// array containing consumer keys
	private $arrConsumerKeys = array(
		'key' => '',
		'secret' => ''
	);

	// array containing access tokens
	private $arrAcccessTokens = array(
		'token' => '',
		'secret' => ''
	);

	/**
	* class constructor
	* @param array holding the consmer key and secret
	*/
	public function __construct($arrConsumerKeys){
				
		// set consumer keys
		if(isset($arrConsumerKeys) && count($arrConsumerKeys) > 0){
			if(!isset($arrConsumerKeys["key"]))
				return "consumer key not set";

			if(!isset($arrConsumerKeys["secret"]))
				return "consumer secret not set";

			$this->arrConsumerKeys["key"] = $arrConsumerKeys["key"];
			$this->arrConsumerKeys["secret"] = $arrConsumerKeys["secret"];
		}

	}

	/**
	* Get the token from twitter api
	* @return string authentication token needed for passing requests
	*/
	public function getToken(){
		// connnect using curl
		$objCurlHandle = curl_init();

		curl_setopt($objCurlHandle, CURLOPT_URL, 'https://api.twitter.com/oauth2/token');
		curl_setopt($objCurlHandle, CURLOPT_POST, true);

		// set the needed data for the token request
		$arrData = array();
		$arrData['grant_type'] = "client_credentials";

		curl_setopt($objCurlHandle, CURLOPT_POSTFIELDS, $arrData);

		// set app keys
		curl_setopt($objCurlHandle,CURLOPT_USERPWD, $this->arrConsumerKeys['key'] . ':' . $this->arrConsumerKeys['secret']);
		curl_setopt($objCurlHandle,CURLOPT_RETURNTRANSFER, true);

		$strResult = curl_exec($objCurlHandle);

		curl_close($objCurlHandle);

		$arrToken = json_decode($strResult);

		return $arrToken->access_token;
	}

	/**
	*	Get the twitter feed from specified user
	*/
	public function getFeed($iAmount, $strUsername){
		
		$objCurlHandle = curl_init();

		curl_setopt($objCurlHandle, CURLOPT_URL, 'https://api.twitter.com/1.1/statuses/user_timeline.json?count='.$iAmount.'&screen_name='.$strUsername);

		curl_setopt($objCurlHandle, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $this->getToken()));

		curl_setopt($objCurlHandle, CURLOPT_RETURNTRANSFER, true);

		$strResult = curl_exec($objCurlHandle);

		curl_close($objCurlHandle);

		return json_decode($strResult);

	}
}

// example usage
$arrConsumerKeys = array('key'=>'<your consumer key>', 'secret'=>'<your consumer secret>');
$feed = new SimpleTwitterFeed($arrConsumerKeys);

echo '<pre>'; 
// get the feed and set the parameter for amount and username
var_dump($feed->getFeed(9000, '<your twitter username>'));