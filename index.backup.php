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


// If you already have a valid access token:
$session = new FacebookSession('access-token');

// If you're making app-level requests:
$session = FacebookSession::newAppSession();

// To validate the session:
try {
    $session->validate();
} catch (FacebookRequestException $ex) {
    // Session not valid, Graph API returned an exception with the reason.
    echo $ex->getMessage();
} catch (\Exception $ex) {
    // Graph API returned info, but it may mismatch the current app or have expired.
    echo $ex->getMessage();
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