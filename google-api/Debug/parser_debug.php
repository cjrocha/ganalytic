<?php
//Created on July 2019 by Andrei Chirila (cjrocha.webs@gmail.com)
//Debug version! Exports data to Json format and write's log
//This code is a crawler of Google Analytics accounts
//Data retrieved is being inserted into MySql DB using DB connection
//and conform to client requirements


// Load the Google API PHP Client Library and Log reporting.
require_once __DIR__ . '/google-api/vendor/autoload.php';
require_once __DIR__ . '/google-api/Logging.php';

$gdate = date("Y-m-d");  //System Date required in Tables

//DB Credentials
$servername = "206.81.0.206:3306";
$username = "analytics_crawler";
$password = "3R5Z@OPS559A7";

//Start logging reporting
$log = new Logging();
$log->lfile("g-analytics_log.txt");

//Autenticate to Analytics
$KEY_FILE_LOCATION = __DIR__ . '/service-account-credentials.json';

// Create and configure a new client object.
$client = new Google_Client();
$client->setApplicationName("Analytics Reporting");
$client->setAuthConfig($KEY_FILE_LOCATION);
$client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
$analytics = new Google_Service_Analytics($client);

//Accounts to be checked: Incontinence->98225752; Multi Range->115772635; Velo Hand Dryers->98229323
$viewId = [98225752, 115772635, 98229323];
foreach ($viewId as $siteId){
	if ($siteId == 98225752){
		$dbname = "incontinence";
	} elseif ($siteId == 115772635){
		$dbname = "multirange";
	} else {
		$dbname = "velo";
	}

	//Retrieving data for Users table
		$users = $analytics->data_ga->get('ga:' . $siteId, 'yesterday', 'yesterday','ga:users, ga:newUsers, ga:percentNewSessions, ga:sessionsPerUser', ['dimensions'=>'ga:year, ga:month, ga:week, ga:day, ga:hour, ga:yearMonth, ga:yearWeek']);
		$u=0;
		foreach ($users as $user){
			$gyear = $user[0];
			$gmonth = $user[1];
			$gweek = $user[2];
			$gday = $user[3];
			$gyearmonth = $user[5];
			$gyearweek = $user[6];
			$udata[$u] = [
				'Date: ' => $gdate,
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
			#echo '<pre>';
			#print_r($udata);
		}
		
	//Retrievinng data for Sources table
		$sources = $analytics->data_ga->get('ga:' . $siteId, 'yesterday', 'yesterday','ga:sessions, ga:bounces, ga:bounceRate, ga:sessionDuration, ga:avgSessionDuration, ga:uniqueDimensionCombinations', ['dimensions'=>'ga:hour']);
		$s=0;
		foreach ($sources as $source){
			$sdata[$s] = [
				'Date: ' => $gdate,
				'Year: ' => $gyear,
				'Month: '=> $gmonth,
				'Week: ' => $gweek,
				'Day: ' => $gday,
				'Hour: '=> $source[0],
				'yearMonth' => $gyearmonth,
				'yearWeek'=> $gyearweek,
				'Sessions'=> $source[1],
				'Bounces'=> $source[2],
				'BounceRate'=> $source[3],
				'SessionDuration'=> $source[4],
				'AvgSessionDuration'=> $source[5],
				'UniqueDimensionCombinations'=> $source[6],
			];
		Handle($sdata[$s]);
		$s++;
		#echo '<pre>';
		#print_r($sdata);
		}
		
	//Retrieving data for Traffic Sources table
		$tsources = $analytics->data_ga->get('ga:' . $siteId, 'yesterday', 'yesterday','ga:organicSearches', ['dimensions'=>'ga:hour, ga:campaign, ga:source, ga:medium, ga:sourceMedium, ga:keyword']);
		$ts=0;
		foreach ($tsources as $tsource){	
			$tsdata[$ts] = [
				'Date: ' => $gdate,
				'Year: ' => $gyear,
				'Month: '=> $gmonth,
				'Week: ' => $gweek,
				'Day: ' => $gday,
				'Hour: '=> $tsource[0],
				'yearMonth' => $gyearmonth,
				'yearWeek'=> $gyearweek,
				'campaign'=> $tsource[1],
				'Source'=> $tsource[2],
				'medium'=> $tsource[3],
				'SourceMedium'=> $tsource[4],
				'Keyword'=> $tsource[5]
		//		'organicSearches'=> $tsource[6]
			];
		Handle($tsdata[$ts]);
		$ts++;
		#echo '<pre>';
		#print_r($tsdata);
		}
		
	//Retrieving data for Adwords table
		$addwords = $analytics->data_ga->get('ga:' . $siteId, 'yesterday', 'yesterday','ga:impressions, ga:adClicks, ga:adCost, ga:CPC, ga:CTR, ga:costPerGoalConversion', ['dimensions'=>'ga:hour, ga:adGroup']);
		$aw=0;
		foreach ($addwords as $addword){
			$adddata[$aw] = [
				'Date: ' => $gdate,
				'Year: ' => $gyear,
				'Month: '=> $gmonth,
				'Week: ' => $gweek,
				'Day: ' => $gday,
				'Hour: '=> $addword[0],
				'yearMonth' => $gyearmonth,
				'yearWeek'=> $gyearweek,
				'AdGroup'=> $addword[1],
				'Impressions'=> $addword[2],
				'AdClicks'=> $addword[3],
				'AdCost'=> $addword[4],
				'CPC'=>$addword[5],
				'CTR'=>$addword[6],
				'CostPerGoalConversion'=>$addword[7],
			];
			Handle($adddata[$aw]);
			$aw++;
			#echo '<pre>';
			#print_r($adddata);
		}
		
	//Retrieving data for Devices table
		$devices = $analytics->data_ga->get('ga:' . $siteId, 'yesterday', 'yesterday','ga:sessions', ['dimensions'=>'ga:hour, ga:deviceCategory']);
		$dv=0;
		foreach ($devices as $device){
			$devicedata[$dv] = [
				'Date: ' => $gdate,
				'Year: ' => $gyear,
				'Month: '=> $gmonth,
				'Week: ' => $gweek,
				'Day: ' => $gday,
				'Hour: '=> $device[0],
				'yearMonth' => $gyearmonth,
				'yearWeek'=> $gyearweek,
				'deviceCategory'=> $device[1]
		//		'Sessions' => $device[2]
			];
			Handle($devicedata[$dv]);
			$dv++;
			#echo '<pre>';
			#print_r($devicedata);
		}
		
	//Retrieving data for Social table
		$socials = $analytics->data_ga->get('ga:' . $siteId, 'yesterday', 'yesterday','ga:sessions', ['dimensions'=>'ga:hour, ga:deviceCategory']);
		$so=0;
		foreach ($socials as $social){
			$socdata[$so] = [
				'Date: ' => $gdate,
				'Year: ' => $gyear,
				'Month: '=> $gmonth,
				'Week: ' => $gweek,
				'Day: ' => $gday,
				'Hour: '=> $social[0],
				'yearMonth' => $gyearmonth,
				'yearWeek'=> $gyearweek,
				'socialInteractionNetwork'=> $social[1]
		//		'Sessions' => $social[2]
			];
			Handle($socdata[$so]);
			$so++;
			#echo '<pre>';
			#print_r($socdata);
		}

	//Retrieving data for Page Tracking table
		$ptracs = $analytics->data_ga->get('ga:' . $siteId, 'yesterday', 'yesterday','ga:pageValue, ga:entrances, ga:entranceRate, ga:pageviews, ga:pageviewsPerSession, ga:uniquePageviews, ga:timeOnPage, ga:avgTimeOnPage, ga:exits, ga:exitRate', ['dimensions'=>'ga:hour']);
		$pt=0;
		foreach ($ptracs as $ptrac){
			$ptdata[$pt] = [
				'Date: ' => $gdate,
				'Year: ' => $gyear,
				'Month: '=> $gmonth,
				'Week: ' => $gweek,
				'Day: ' => $gday,
				'Hour: '=> $ptrac[0],
				'yearMonth' => $gyearmonth,
				'yearWeek'=> $gyearweek,
				'pageValue'=> $ptrac[1],
				'entrances'=> $ptrac[2],
				'entranceRate'=> $ptrac[3],
				'pageviews'=> $ptrac[4],
				'pageviewsPerSession'=> $ptrac[5],
				'uniquePageviews'=> $ptrac[6],
				'timeOnPage'=> $ptrac[7],
				'avgTimeOnPage'=> $ptrac[8],
				'exits'=> $ptrac[9],
				'exitRate'=> $ptrac[10],
			];
			Handle($ptdata[$pt]);
			$pt++;
			#echo '<pre>';
			#print_r($ptdata);
		}
/*	//Retrieving data for Goal Conversion table
		$gconvs = $analytics->data_ga->get('ga:' . $viewId, 'yesterday', 'yesterday','ga:sessions, ga:goalXXCompletions, ga:goalXXConversionRate', ['dimensions'=>'ga:hour']);
		$gc=0;
		foreach ($gconvs as $gconv){
			$gconvdata[$gc] = [
				'Date: ' => $gdate,
				'Year: ' => $gyear,
				'Month: '=> $gmonth,
				'Week: ' => $gweek,
				'Day: ' => $gday,
				'Hour: '=> $gconv[0],
				'yearMonth' => $gyearmonth,
				'yearWeek'=> $gyearweek,
				'goalXXCompletions'=> $gconv[1],
				'goalXXConversionRate'=> $gconv[2]
			];
			Handle($gconvdata[$gc]);
			$gc++;
			#echo '<pre>';
			#print_r($gconvdata);
		}
*/
	// Close log file
		$log->lwrite('Export Completed Successfuly on '.$gdate);
		$log->lwrite('-----------------------------------------');
		$log->lclose();
	
}


// Take's the json data from Analytics call and delivers into Json file
// Does not erase data in Json, but adds content to file 
function Handle($event){
	$filename = $siteId.'-analytics.json';
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