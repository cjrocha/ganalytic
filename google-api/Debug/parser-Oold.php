<?php

// Load the Google API PHP Client Library and Log reporting.
require_once __DIR__ . '/google-api/vendor/autoload.php';
require_once __DIR__ . '/google-api/Logging.php';//Logging data

$date_scr = date("Y-m-d");//System Date required in Tables
$day_scr = date("d");//System Day required for some tables 
//$log = new Logging();
//$log->lfile("Scape_logfile_$date_scr.txt");

// Creates and returns the Analytics Reporting service object.
// Use the developers console and download your service account
// credentials in JSON format. Place them in this directory or
// change the key file location if necessary.
$KEY_FILE_LOCATION = __DIR__ . '/service-account-credentials.json';

// Create and configure a new client object.
$client = new Google_Client();
$client->setApplicationName("Analytics Reporting");
$client->setAuthConfig($KEY_FILE_LOCATION);
$client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
$analytics = new Google_Service_Analytics($client);

$viewId = 98225752; //115772635; 98229323 

//Retrieving data for Users table
$users = $analytics->data_ga->get('ga:' . $viewId, 'yesterday', 'yesterday','ga:users, ga:newUsers, ga:percentNewSessions, ga:sessionsPerUser', ['dimensions'=>'ga:year, ga:month, ga:week, ga:day, ga:hour, ga:yearMonth, ga:yearWeek']);
$u=0;
foreach ($users as $user){
	$udata[$u] = [
		'Date: ' => $date_scr,
		'Year: ' => $user[0],
		'Month: '=> $user[1],
		'Week: ' => $user[2],
		'Day: ' => $user[3],
		'Hour: '=> $user[4],
		'yearMonth' => $user[5],
		'yearWeek'=> $user[6],
		'Users'=> $user[7],
		'NewUsers'=> $user[8],
		'PercentNewSessions'=> $user[9],
		'SessionPerUser'=>$user[10]
	];
	Handle($udata[$u]);
	$u++;
}
//Retrievinng data for Sources table
$sources = $analytics->data_ga->get('ga:' . $viewId, 'yesterday', 'yesterday','ga:sessions, ga:bounces, ga:bounceRate, ga:sessionDuration, ga:avgSessionDuration, ga:uniqueDimensionCombinations', ['dimensions'=>'ga:year, ga:month, ga:week, ga:day, ga:hour, ga:yearMonth, ga:yearWeek']);
$s=0;
foreach ($sources as $source){
	$sdata[$s] = [
		'Date: ' => $date_scr,
		'Year: ' => $source[0],
		'Month: '=> $source[1],
		'Week: ' => $source[2],
		'Day: ' => $source[3],
		'Hour: '=> $source[4],
		'yearMonth' => $source[5],
		'yearWeek'=> $source[6],
		'Sessions'=> $source[7],
		'Bounces'=> $source[8],
		'BounceRate'=> $source[9],
		'SessionDuration'=>$source[10],
		'AvgSessionDuration'=>$source[11],
		'UniqueDimensionCombinations'=>$source[12],
	];
	Handle($sdata[$s]);
	$s++;
}
/*
//Retrieving data for Traffic Sources table
$tsources = $analytics->data_ga->get('ga:' . $viewId, 'yesterday', 'yesterday','ga:sessions, ga:bounces, ga:bounceRate, ga:sessionDuration, ga:avgSessionDuration, ga:uniqueDimensionCombinations', ['dimensions'=>'ga:year, ga:month, ga:week, ga:hour, ga:yearMonth, ga:yearWeek, ']);
$ts=0;
foreach ($tsources as $tsource){
	$tsdata[$ts] = [
		'Date: ' => $date_scr,
		'Year: ' => $tsource[0],
		'Month: '=> $tsource[1],
		'Week: ' => $tsource[2],
		'Day: ' => $day_scr,
		'Hour: '=> $tsource[3],
		'yearMonth' => $tsource[4],
		'yearWeek'=> $tsource[5],
		'yearWeek'=> $tsource[5],
		'Sessions'=> $tsource[7],
		'Bounces'=> $tsource[8],
		'BounceRate'=> $tsource[9],
		'SessionDuration'=>$tsource[10],
		'AvgSessionDuration'=>$tsource[11],
		'UniqueDimensionCombinations'=>$tsource[12],
	];
	Handle($sdata[$ts]);
	$ts++;
}
*/

