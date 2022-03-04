<?php
function google_calendar_settings_page () {
    
    require_once('google-calendar-api.php');
    
    $file_settings = WP_PLUGIN_DIR . "/pilots-plugin/" . 'google_calendar_settings.txt';
    
    $file_token = WP_PLUGIN_DIR . "/pilots-plugin/" . 'google_calendar_token.txt';
    
    if (isset($_POST['save_google_calendar_settings'])) {
        file_put_contents($file_settings, $_POST['client-id'] . "$$$" . $_POST['client-secret']);
    }
    
    $file_token_content = file_get_contents($file_token);
    if (!empty($file_token_content)) {

        $refresh_token = $file_token_content;
        //echo $refresh_token;
    }
    
   $file_settings_content = file_get_contents($file_settings);
    if (!empty($file_settings_content)) {
        $array_file_settings_content = explode('$$$',$file_settings_content);
        
        $client_id = $array_file_settings_content[0];

        /* Google App Client Secret */
        $client_secret = $array_file_settings_content[1];
        
        /* Google App Redirect Url */
        $client_redirect_url = 'https://www.pilots.co.il/wp-admin/admin.php?page=settingsgooglecalendar';
        //echo $client_id . " - " . $client_secret . " - " . $client_redirect_url;
    } 
    
    
    if(isset($_GET['code'])) {
    	try {
    		$capi = new GoogleCalendarApi();
    		$token = $capi->GetRefreshToken($GLOBALS['client_id'], $GLOBALS['client_redirect_url'], $GLOBALS['client_secret'], $_GET['code']);
    	
        	var_dump($token);
        	file_put_contents($file_token, $token['refresh_token']);
            
    	}
    	catch(Exception $e) {
    		echo $e->getMessage();
    		exit();
    	}
    }
    

    
    

    
    
    if(isset($_POST['create-event'])) {

        eventGoogleCalendar (11, $GLOBALS['client_id'], $GLOBALS['client_redirect_url'], $GLOBALS['client_secret'], $refresh_token, 0, '2021-12-06', '11:00');
        
     
    }

?>
<form action="" method="post" class="form_google_calendar_settings">

        <p><label>CLIENT ID</label> <input type="text" id="client-id" name="client-id" required value="<?php if (isset($client_id)) echo $client_id; ?>"></p>
        <p><label>CLIENT SECRET</label> <input type="text" id="client-secret" name="client-secret" required value="<?php if (isset($client_secret)) echo $client_secret; ?>"></p>
        
        
        <p><input type="submit" name="save_google_calendar_settings" id="save_google_calendar_settings" value="עדכן"></p>
      </form>
<?php

$login_url = 'https://accounts.google.com/o/oauth2/auth?scope=' . urlencode('https://www.googleapis.com/auth/calendar') . '&redirect_uri=' . urlencode($client_redirect_url) . '&response_type=code&client_id=' . $client_id . '&access_type=online';
if (!empty($file_settings_content)) {
?>

<a id="logo" href="<?= $login_url ?>">Login with Google</a>
<?php
}
?>
<form action="" method="post" class="form_google_calendar_adding">
    
    <input id="event-title" type="hidden" value="Test" >
	<input id="event-type" type="hidden" value="FIXED-TIME" >
	<input id="event-start-time" type="hidden" value="2021-12-03 10:55" >
	<input id="event-end-time" type="hidden" value="2021-12-03 10:55" >
	<input id="event-date" type="hidden" value="" >
    <p><input type="submit" name="create-event" id="create-event" value="עדכן"></p>
</form>

<?php

}
