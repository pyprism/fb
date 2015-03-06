<?php
/**
 * Created by PhpStorm.
 * User: prism
 * Date: 3/2/15
 * Time: 10:56 AM
 */

session_start();

error_reporting(-1);
ini_set('display_errors', 'On');
set_time_limit(0);

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

try{
    $session = $helper->getSessionFromRedirect();
} catch (\Exception $ex) {
    echo $ex;
}

if($session){
    $me = new FacebookRequest(
        $session,
        'GET',
        '/me'
    );

    $res = $me->execute();
    $name = $res->getGraphObject()->getProperty('name');

    $request = new FacebookRequest(
        $session,
        'GET',
        '/me/inbox'
    );
    $response = $request->execute();
    $graph_object = $response->getGraphObject();
    $loc = $graph_object->getProperty('data')->asArray();

    function extract_save($collection, $name, $object){
        $counter = 0;
        while($counter < count($object->data)){
            $hiren = $object[$counter];
            database($collection, $name, $hiren->id, $hiren->from->id,
                $hiren->from->name, $hiren->message, $hiren->created_time);
            $counter = $counter + 1 ;
        }
    }

    $counter = 0 ;
    while($counter < count($loc)){
        $ob = get_object_vars($loc[$counter]);
        $object = $ob['comments'];
        //extract_save($counter, $name, $object);
        $counter++;

        $client = new Client();
        $response = $client->get($ob['comments']->paging->next);
        $body = $response->json();
        if(!array_filter($body))
            break;
        dump($body);
    }
}else
    echo "U r f@\$ked up buddy !  :/ , The Judgement Day is coming for u ";