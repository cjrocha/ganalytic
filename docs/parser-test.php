<?php
//Created on July 2019 by Andrei Chirila (cjrocha.webs@gmail.com)
//This code is a crawler of Google Analytics accounts
//Data retrieved is being inserted into MySql DB using DB connection
//and conform to client requirements


// Load the Google API PHP Client Library and Log reporting.
require_once __DIR__ . '/google-api/vendor/autoload.php';
require_once __DIR__ . '/google-api/Logging.php';

$sysdate = date("Y-m-d");  //System Date required in Tables

//DB Credentials
$servername = "206.81.0.206:3306";
$username = "analytics_crawler";
$password = "3R5Z@OPS559A7";

//Start logging reporting
$log = new Logging();
$log->lfile("g-analytics_log.txt");

//Autenticate to Analytics
$KEY_FILE_LOCATION = __DIR__ . '/google-api/service-account-credentials.json';

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
	//Connecting to DB
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
		$log->lwrite('Connection failed: '.$conn->connect_error);
		die("Connection failed: " . $conn->connect_error);
	} else {

	//Retrieving data for Users table
		$users = $analytics->data_ga->get('ga:' . $siteId, '2019-11-09', 'yesterday', 'ga:users, ga:newUsers, ga:percentNewSessions, ga:sessionsPerUser, ga:sessions, ga:bounces, ga:bounceRate, ga:sessionDuration, ga:avgSessionDuration, ga:uniqueDimensionCombinations', ['dimensions'=>'ga:date, ga:year, ga:day, ga:hour, ga:yearMonth, ga:yearWeek, ga:userType']);
		foreach ($users as $user){
			$gdate = $user[0];
			$gyear = $user[1];
			$gday = $user[2];
			$gyearmonth = $user[4];
			$gyearweek = $user[5];
			//Sorting month and week for later use
			$n = 2;
			$ggmonth = strlen($gyearmonth) - $n;
			$gmonth = substr($gyearmonth, $ggmonth);
			$ggweek = strlen($gyearweek) - $n;
			$gweek = substr($gyearweek, $ggweek);
		}

	//Retrieving data for Users table
//		$users = $analytics->data_ga->get('ga:' . $siteId, 'yesterday', 'yesterday', 'ga:users, ga:newUsers, ga:percentNewSessions, ga:sessionsPerUser, ga:sessions, ga:bounces, ga:bounceRate, ga:sessionDuration, ga:avgSessionDuration, ga:uniqueDimensionCombinations', ['dimensions'=>'ga:date, ga:year, ga:day, ga:hour, ga:yearMonth, ga:yearWeek, ga:userType']);
//		$addwords = $analytics->data_ga->get('ga:' . $siteId, 'yesterday', 'yesterday', 'ga:impressions, ga:adClicks, ga:adCost, ga:CPC, ga:CTR, ga:costPerConversion, ga:goalCompletionsAll, ga:costPerGoalConversion, ga:goalConversionRateAll', ['dimensions'=>'ga:adGroup, ga:adKeywordMatchType, ga:adMatchedQuery, ga:goalCompletionLocation']);
		$goals = $analytics->data_ga->get('ga:' . $siteId, '2019-11-09', 'yesterday', 'ga:goalStartsAll, ga:goalCompletionsAll, ga:goalValueAll, ga:goalValuePerSession, ga:goalConversionRateAll, ga:goalAbandonsAll, ga:goalAbandonRateAll', ['dimensions'=>'ga:hour, ga:goalCompletionLocation, ga:goalPreviousStep1, ga:goalPreviousStep2, ga:goalPreviousStep3']);
		foreach ($goals as $goal){
//			$gdate = $user[0];
//  		$gyear = $user[1];
//			$gday = $user[2];
//			$gyearmonth = $user[4];
//			$gyearweek = $user[5];
			//Sorting month and week for later use
//			$n = 2;
//			$ggmonth = strlen($gyearmonth) - $n;
//			$gmonth = substr($gyearmonth, $ggmonth);
//			$ggweek = strlen($gyearweek) - $n;
//			$gweek = substr($gyearweek, $ggweek);
			
//			echo "\n";
//			echo "0 is: ".$goal[0]."\n";
//			echo "1 is: ".$goal[1]."\n";
//			echo "2 is: ".$goal[2]."\n";
//  		echo "3 is: ".$goal[3]."\n";
//			echo "4 is: ".$goal[4]."\n";
//			echo "5 is: ".$goal[5]."\n";
//			echo "6 is: ".$goal[6]."\n";
//			echo "7 is: ".$goal[7]."\n";
//			echo "8 is: ".$goal[8]."\n";
//			echo "9 is: ".$goal[9]."\n";
//			echo "10 is: ".$goal[10]."\n";
//			echo "11 is: ".$goal[11]."\n";
//			echo "12 is: ".$user[12]."\n";
//			echo "13 is: ".$user[13]."\n";
//			echo "14 is: ".$user[14]."\n";
//			echo "15 is: ".$user[15]."\n";
//			echo "16 is: ".$user[16]."\n";
			
			
			
			$sqlg = "INSERT INTO goals (g_date, g_year, g_month, g_week, g_day, g_hour, g_yearmonth, g_yearweek, g_goalcomLocation, g_goalprevStep1, g_goalprevStep2, g_goalprevStep3, g_goalStartsAll, g_goalAllCompletions, g_goalAllValue, g_goalValuePerSession, g_goalConversionRateAll, g_goalAllAbandons, g_goalAbandonRateAll, g_processdate)
				VALUES ('$gdate', '$gyear', '$gmonth', '$gweek', '$gday', '$goal[0]', '$gyearmonth', '$gyearweek', '$goal[1]', '$goal[2]', '$goal[3]', '$goal[4]', '$goal[5]', '$goal[6]', '$goal[7]', '$goal[8]', '$goal[9]', '$goal[10]', '$goal[11]', '$sysdate')";
			if(mysqli_query($conn, $sqlg)){
//				echo "Records added successfully.";
			} else{
				$log->lwrite('ERROR: Could not execute $sqlg.  '. mysqli_error($conn));
			}
		}

		
/*		
	//Retrievinng data for Sources table
		$tsources = $analytics->data_ga->get('ga:' . $siteId, 'yesterday', 'yesterday','ga:organicSearches, ga:socialInteractions, ga:socialInteractionsPerSession', ['dimensions'=>'ga:hour, ga:campaign, ga:source, ga:medium, ga:sourceMedium, ga:keyword, ga:deviceCategory']);
		foreach ($tsources as $tsource){
			$sqlso = "INSERT INTO sources (so_date, so_year, so_month, so_week, so_day, so_hour, so_yearmonth, so_yearweek, so_campaign, so_source, so_medium, so_sourcemedium, so_keyword, so_devicecategory,  so_organicsearches, so_socialInteractions, so_socialInteractionsPerSession, so_processdate)
				VALUES ('$gdate', '$gyear', '$gmonth', '$gweek', '$gday', '$tsource[0]', '$gyearmonth', '$gyearweek', '$tsource[1]', '$tsource[2]', '$tsource[3]', '$tsource[4]', '$tsource[5]', '$tsource[6]', '$tsource[7]', '$tsource[8]', '$tsource[9]', '$sysdate')";
			if(mysqli_query($conn, $sqlso)){
//				echo "Records added successfully.";
			} else{
				$log->lwrite('ERROR: Could not execute $sqlso.  '. mysqli_error($conn));
			}
		}
		
	//Retrieving data for Adwords table
		$addwords = $analytics->data_ga->get('ga:' . $siteId, 'yesterday', 'yesterday','ga:impressions, ga:adClicks, ga:adCost, ga:CPC, ga:CTR, ga:costPerConversion, ga:goalCompletionsAll, ga:costPerGoalConversion, ga:goalConversionRateAll', ['dimensions'=>'ga:adGroup, ga:adKeywordMatchType, ga:adMatchedQuery']);
		foreach ($addwords as $addword){
			$sqla = "INSERT INTO adds (a_date, a_year, a_month, a_week, a_day, a_yearmonth, a_yearweek, a_adgroup, a_adkeywordmatchtype, a_admatchedquery, a_impressions, a_adclicks, a_adcost, a_cpc, a_ctr, a_costperconversion, a_goalcompletionsall, a_costpergoalconversion, a_goalconversionrateall, a_processdate)
					VALUES ('$gdate', '$gyear', '$gmonth', '$gweek', '$gday', '$gyearmonth', '$gyearweek', '$addword[0]', '$addword[1]', '$addword[2]', '$addword[3]', '$addword[4]', '$addword[5]', '$addword[6]', '$addword[7]', '$addword[8]', '$addword[9]', '$addword[10]', '$addword[11]', '$sysdate')";
			if(mysqli_query($conn, $sqla)){
//				echo "Records added successfully.";
			} else{
				$log->lwrite('ERROR: Could not execute $sqla. '. mysqli_error($conn));
			}
		}

	//Retrieving data for Page Tracking table
		$ptracs = $analytics->data_ga->get('ga:' . $siteId, 'yesterday', 'yesterday','ga:pageValue, ga:entrances, ga:entranceRate, ga:pageviews, ga:pageviewsPerSession, ga:uniquePageviews, ga:timeOnPage, ga:avgTimeOnPage, ga:exits, ga:exitRate', ['dimensions'=>'ga:hour, ga:hostname, ga:pagePath, ga:pageTitle, ga:landingPagePath, ga:exitPagePath']);
		foreach ($ptracs as $ptrac){
			$sqlp = "INSERT INTO pagesessions (p_date, p_year, p_month, p_week, p_day, p_hour, p_yearmonth, p_yearweek, p_hostname, p_pagepath, p_pagetitle, p_landingpagepath, p_exitpagepath, p_pagevalue, p_entrances, p_entrancerate, p_pageviews, p_pageviewspersession, p_uniquepageviews, p_timeonpage, p_avgtimeonpage, p_exits, p_exitrates, p_processdate)
					VALUES ('$gdate', '$gyear', '$gmonth', '$gweek', '$gday', '$ptrac[0]', '$gyearmonth', '$gyearweek', '$ptrac[1]', '$ptrac[2]', '$ptrac[3]', '$ptrac[4]', '$ptrac[5]', '$ptrac[6]', '$ptrac[7]', '$ptrac[8]', '$ptrac[9]', '$ptrac[10]', '$ptrac[11]', '$ptrac[12]', '$ptrac[13]', '$ptrac[14]', '$ptrac[15]', '$sysdate')";
			if(mysqli_query($conn, $sqlp)){
//				echo "Records added successfully.";
			} else{
				$log->lwrite('ERROR: Could not execute $sqlp. '. mysqli_error($conn));
			}
		}
*/
	// Close connection and log file
		mysqli_close($conn);
//		$log->lwrite('Export Completed Successfuly on '.$sysdate.' for account: '.$dbname);
//		$log->lwrite('-----------------------------------------');
//		$log->lclose();
	}
}
?>