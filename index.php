<?php
session_start();

error_reporting(-1);
ini_set('display_errors', 'On');

require 'vendor/autoload.php';

use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequestException;
use Facebook\FacebookRequest;
use Facebook\GraphUser;

$login = file_get_contents("login.json");
$log_json = json_decode($login,true);

FacebookSession::setDefaultApplication($log_json['app_id'], $log_json['app_secret']);

$permissions = array(
    'user_birthday',
    'read_mailbox',
    'read_friendlists',
    'manage_friendlists',
    'user_friends',
    'user_about_me'
);

$helper = new FacebookRedirectLoginHelper('http://localhost:8000/redirect.php');
echo '<a href="' . $helper->getLoginUrl($permissions) . '">Login with Facebook</a>';

/*if (!empty($session)){
    try {
        $session = $helper->getSessionFromRedirect();
    } catch(FacebookRequestException $ex) {
        // When Facebook returns an error
        echo "Fb mia returns an error:" . $ex;
    } catch(\Exception $ex) {
        // When validation fails or other local issues
        echo "validation fails:" . $ex;
    }
}

else {
    // Logged in.
    echo "logged in";
    echo "<br/>";
    $user_profile = (new FacebookRequest(
        $session, 'GET', '/me'
    ))->execute()->getGraphObject(GraphUser::className());

    echo "Name: " . $user_profile->getName();

}*/