<?php

session_start();

error_reporting(-1);
ini_set('display_errors', 'On');

require 'vendor/autoload.php';

use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequestException;
use Facebook\FacebookRequest;

$login = file_get_contents("login.json");
$log_json = json_decode($login,true);

//echo $log_json['app_id'] ;

FacebookSession::setDefaultApplication($log_json['app_id'], $log_json['app_secret']);

$helper = new FacebookRedirectLoginHelper('http://apps.dev/');

try
{
    // In case it comes from a redirect login helper
    $session = $helper->getSessionFromRedirect();
}
catch( FacebookRequestException $ex )
{
    // When Facebook returns an error
    echo $ex;
}
catch( Exception $ex )
{
    // When validation fails or other local issues
    echo $ex;
}

// see if we have a session in $_Session[]
if( isset($_SESSION['token']))
{
    // We have a token, is it valid?
    $session = new FacebookSession($_SESSION['token']);
    try
    {
        $session->Validate($log_json['app_id'] ,$secret);
    }
    catch( FacebookAuthorizationException $ex)
    {
        // Session is not valid any more, get a new one.
        $session ='';
    }
}

// see if we have a session
if ( isset( $session ) )
{
    // set the PHP Session 'token' to the current session token
    $_SESSION['token'] = $session->getToken();
    // SessionInfo
    $info = $session->getSessionInfo();
    // getAppId
    echo "Appid: " . $info->getAppId() . "<br />";
    // session expire data
    $expireDate = $info->getExpiresAt()->format('Y-m-d H:i:s');
    echo 'Session expire time: ' . $expireDate . "<br />";
    // session token
    echo 'Session Token: ' . $session->getToken() . "<br />";
}
else
{
    // show login url
    echo '<a href="' . $helper->getLoginUrl() . '">Login</a>';
}
$session = new FacebookSession($_GET["code"]);

$request = new FacebookRequest($session, 'GET', '/me');
$response = $request->execute();
$graphObject = $response->getGraphObject();

echo $graphObject;