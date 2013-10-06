<?php    
    require_once "config.php";
    
    // Base URL
    $baseUrl = 'http://api.fitbit.com';
    
    // Request token path
    $req_url = $baseUrl . '/oauth/request_token';

    // Authorization path
    $authurl = $baseUrl . '/oauth/authorize';

    // Access token path
    $acc_url = $baseUrl . '/oauth/access_token';

    // Fitbit API call (get activities for specified date)
    $apiCall = "http://api.fitbit.com/1/user/-/activities/date/".date('Y-m-d').".xml";

    // Start session to store the information between calls
    session_start();

    // In state=1 the next request should include an oauth_token.
    // If it doesn't go back to 0
    if ( !isset($_GET['oauth_token']) && $_SESSION['state']==1 ) $_SESSION['state'] = 0;

    try 
    {
        // Create OAuth object
        $oauth = new OAuth($conskey,$conssec,OAUTH_SIG_METHOD_HMACSHA1,OAUTH_AUTH_TYPE_AUTHORIZATION);

        // Enable ouath debug (should be disabled in production)
        $oauth->enableDebug();

        if ( $_SESSION['state'] == 0 ) 
        {
            // Getting request token. Callback URL is the Absolute URL to which the server provder will redirect the User back when the obtaining user authorization step is completed.
            $request_token_info = $oauth->getRequestToken($req_url, $callbackUrl);

            // Storing key and state in a session.
            $_SESSION['secret'] = $request_token_info['oauth_token_secret'];
            $_SESSION['state'] = 1;

            // Redirect to the authorization.
            header('Location: '.$authurl.'?oauth_token='.$request_token_info['oauth_token']);
            exit;
        } 
        else if ( $_SESSION['state']==1 ) 
        {
            // Authorized. Getting access token and secret
            $oauth->setToken($_GET['oauth_token'],$_SESSION['secret']);
            $access_token_info = $oauth->getAccessToken($acc_url);

            // Storing key and state in a session.
            $_SESSION['state'] = 2;
            $_SESSION['token'] = $access_token_info['oauth_token'];
            $_SESSION['secret'] = $access_token_info['oauth_token_secret'];
        } 

        // Setting asccess token to the OAuth object
        $oauth->setToken($_SESSION['token'],$_SESSION['secret']);

        // Performing API call 
        $oauth->fetch($apiCall);

        // Getting last response
        $response = $oauth->getLastResponse();

        // Initializing the simple_xml object using API response 
        $xml = simplexml_load_string($response);
    } 
    catch( OAuthException $E ) 
    {
        print_r($E);
    }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <title>Fitbit Example</title>
  </head>
  <body>
  <?php print_r($xml); ?>
  
  <!-- Show activities on the page -->
    Unit System: <?php echo $xml->unitSystem ?><br />
    Active Score: <?php echo $xml->summary->activeScore ?><br />
    Calories Out: <?php echo $xml->summary->caloriesOut ?><br />
    Fairly Active Minutes: <?php echo $xml->summary->fairlyActiveMinutes ?><br />
    Lightly Active Minutes: <?php echo $xml->summary->lightlyActiveMinutes ?><br />
    Very Active Minutes: <?php echo $xml->summary->veryActiveMinutes ?><br />    
    Sedentary Minutes: <?php echo $xml->summary->sedentaryMinutes ?><br />    
    Steps: <?php echo $xml->summary->steps ?><br />
    Distances:<br />
    <table border="1">
      <tr>
        <th>Activity</th>
        <th>Distance</th>
      </tr>
      <?php foreach ($xml->summary->distances->activityDistance as $distance) { ?>
        <tr>
          <td><?php echo $distance->activity ?></td>
          <td><?php echo $distance->distance ?></td>
        </tr>
      <?php } ?>
    </table>
  </body>
</html>