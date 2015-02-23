<?php
/**
 * Created by PhpStorm.
 * User: prism
 * Date: 9/13/14
 * Time: 8:12 PM
 */
session_start();

error_reporting(-1);
ini_set('display_errors', 'On');

require 'vendor/autoload.php';
require 'mongo.php';

use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookRequestException;
use Facebook\FacebookSession;
use GuzzleHttp\Client;
use GuzzleHttp\EntityBody;
use GuzzleHttp\Message\Request;
use GuzzleHttp\Message\Response;
$login = file_get_contents("login.json");
$log_json = json_decode($login, true);

FacebookSession::setDefaultApplication($log_json['app_id'], $log_json['app_secret']);

$helper = new FacebookRedirectLoginHelper('http://localhost:8000/redirect.php');

//$session = new FacebookSession($_GET['code']);
try {
	$session = $helper->getSessionFromRedirect();
} catch (FacebookRequestException $ex) {
// When Facebook returns an error
} catch (\Exception $ex) {
// When validation fails or other local issues
}
if ($session) {
// Logged in
	echo "logged in";
	echo "<br/>";
	$request = new FacebookRequest(
		$session,
		'GET',
		'/me/inbox?limit=100'
		//'/me/threads'
	);
	$response = $request->execute();
	$graphObject = $response->getGraphObject();

	//echo "Name: " . $user_profile->getName();
	//print_r($graphObject);
	// var_dump($graphObject);
	// $h = (array)$graphObject->backingData->data ;
	// var_dump($h::backingData);
	// foreach($graphObject as $x){
	//      dump($x);
	// }
	// $loc = $response->getGraphObject();
	//dump($graphObject);
	$loc = $graphObject->getProperty('data');
	dump($loc);
	$locs = $loc->asArray();
	dump($locs);

	function extract_e($collection, $object){
		$counter = 0;
		while($counter < count($object)){
			$hiren = $object[$counter]; 
			if (empty($hiren->from->id))
				dump($hiren);
			database($collection, $hiren->id, $hiren->from->id, 
				$hiren->from->name, $hiren->message, $hiren->created_time);
			$counter = $counter + 1 ;
		}
	}

	$counter = 0;
	while ( $counter < count($locs)) {
		$x = get_object_vars($locs[$counter]);
		$y = $x['comments']->data;
		//extract_e($counter, $y);
		$counter = $counter + 1;
	}

	

	//dump($locs[0]);
	//foreach($locs[0] as $x=>$y) {
	//    dump($y);
	//}
	$x = get_object_vars($locs[0]);
	//dump($x['comments']->data);
	$y = $x['comments']->data;

	function url($url){
		$client = new Client();
		$response=$client->get($url);
		$body = $response->json();
		return $body['paging']['next'];
	}

		$client = new Client();
		$response = $client->get($x['comments']->paging->next);
		$body = $response->json();
		$url = $body['paging']['next'];
		$count = 0 ;
		while(true){
		$result = url($url);
		 if(empty($result))
		 	break;
		 echo $count . ": " . $result . "<br>";
		 $url = $result;
		 $count = $count + 1;
		 // Api Limit !  :/
		 sleep(5);
	 	}
       
}