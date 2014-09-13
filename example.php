<?php
/**
 * Created by PhpStorm.
 * User: prism
 * Date: 9/10/14
 * Time: 9:44 AM
 */
session_start();
error_reporting(-1);
ini_set('display_errors', 'On');

require 'vendor/autoload.php';

use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequestException;

$login = file_get_contents("login.json");
$log_json = json_decode($login,true);

$permissions = array(
    'user_birthday',
    'read_mailbox'
);

FacebookSession::setDefaultApplication($log_json['app_id'], $log_json['app_secret'] );

// Create the login helper and replace REDIRECT_URI with your URL
// Use the same domain you set for the apps 'App Domains'
// e.g. $helper = new FacebookRedirectLoginHelper( 'http://mydomain.com/redirect' );
$helper = new FacebookRedirectLoginHelper( 'http://apps.dev/example.php' );

// Check if existing session exists
if ( isset( $_SESSION ) && isset( $_SESSION['fb_token'] ) ) {
    // Create new session from saved access_token
    $session = new FacebookSession( $_SESSION['fb_token'] );

    // Validate the access_token to make sure it's still valid
    try {
        if ( ! $session->validate() ) {
            $session = null;
        }
    } catch ( Exception $e ) {
        // Catch any exceptions
        $session = null;
    }
} else {
    // No session exists
    try {
        $session = $helper->getSessionFromRedirect();
    } catch( FacebookRequestException $ex ) {

        // When Facebook returns an error
    } catch( Exception $ex ) {

        // When validation fails or other local issues
        echo $ex->getMessage();
    }
}

// Check if a session exists
if ( isset( $session ) ) {

    // Save the session
    $_SESSION['fb_token'] = $session->getToken();

    // Create session using saved token or the new one we generated at login
    $session = new FacebookSession( $session->getToken() );

    // Create the logout URL (logout page should destroy the session)
    $logoutURL = $helper->getLogoutUrl( $session, 'http://apps.dev/logout' );

    echo '<a href="' . $logoutURL . '">Log out</a>';
} else {
    // No session

    // Get login URL
    $loginUrl = $helper->getLoginUrl( $permissions );

    echo '<a href="' . $loginUrl . '">Log in</a>';
}

echo $_GET["code"] ;