//Retrieving data for Adwords table
$addwords = $analytics->data_ga->get('ga:' . $viewId, 'yesterday', 'yesterday','ga:impressions, ga:adClicks, ga:adCost, ga:CPC, ga:CTR, ga:costPerGoalConversion', ['dimensions'=>'ga:year, ga:month, ga:week, ga:hour, ga:yearMonth, ga:yearWeek, ga:adGroup']);
$aw=0;
foreach ($addwords as $addword){
	$adddata[$aw] = [
		'Date: ' => $date_scr,
		'Year: ' => $tsource[0],
		'Month: '=> $tsource[1],
		'Week: ' => $tsource[2],
		'Day: ' => $day_scr,
		'Hour: '=> $tsource[3],
		'yearMonth' => $tsource[4],
		'yearWeek'=> $tsource[5],
		'AdGroup'=> $tsource[6],
		'Impressions'=> $tsource[7],
		'AdClicks'=> $tsource[8],
		'AdCost'=> $tsource[9],
		'CPC'=>$tsource[10],
		'CTR'=>$tsource[11],
		'CostPerGoalConversion'=>$tsource[12],
	];
	Handle($adddata[$aw]);
	$aw++;
}

//Retrieving data for Goal Conversion table
$gconvs = $analytics->data_ga->get('ga:' . $viewId, 'yesterday', 'yesterday','ga:goalXXCompletions, ga:goalXXConversionRate', ['dimensions'=>'ga:year, ga:month, ga:week, ga:day, ga:hour, ga:yearMonth, ga:yearWeek']);
$gc=0;
foreach ($gconvs as $gconv){
	$gconvdata[$gc] = [
		'Date: ' => $date_scr,
		'Year: ' => $gconv[0],
		'Month: '=> $gconv[1],
		'Week: ' => $gconv[2],
		'Day: ' => $gconv[3],
		'Hour: '=> $gconv[4],
		'yearMonth' => $gconv[5],
		'yearWeek'=> $gconv[6],
		'goalXXCompletions'=> $gconv[7],
		'goalXXConversionRate'=> $tsource[8]
	];
	Handle($gconvdata[$gc]);
	$gc++;
}

//Retrieving data for Devices table
$devices = $analytics->data_ga->get('ga:' . $viewId, 'yesterday', 'yesterday','ga:sessions', ['dimensions'=>'ga:year, ga:month, ga:week, ga:hour, ga:yearMonth, ga:yearWeek, ga:deviceCategory']);
$dv=0;
foreach ($devices as $device){
	$devicedata[$dv] = [
		'Date: ' => $date_scr,
		'Year: ' => $devicedata[0],
		'Month: '=> $devicedata[1],
		'Week: ' => $devicedata[2],
		'Day: ' => $day_scr,
		'Hour: '=> $devicedata[3],
		'yearMonth' => $devicedata[4],
		'yearWeek'=> $devicedata[5],
		'deviceCategory'=> $devicedata[6],
	];
	Handle($devicedata[$dv]);
	$dv++;
}

//Retrieving data for Social table
$socials = $analytics->data_ga->get('ga:' . $viewId, 'yesterday', 'yesterday','ga:sessions', ['dimensions'=>'ga:year, ga:month, ga:week, ga:hour, ga:yearMonth, ga:yearWeek, ga:deviceCategory']);
$so=0;
foreach ($socials as $social){
	$socdata[$so] = [
		'Date: ' => $date_scr,
		'Year: ' => $socdata[0],
		'Month: '=> $socdata[1],
		'Week: ' => $socdata[2],
		'Day: ' => $day_scr,
		'Hour: '=> $socdata[3],
		'yearMonth' => $socdata[4],
		'yearWeek'=> $socdata[5],
		'socialInteractionNetwork'=> $socdata[6],
	];
	Handle($socdata[$so]);
	$so++;
}

