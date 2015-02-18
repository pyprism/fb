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
	//echo count($y) ;
	//echo "<br>";
	//echo $y[0]->id;
	//echo '<br>';
	//echo $y[0]->from->id;
	//echo '<br>';
	//echo $y[0]->from->name;
	// '<br>';
	//echo $y[0]->message;
	//echo '<br>';
	//echo $y[0]->created_time;
	//echo '<br>';
	//foreach ( $y as $key => $value ) {
	//	echo $key . $value;
	//}
	/*
	* access to next pagination data 
	*/
	//try{
	//  dump($x['comments']->paging->next);
	//}
	//catch(Exception $e){
	//	echo "Exception baby : " . $e->getMessage() ;
	//}
	// foreach($loc as $fuck)
	//   var_dump($fuck);
	//$client = new Client();
	//$response = $client->get("https://graph.facebook.com/v2.2/1463911900493144/comments?access_token=CAAKXPUpm20EBAG6CEsO4Lh32ZCNlrWrqrfinHC9MQP6rm0LVSYVpPWquEtLNaueSw4nw82RZADeqrIQiP7HutWKua1Aoa25QnNIvxlsMlkDdukIN2AvaZCn24IzZCj4PdQQLX1ang6PY9tnsZAnm6xsZCXqQ6epkAqnqxRT5MYfy8vw2ZBjrbTKvbrZAJmkuZAYVB4Xtnzg4zSCbFXMuEPyNW&limit=25&until=1423765183&__paging_token=enc_AdAczc5vQhqbetxYNqwtljuB5eENvDWeBH05FJcxrRHoawOlTofZBDE8p0YA5Q82DJ5EvuEQaoog8LnAxQ7Lw5AwV");
	//$body = $response->json();
	//dump($body);
	//database();
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