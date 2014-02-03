<?php

require 'vendor/autoload.php';
$login = file_get_contents("login.json");
$log_json = json_decode($login,true);

//echo $log_json['app_id'] ;

$config = array(
    'appId' => '721830601168795',
    'secret' => 'ad40de14362bf1b4152d2f128dcc640a',
    'allowSignedRequest' => false // optional but should be set to false for non-canvas apps
  );

  $facebook = new Facebook($config);
  $user_id = $facebook->getUser();
?>
<html>
  <head></head>
  <body>

  <?php
    if($user_id) {

      // We have a user ID, so probably a logged in user.
      // If not, we'll get an exception, which we handle below.
      try {

        $user_profile = $facebook->api('/me/friendrequests/','GET');
        //echo "Name: " . $user_profile['name'];
        var_dump($user_profile) ;

      } catch(FacebookApiException $e) {
        // If the user is logged out, you can have a
        // user ID even though the access token is invalid.
        // In this case, we'll get an exception, so we'll
        // just ask the user to login again here.
        $login_url = $facebook->getLoginUrl(array(
                       'scope' => 'read_friendlists'
                       ));
        echo 'Please <a href="' . $login_url . '">login.</a>';
        error_log($e->getType());
        error_log($e->getMessage());
      }
    } else {

      // No user, print a link for the user to login
      $login_url = $facebook->getLoginUrl();
      echo 'Please <a href="' . $login_url . '">login.</a>';

    }

  ?>

  </body>
</html>