//Retrieving data for Page Tracking table
$ptracs = $analytics->data_ga->get('ga:' . $viewId, 'yesterday', 'yesterday','ga:pageValue, ga:entrances, ga:entranceRate, ga:pageviews, ga:pageviewsPerSession, ga:uniquePageviews, ga:timeOnPage, ga:avgTimeOnPage, ga:exits, ga:exitRate', ['dimensions'=>'ga:year, ga:month, ga:week, ga:day, ga:hour, ga:yearMonth, ga:yearWeek, ga:deviceCategory']);
$pt=0;
foreach ($ptracs as $ptrac){
	$ptdata[$pt] = [
		'Date: ' => $date_scr,
		'Year: ' => $$ptrac[0],
		'Month: '=> $$ptrac[1],
		'Week: ' => $$ptrac[2],
		'Day: ' => $ptrac[3],
		'Hour: '=> $ptrac[4],
		'yearMonth' => $ptrac[5],
		'yearWeek'=> $ptrac[6],
		'pageValue'=> $socdata[7],
		'entrances'=> $socdata[8],
		'entranceRate'=> $socdata[9],
		'pageviews'=> $socdata[10],
		'pageviewsPerSession'=> $socdata[11],
		'uniquePageviews'=> $socdata[12],
		'timeOnPage'=> $socdata[13],
		'avgTimeOnPage'=> $socdata[14],
		'exits'=> $socdata[15],
		'exitRate'=> $socdata[16],
	];
	Handle($ptdata[$pt]);
	$pt++;
}
//$times = $analytics->data_ga->get('ga:' . $viewId, 'yesterday', 'yesterday', 'ga:users', ['dimensions'=>'ga:year, ga:month, ga:week, ga:day, ga:hour, ga:yearMonth, ga:yearWeek']);
//$i=0;
//foreach ($times as $time){
//	$tdata[$i] = [
//		'Date: ' => $date_scr,
//		'Year: ' => $time[0],
//		'Month: '=> $time[1],
//		'Week: ' => $time[2],
//		'Day: ' => $time[3],
//		'Hour: '=> $time[4],
//		'yearMonth' => $time[5],
//		'yearWeek'=> $time[6]
//	];
//	$i++

//	Handle($tdata);
#echo '<pre>';
#print_r($userdata);
//}


/*
$sources = $analytics->data_ga->get('ga:' . $viewId, 'yesterday', 'yesterday', 'ga:organicSearches', ['dimensions'=>'ga:date,ga:referralPath,ga:source,ga:operatingSystem,ga:browser']);
foreach ($sources as $source){
	$sourcedata = [
		'Date: ' => $source[0],
		'Referral Path: ' => $source[1],
		'Source: '=> $source[2],
		'Operating System: ' => $source[3],
		'Browser: ' => $source[4],
		'Organic Searches: '=> $source[5]
	];
	Handle($sourcedata);
#echo '<pre>';
#print_r($sourcedata);
}

$dds = $analytics->data_ga->get('ga:' . $viewId, '7daysAgo', 'today', 'ga:impressions,ga:adClicks,ga:adCost', ['dimensions'=>'ga:date,ga:adGroup,ga:adDisplayUrl,ga:adwordsCampaignID']);
foreach ($dds as $dd){
	$sourceadd = [
		'Date: ' => $dd[0],
		'Add Group: ' => $dd[1],
		'Add Display Url: ' => $dd[2],
		'Add Campaign Id: ' => $dd[3],
		'Add Impressions: '=> $dd[4],
		'Add Clicks: '=> $dd[5],
		'Add Cost: '=> $dd[6],
	];
	Handle($sourceadd);
}

$places = $analytics->data_ga->get('ga:' . $viewId, 'yesterday', 'yesterday', 'ga:pageviews', ['dimensions'=>'ga:date,ga:country,ga:city,ga:networkDomain,ga:language,ga:pagePath']);
foreach ($places as $place){
	$sourceplace = [
		'Date: ' => $place[0],
		'Country: ' => $place[1],
		'City: ' => $place[2],
		'Network Domain: ' => $place[3],
		'Language: '=> $place[4],
		'Page Path: '=> $place[5],
		'Page Views: '=> $place[6],
	];
	Handle($sourceplace);
}*/
// Take's the json data from Analytics call and delivers into Json file
// Does not erase data in Json, but adds content to file 
function Handle($event){
$filename = 'analytics.json';
// read the file if present
$handle = fopen($filename, 'r+');
// create the file if needed
if ($handle === false){
    $handle = fopen($filename, 'w+');
}
if ($handle){
    // seek to the end
    fseek($handle, 0, SEEK_END);
    // are we at the end of is the file empty
    if (ftell($handle) > 0){
        // move back a byte
        fseek($handle, -1, SEEK_END);
        // add the trailing comma
        fwrite($handle, ',', 1);
        // add the new json string
        fwrite($handle, json_encode($event) . ']');
    }else{
        // write the first event inside an array
        fwrite($handle, json_encode(array($event)));
    }
        // close the handle on the file
        fclose($handle);
}
}
?>