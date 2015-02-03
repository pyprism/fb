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

use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequestException;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\GraphLocation;

$login = file_get_contents("login.json");
$log_json = json_decode($login,true);

FacebookSession::setDefaultApplication($log_json['app_id'], $log_json['app_secret']);

$helper = new FacebookRedirectLoginHelper('http://localhost:8000/redirect.php');

//$session = new FacebookSession($_GET['code']);
try {
    $session = $helper->getSessionFromRedirect();
} catch(FacebookRequestException $ex) {
// When Facebook returns an error
} catch(\Exception $ex) {
// When validation fails or other local issues
}
if ($session) {
// Logged in
    echo "logged in";
    echo "<br/>";
    $request = new FacebookRequest(
        $session,
        'GET',
        '/me/inbox'
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
  // $loc = $response->getGraphObject(GraphLocation::backingData());
   $loc = $graphObject->getProperty('data') ;
   //var_dump($loc);
    foreach($loc as $fuck)
        var_dump($fuck);
}