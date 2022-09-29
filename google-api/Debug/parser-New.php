<?php
//Created on July 2019 by Andrei Chirila (cjrocha.webs@gmail.com)
//This code is a crawler of Google Analytics accounts
//Data retrieved is being inserted into MySql DB using DB connection
//and conform to client requirements


// Load the Google API PHP Client Library and Log reporting.
require_once __DIR__ . '/google-api/vendor/autoload.php';
require_once __DIR__ . '/google-api/Logging.php';

$gdate = date("Y-m-d");  //System Date required in Tables

//DB Credentials
$servername = "";
$username = "";
$password = "";

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
	//Connecting to DB
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
		$log->lwrite('Connection failed: '.$conn->connect_error);
		die("Connection failed: " . $conn->connect_error);
	} else {

	//Retrieving data for Users table
		$users = $analytics->data_ga->get('ga:' . $siteId, 'yesterday', 'yesterday','ga:users, ga:newUsers, ga:percentNewSessions, ga:sessionsPerUser', ['dimensions'=>'ga:year, ga:month, ga:week, ga:day, ga:hour, ga:yearMonth, ga:yearWeek']);
//		$u=0;
		foreach ($users as $user){
			$gyear = $user[0];
			$gmonth = $user[1];
			$gweek = $user[2];
			$gday = $user[3];
			$gyearmonth = $user[5];
			$gyearweek = $user[6];
			$sqlu = "INSERT INTO users (u_date, u_year, u_month, u_week, u_day, u_hour, u_yearmonth, u_yearweek, u_users, u_newusers, u_percentnewsessions, u_sessionperuser)
				VALUES ('$gdate', '$user[0]', '$user[1]', '$user[2]', '$user[3]', '$user[4]', '$user[4]', '$user[6]', '$user[7]', '$user[8]', '$user[9]', '$user[10]')";
			
//			$udata[$u] = [
//			'Date: ' => $gdate,
//			'Year: ' => $user[0],
//			'Month: '=> $user[1],
//			'Week: ' => $user[2],
//			'Day: ' => $user[3],
//			'Hour: '=> $user[4],
//			'yearMonth' => $user[5],
//			'yearWeek'=> $user[6],
//			'Users'=> $user[7],
//			'NewUsers'=> $user[8],
//			'PercentNewSessions'=> $user[9],
//			'SessionPerUser'=>$user[10]
//		];
//		Handle($udata[$u]);
//		$u++;
//		#echo '<pre>';
//		#print_r($udata);
//	}
			if(mysqli_query($conn, $sqlu)){
//				echo "Records added successfully.";
			} else{
				$log->lwrite('ERROR: Could not execute $sqlu.  '. mysqli_error($conn));
//				echo "ERROR: Could not able to execute $sqlu. " . mysqli_error($conn);
			}
		
		}
		
	//Retrievinng data for Sources table
		$sources = $analytics->data_ga->get('ga:' . $siteId, 'yesterday', 'yesterday','ga:sessions, ga:bounces, ga:bounceRate, ga:sessionDuration, ga:avgSessionDuration, ga:uniqueDimensionCombinations', ['dimensions'=>'ga:hour']);
//		$s=0;
		foreach ($sources as $source){
			$sqls = "INSERT INTO session (s_date, s_year, s_month, s_week, s_day, s_hour, s_yearmonth, s_yearweek, s_sessions, s_bounces, s_bouncerate, s_sessionduration, s_avgsessionduration, s_uniquedimensioncombinations)
				VALUES ('$gdate', '$gyear', '$gmonth', '$gweek', '$gday', '$source[0]', '$gyearmonth', '$gyearweek', '$source[1]', '$source[2]', '$source[3]', '$source[4]', '$source[5]', '$source[6]')";
//			$sdata[$s] = [
//				'Date: ' => $gdate,
//				'Year: ' => $gyear,
//				'Month: '=> $gmonth,
//				'Week: ' => $gweek,
//				'Day: ' => $gday,
//				'Hour: '=> $source[0],
//				'yearMonth' => $gyearmonth,
//				'yearWeek'=> $gyearweek,
//				'Sessions'=> $source[1],
//				'Bounces'=> $source[2],
//				'BounceRate'=> $source[3],
//				'SessionDuration'=> $source[4],
//				'AvgSessionDuration'=> $source[5],
//				'UniqueDimensionCombinations'=> $source[6],
//			];
//		Handle($sdata[$s]);
//		$s++;
		#echo '<pre>';
		#print_r($sdata);
//	}
			if(mysqli_query($conn, $sqls)){
//				echo "Records added successfully.";
			} else{
				$log->lwrite('ERROR: Could not execute $sqls. ' . mysqli_error($conn));
//				echo "ERROR: Could not able to execute $sqls. " . mysqli_error($conn);
			}
		}
		
	//Retrieving data for Traffic Sources table
		$tsources = $analytics->data_ga->get('ga:' . $siteId, 'yesterday', 'yesterday','ga:organicSearches', ['dimensions'=>'ga:hour, ga:campaign, ga:source, ga:medium, ga:sourceMedium, ga:keyword']);
//		$ts=0;
		foreach ($tsources as $tsource){	
				$sqlso = "INSERT INTO source (so_date, so_year, so_month, so_week, so_day, so_hour, so_yearmonth, so_yearweek, so_campaign, so_source, so_medium, so_sourcemedium, so_keyword)
					VALUES ('$gdate', '$gyear', '$gmonth', '$gweek', '$gday', '$tsource[0]', '$gyearmonth', '$gyearweek', '$tsource[1]', '$tsource[2]', '$tsource[3]', '$tsource[4]', '$tsource[5]')";
//			$tsdata[$ts] = [
//				'Date: ' => $gdate,
//				'Year: ' => $gyear,
//				'Month: '=> $gmonth,
//				'Week: ' => $gweek,
//				'Day: ' => $gday,
//				'Hour: '=> $tsource[0],
//				'yearMonth' => $gyearmonth,
//				'yearWeek'=> $gyearweek,
//				'campaign'=> $tsource[1],
//				'Source'=> $tsource[2],
//				'medium'=> $tsource[3],
//				'SourceMedium'=> $tsource[4],
//				'Keyword'=> $tsource[5]
//		//		'organicSearches'=> $tsource[6]
//		];
//		Handle($tsdata[$ts]);
//		$ts++;
//		#echo '<pre>';
//		#print_r($tsdata);
//	}
			if(mysqli_query($conn, $sqlso)){
//				echo "Records added successfully.";
			} else{
				$log->lwrite('ERROR: Could not execute $sqlso. '. mysqli_error($conn));
//				echo "ERROR: Could not able to execute $sqlso. " . mysqli_error($conn);
			}
		}
		
	//Retrieving data for Adwords table
		$addwords = $analytics->data_ga->get('ga:' . $siteId, 'yesterday', 'yesterday','ga:impressions, ga:adClicks, ga:adCost, ga:CPC, ga:CTR, ga:costPerGoalConversion', ['dimensions'=>'ga:hour, ga:adGroup']);
//		$aw=0;
		foreach ($addwords as $addword){
			$sqla = "INSERT INTO adds (a_date, a_year, a_month, a_week, a_day, a_hour, a_yearmonth, a_yearweek, a_addgroup, a_impressions, a_addclicks, a_addcost, a_cpc, a_ctr, a_costpergoalconversion)
					VALUES ('$gdate', '$gyear', '$gmonth', '$gweek', '$gday', '$addword[0]', '$gyearmonth', '$gyearweek', '$addword[1]', '$addword[2]', '$addword[3]', '$addword[4]', '$addword[5]', '$addword[6]', '$addword[7]')";
//			$adddata[$aw] = [
//				'Date: ' => $gdate,
//				'Year: ' => $gyear,
//				'Month: '=> $gmonth,
//				'Week: ' => $gweek,
//				'Day: ' => $gday,
//				'Hour: '=> $addword[0],
//				'yearMonth' => $gyearmonth,
//				'yearWeek'=> $gyearweek,
//				'AdGroup'=> $addword[1],
//				'Impressions'=> $addword[2],
//				'AdClicks'=> $addword[3],
//				'AdCost'=> $addword[4],
//				'CPC'=>$addword[5],
//				'CTR'=>$addword[6],
//				'CostPerGoalConversion'=>$addword[7],
//			];
//			Handle($adddata[$aw]);
//			$aw++;
//			#echo '<pre>';
//			#print_r($adddata);
//		}
			if(mysqli_query($conn, $sqla)){
//				echo "Records added successfully.";
			} else{
				$log->lwrite('ERROR: Could not execute $sqla. '. mysqli_error($conn));
//				echo "ERROR: Could not able to execute $sqla. " . mysqli_error($conn);
			}
		}
		
	//Retrieving data for Devices table
		$devices = $analytics->data_ga->get('ga:' . $siteId, 'yesterday', 'yesterday','ga:sessions', ['dimensions'=>'ga:hour, ga:deviceCategory']);
//		$dv=0;
		foreach ($devices as $device){
			$sqld = "INSERT INTO device (d_date, d_year, d_month, d_week, d_day, d_hour, d_yearmonth, d_yearweek, d_devicecategory)
					VALUES ('$gdate', '$gyear', '$gmonth', '$gweek', '$gday', '$device[0]', '$gyearmonth', '$gyearweek', '$device[1]')";
//			$devicedata[$dv] = [
//				'Date: ' => $gdate,
//				'Year: ' => $gyear,
//				'Month: '=> $gmonth,
//				'Week: ' => $gweek,
//				'Day: ' => $gday,
//				'Hour: '=> $device[0],
//				'yearMonth' => $gyearmonth,
//				'yearWeek'=> $gyearweek,
//				'deviceCategory'=> $device[1]
//		//		'Sessions' => $device[2]
//			];
//			Handle($devicedata[$dv]);
//			$dv++;
//			#echo '<pre>';
//			#print_r($devicedata);
//		}
			if(mysqli_query($conn, $sqld)){
//				echo "Records added successfully.";
			} else{
				$log->lwrite('ERROR: Could not able to execute $sqld. '. mysqli_error($conn));
//				echo "ERROR: Could not able to execute $sqld. " . mysqli_error($conn);
			}
		}
		
	//Retrieving data for Social table
		$socials = $analytics->data_ga->get('ga:' . $siteId, 'yesterday', 'yesterday','ga:sessions', ['dimensions'=>'ga:hour, ga:deviceCategory']);
//		$so=0;
		foreach ($socials as $social){
			$sqln = "INSERT INTO social (n_date, n_year, n_month, n_week, n_day, n_hour, n_yearmonth, n_yearweek, n_socialinteractionnetwork)
					VALUES ('$gdate', '$gyear', '$gmonth', '$gweek', '$gday', '$social[0]', '$gyearmonth', '$gyearweek', '$social[1]')";
//			$socdata[$so] = [
//				'Date: ' => $gdate,
//				'Year: ' => $gyear,
//				'Month: '=> $gmonth,
//				'Week: ' => $gweek,
//				'Day: ' => $gday,
//				'Hour: '=> $social[0],
//				'yearMonth' => $gyearmonth,
//				'yearWeek'=> $gyearweek,
//				'socialInteractionNetwork'=> $social[1]
//		//		'Sessions' => $social[2]
//			];
//			Handle($socdata[$so]);
//			$so++;
//			#echo '<pre>';
//			#print_r($socdata);
//		}
			if(mysqli_query($conn, $sqln)){
//				echo "Records added successfully.";
			} else{
				$log->lwrite('ERROR: Could not execute $sqln. '. mysqli_error($conn));
//				echo "ERROR: Could not able to execute $sqln. " . mysqli_error($conn);
			}
		}

	//Retrieving data for Page Tracking table
		$ptracs = $analytics->data_ga->get('ga:' . $siteId, 'yesterday', 'yesterday','ga:pageValue, ga:entrances, ga:entranceRate, ga:pageviews, ga:pageviewsPerSession, ga:uniquePageviews, ga:timeOnPage, ga:avgTimeOnPage, ga:exits, ga:exitRate', ['dimensions'=>'ga:hour']);
//		$pt=0;
		foreach ($ptracs as $ptrac){
			$sqlp = "INSERT INTO pagesessions (p_date, p_year, p_month, p_week, p_day, p_hour, p_yearmonth, p_yearweek, p_pagevalue, p_entrances, p_entrancerate, p_pageviews, p_pageviewspersession, p_uniquepageviews, p_timeonpage, p_avgtimeonpage, p_exits, p_exitrates)
					VALUES ('$gdate', '$gyear', '$gmonth', '$gweek', '$gday', '$ptrac[0]', '$gyearmonth', '$gyearweek', '$ptrac[1]', '$ptrac[2]', '$ptrac[3]', '$ptrac[4]', '$ptrac[5]', '$ptrac[6]', '$ptrac[7]', '$ptrac[8]', '$ptrac[9]', '$ptrac[10]')";
//			$ptdata[$pt] = [
//				'Date: ' => $gdate,
//				'Year: ' => $gyear,
//				'Month: '=> $gmonth,
//				'Week: ' => $gweek,
//				'Day: ' => $gday,
//				'Hour: '=> $ptrac[0],
//				'yearMonth' => $gyearmonth,
//				'yearWeek'=> $gyearweek,
//				'pageValue'=> $ptrac[1],
//				'entrances'=> $ptrac[2],
//				'entranceRate'=> $ptrac[3],
//				'pageviews'=> $ptrac[4],
//				'pageviewsPerSession'=> $ptrac[5],
//				'uniquePageviews'=> $ptrac[6],
//				'timeOnPage'=> $ptrac[7],
//				'avgTimeOnPage'=> $ptrac[8],
//				'exits'=> $ptrac[9],
//				'exitRate'=> $ptrac[10],
//			];
//			Handle($ptdata[$pt]);
//			$pt++;
//			#echo '<pre>';
//			#print_r($ptdata);
//		}
			if(mysqli_query($conn, $sqlp)){
//				echo "Records added successfully.";
			} else{
				$log->lwrite('ERROR: Could not execute $sqlp. '. mysqli_error($conn));
//				echo "ERROR: Could not able to execute $sqlp. " . mysqli_error($conn);
			}
		}
	// Close connection
		mysqli_close($conn);
		$log->lwrite('Export Completed Successfuly on '.$gdate);
		$log->lwrite('-----------------------------------------');
		$log->lclose();
	}
}
/*//Retrieving data for Goal Conversion table
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
}
*/
?>