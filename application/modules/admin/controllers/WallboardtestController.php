<?php
global $fdow,$ldow,$month_start,$month_end, $zendesk_auth;
$datenow = date('Y-m-d');// date now
$mon = new DateTime($datenow);
$fri = new DateTime($datenow);
$mon->modify('monday this week');
$fdow=$mon->format('Y-m-d');
$fri->modify('sunday this week');
$ldow=$fri->format('Y-m-d');
$month_start = strtotime('first day of this month', time());
$month_start_unix = strtotime('first day of this month', time());
$month_start = date('Y-m-d', $month_start);
$month_end = strtotime('last day of this month', time());
$month_end_unix = strtotime('last day of this month', time());
$month_end = date('Y-m-d', $month_end);


class Admin_WallboardtestController extends Teabag_Controller_Action {

	public function urlPing($domain=NULL)
	{
		$curlInit = curl_init($domain);
	    curl_setopt($curlInit,CURLOPT_CONNECTTIMEOUT,10);
	    curl_setopt($curlInit,CURLOPT_HEADER,true);
	    curl_setopt($curlInit,CURLOPT_NOBODY,true);
	    curl_setopt($curlInit,CURLOPT_RETURNTRANSFER,true);

	    //get answer
	    $response = curl_exec($curlInit);

	    curl_close($curlInit);
	    if ($response) return true;
	    return false;
	}
	public function secondsToTime($seconds) {
	    $dtF = new \DateTime('@0');
	    $dtT = new \DateTime("@$seconds");
	    return $dtF->diff($dtT)->format('%a:%h:%i:%s');
	}
	public function apitestAction() {

		$api = $this->getRequest()->getParam('api');

         $this->getHelper('Layout')
         ->disableLayout();

		$this->getHelper('ViewRenderer')
			 ->setNoRender();



		//$gdatat = $googleMapper->GoogleData(date('Y-m-d'),date('Y-m-d'));
		//$gdataw = $googleMapper->GoogleData($fdow,$ldow);
		//$gdataw = $googleMapper->GoogleData($fdow,$ldow);
		//$gdatam = $googleMapper->GoogleData($month_start,$month_end);

		//$this->view->gdatat = $gdatat;
		if($api == 'contact'){




			$jobschedule = new Application_Model_Table_Contact_Jobschedules();
			$jobscheduleitems = new Application_Model_Table_Contact_Jobitems();
			$jobtypes = new Application_Model_Table_Contact_Jobtypes();
			$runningjobs = $jobschedule->fetchAll(array('is_deleted = 0','job_status IN (?)'=>array(1,2,3),'job_created >= ?'=>date('Y-m-d')));

			$runningjobs=array();
			$jobtypes = $jobtypes->fetchAll(array('job_active = 1'));
			foreach ($jobtypes as $type) {

				 $run = $jobschedule->fetchAll(array('job_ended = 0','job_created >= ?'=>date('Y-m-d 00:00:00'),'job_status IN (?)'=>array(1,2),'is_deleted = 0', 'job_type = ?'=>$type->id));
				 $runningjobs['running_jobs'][$type->id]['title'] = $type->job_name;
				 $run = $run->ToArray();
				 foreach ($run as $key => $value) {
					 $time1 = strtotime($value['send_start']);
           $time2 = strtotime($value['send_end']);
           $difference = round(abs($time2 - $time1) / 60,2);
           $sendrate = round(($value['job_items']/$difference)*5,0);
					 $run[$key]['sendrate'] = $sendrate;
					 $run[$key]['next_send_time'] = date('H:i',strtotime($value['next_send_time']));
					 if ($value['job_paused'] == 1) {
					 	$run[$key]['status']='Paused';
					 }
					 elseif ($value['job_ended'] == 1) {
					 	$run[$key]['status']='Ended';
					 }
					 elseif ($value['job_status'] == 1) {
						$run[$key]['status']='Sending';
					 }
					 else{
						$run[$key]['status']='Pending';
					 }
				 }
				 $runningjobs['running_jobs'][$type->id]['data'] = $run;

			}

			$the_que = $jobscheduleitems->get_que();


			$summaries = $jobschedule->fetchsummary();

			$summariesc = $jobscheduleitems->fetchsenttest();

			$runningjobs['que'] = $the_que;
			$runningjobs['summaries'] = $summaries;
			$runningjobs['summariesc'] = $summariesc;

			$sumitem = 0;
      $sumsent = 0;
			$runningjobs['sumsent']=0;
      foreach ($summariesc as $item) {
          $runningjobs['sumsent'] += $item['counted'];
      }


      foreach ($summaries as $item) {
          $sumitem = $sumitem + $item['job_items'];
          $runningjobs['sumsec'][$item['name']] = $runningjobs['sumsec'][$item['name']] + $item['job_items'];
      }
			$runningjobs['sumitem'] = $sumitem;

			$runningjobs['summariesg'] = $jobschedule->fetchsummarytest()->ToArray();

			echo json_encode($runningjobs);
		}
		if($api == "websites_uptime_robot"){
			require_once(LIBRARY_PATH."/UptimeRobot/API.php");
			$config = [
			    'apiKey' => 'ur669465-ad6ca05a279ebeed464a119c',
			    'url' => 'https://api.uptimerobot.com/v2'
			];
		    $api = new UptimeRobot\API($config);
			$out = [];

		    $responsetimes = $api->request('/getMonitors', array('response_times'=>1));
		    $uptime = $api->request('/getMonitors', array('custom_uptime_ratios'=>'7-14-30'));
		    $status = $api->request('/getMonitors');

			foreach ($responsetimes['monitors'] as $key => $value) {
				$out[$value['friendly_name']]['response_times']=$value['response_times'][0]['value'];
			}
			foreach ($uptime['monitors'] as $key => $value) {
				$out[$value['friendly_name']]['uptime_ratio']['last_7']=explode('-',$value['custom_uptime_ratio'])[0];
				$out[$value['friendly_name']]['uptime_ratio']['last_14']=explode('-',$value['custom_uptime_ratio'])[1];
				$out[$value['friendly_name']]['uptime_ratio']['last_30']=explode('-',$value['custom_uptime_ratio'])[2];
			}
			foreach ($status['monitors'] as $key => $value) {
				$out[$value['friendly_name']]['status']=($value['status'] == 9 ? '0':'1');
			}
			echo json_encode($out);

		}
		if ($api == "check_internal_sites") {
			$sitesModal = new Application_Model_Table_Sites();
			$sites = $sitesModal->fetchAll(array('site_location = ?' => 'INTERNAL'));
			$out = [];
			foreach ($sites as $key => $site) {
				$site->site_url = str_replace('https','http', $site->site_url);
				$out[$site->label] = $this->urlPing($site->site_url);
			}
			echo json_encode($out);
		}
		if ($api == "external_mysql_stats") {
			$link   = mysqli_connect('server1.creditresourcesolutions.co.uk', 'bradley_root', 'kyyyq2qr98oJze5V');
			$status = explode('  ', mysqli_stat($link));
			mysqli_close($link);
			$temp = explode(' ',$status[0]);
			$status[0] = $temp[0]." ".$this->secondsToTime($temp[1]);
			echo json_encode($status);
		}
		if ($api == "internal_mysql_stats") {
			$link   = mysqli_connect('192.168.50.5', 'TestAdmin', 'crs431025crs');
			$status = explode('  ', mysqli_stat($link));
			mysqli_close($link);
			//var_dump($status);
			$temp = explode(' ',$status[0]);
			$status[0] = $temp[0]." ".$this->secondsToTime($temp[1]);
			echo json_encode($status);
		}

		if($api == "text_magic_chats"){

			include "TextMagic/textmagic.php";

			$data = getTMChats();

			echo json_encode($data);

		}

		if($api == "text_magic_stats"){

			include "TextMagic/textmagic.php";

			$data = getTMStats();

			echo json_encode($data);

		}

		if($api == "zendesk"){

			// include "Zendesk/zendesk.php";
			//
			// $data = Get_View_Counts("Finance");

			$emailClass = new Application_Model_Table_Myresponse_Emails();
			$out = [];
			$data=$emailClass->getCRSCounts();
			$out['open']=$data;
			$data=$emailClass->getSolved();
			$out['solved']=$data;
			$data=$emailClass->getBotSolved();
			$out['bot']=$data;
			$out['botperc']=number_format(($data['month']/$emailClass->getTotalMonth()*100));

			echo json_encode($out);

		}

		if($api == "business_zendesk"){

			// include "Zendesk/zendesk.php";
			//
			// // The view counts we want are all packed into an array together then sent to the wallboard
			// $data = [Get_View_Counts("Business"), Get_View_Counts("Spark"), Get_View_Counts("Nabuh"), Get_View_Counts("1stCentral"), Get_View_Counts("Bristol")];

			$emailClass = new Application_Model_Table_Myresponse_Emails();
			$data=$emailClass->getWLCounts();


			echo json_encode($data);


		}


		if ($api == "cases_report_day"){

			$array=array();
			$model_caseflow = new Application_Model_Caseflow;
			$date = date('Y-m-d');
			$q = "SELECT client.clcompanynumber, client.cl_name, COUNT(debt.dt_datinstr) AS casesloaded, SUM(debt.dt_debtval) AS balanceLoaded
						FROM   debt INNER JOIN
						       client ON debt.client_code = client.client_code
						WHERE  (debt.dt_datinstr = CAST(GETDATE() AS date))
						GROUP BY client.clcompanynumber, client.cl_name
						ORDER BY client.clcompanynumber, client.cl_name";
			$results = $model_caseflow->caseflow_query($q);

			foreach ($results['rows'] as $value) {
						$out['data'][$value['clcompanynumber']][]=array($value['clcompanynumber'],$value['cl_name'],$value['casesloaded'],$value['balanceLoaded']);


			}

			echo json_encode($out);

		}
		if ($api == "cases_report_day_sector"){
			$casesectormodal = new Application_Model_Table_CasesSectors();
			$foundRows = $casesectormodal->fetchAll(array('date = ?'=>date('Y-m-d')));
			$out = [];
			foreach ($foundRows as $value) {
						$out['data'][$value['clcompanynumber']][]=array($value['clcompanynumber'],$value['casesloaded'],number_format($value['balanceLoaded'], 2));
			}
			echo json_encode($out);
		}


		if ($api=="update_sector") {
			$array=array();
			$model_caseflow = new Application_Model_Caseflow;
			$date = date('Y-m-d');
			$q = "SELECT       client.clcompanynumber, SUM(debt_trans.tx_amount) AS totalcash
						FROM         debt_trans INNER JOIN
                         client ON debt_trans.client_code = client.client_code
					  WHERE        debt_trans.tx_date = '$date' AND (debt_trans.tran_code LIKE 'DR%')
						GROUP BY 		 client.clcompanynumber";
			$results = $model_caseflow->caseflow_query($q);
			foreach ($results['rows'] as $value) {

				if ($value['clcompanynumber']) {
						$out['data'][$value['clcompanynumber']]=substr(str_replace(array(',','"'),'',$value['totalcash']),0,'-3');
				}

			}




			foreach ($out['data'] as $key=>$data) {
				if ($key=="Profiling" || $key=="Claims" || $key=="Eagle") {
						$fique_all = $fique_all + $data;
						$outp['data']['Legal']=$fique_all;
				} else {
						$fique_other = $fique_other + $data;
						$outp['data'][$key]=$data;
				}
			}
			$out['data']['Legal']=$fique_all;
			arsort($outp['data']);
			foreach ($outp['data'] as $lkey=>$data){
				$outp['labels'][]=$lkey;
			}

			$json['labels'] = $outp['labels'];
			$json['updated_time'] = "last updated ".date('H:i:s');
			$json['data'] = array_values($outp['data']);

			//print_r($json);
			//echo "<br>";
			/*echo '<pre>';
				print_r($outp['data']);
			echo '</pre>';*/

			echo json_encode($json);

		}

		if ($api == "get_accounts"){
			$model_caseflow = new Application_Model_Caseflow;
			$date = date('Y-m-01');
			$q = "SELECT     count(dt_datinstr) as casesloaded
						FROM       debt
						WHERE      dt_datinstr >= '$date'";
			$result = $model_caseflow->caseflow_query($q);


			echo $result['rows'][0]['casesloaded'];

		}

		if ($api == "get_cash"){
			// $model_caseflow = new Application_Model_Caseflow;
			// $q = "SELECT        sum(tl_netamt) as totalcash
			// 			FROM          tranlist
			// 			WHERE         (tran_code LIKE 'DR%')";
			// $result = $model_caseflow->caseflow_query($q);
			$cashrevmodal = new Application_Model_Table_TotalCashRevs();
			$cash = $cashrevmodal->fetchRow(array('id = 1'));

			// if ($_SERVER['REMOTE_ADDR']=="192.168.50.115") {
			// 	echo round(32000,0);
			// } else {
			  echo round($cash->totalcash,0);
			// }


		}
		if ($api == "get_revenue"){
			// $model_caseflow = new Application_Model_Caseflow;
			// $q = "SELECT
			// 			SUM((tranlist.tlcosts) + ((CASE WHEN dbo.tranlist.tran_code LIKE 'CC5000%' THEN tranlist.tl_netamt ELSE 0 END)) + (tranlist.tlfees)) as totalrevenue
			//
			// 		FROM     dbo.tranlist
			// 		WHERE (dbo.tranlist.tran_code LIKE 'DR%' OR (dbo.tranlist.tran_code LIKE 'CC5000%'))";
			//
			// $result = $model_caseflow->caseflow_query($q);
			$cashrevmodal = new Application_Model_Table_TotalCashRevs();
			$cash = $cashrevmodal->fetchRow(array('id = 1'));

			echo round($cash->totalrevenue,0);

			// echo round($result['rows'][0]['totalrevenue'],0);

		}

		if ($api == "update_sector_agent_new"){
			$CaseflowModal = new Application_Model_Caseflow();
			$data = $CaseflowModal->collector_cash_today();
			foreach ($data as $key => $value) {
				$out[$value['op_code']]=$value['totalcash'];
			}
		echo '<pre>';
			print_r($out);
		echo '</pre>';
		}

		if ($api == "get_cash_month"){
			// $model_caseflow = new Application_Model_Caseflow;
			// $date = date('Y-m-01');
			// $q = "SELECT     sum(tx_amount) as totalcashmonth
			// 			FROM       debt_trans
			// 			WHERE      tx_date >= '$date' AND (tran_code LIKE 'DR%')";
			// $result = $model_caseflow->caseflow_query($q);
			//
			//
			// echo round($result['rows'][0]['totalcashmonth'],0);
			$cashrevmodal = new Application_Model_Table_TotalCashRevs();
			$cash = $cashrevmodal->fetchRow(array('id = 1'));

			//echo round($cash->totalcashmonth,0);

			//if ($_SERVER['REMOTE_ADDR']=="192.168.50.115") {
			//	echo round(580585,0);
			//} else {
			 echo round($cash->totalcashmonth,0);
			//}

		}
		if ($api == "get_business_sector"){
				$buisnessmodal = new Application_Model_Table_BusinessSectors();
				$fetch = $buisnessmodal->fetchAll(array('date = ?'=>date('Y-m-d')));
				if($fetch){
					$fetch = $fetch->ToArray();
					foreach ($fetch as $key => $sector) {
						$fetch[$key]['counted'] = number_format($sector['counted']);
						$fetch[$key]['Revenue'] = number_format($sector['Revenue']);
					}
				}
			echo json_encode($fetch,0);
		}
		if ($api == "get_revenue_month"){
			// $model_caseflow = new Application_Model_Caseflow;
			// $date = date('Y-m-01');
			// $q = "SELECT
			// 			SUM((debt_trans.tx_costs) + ((CASE WHEN debt_trans.tran_code LIKE 'CC5000%' THEN debt_trans.tx_amount ELSE 0 END)) + (debt_trans.tx_fees)) as totalrevenue
			// 		FROM     dbo.debt_trans INNER JOIN
			// 			dbo.debt ON dbo.debt_trans.debt_code = dbo.debt.debt_code INNER JOIN
			// 			dbo.client ON dbo.debt.client_code = dbo.client.client_code
			// 		WHERE tx_date >= '$date'
			// 			AND (dbo.debt_trans.tran_code LIKE 'DR%' OR (dbo.debt_trans.tran_code LIKE 'CC5000%'))";
			// $result = $model_caseflow->caseflow_query($q);
			//
			//
			// echo round($result['rows'][0]['totalrevenue'],0);
			$cashrevmodal = new Application_Model_Table_TotalCashRevs();
			$cash = $cashrevmodal->fetchRow(array('id = 1'));

			echo round($cash->totalrevenuemonth,0);

		}

		if ($api=="update_agent") {
			//$local="C:\xampp\htdocs\sites\wallboards\";
			$file="..\data\data_live\S -Cash Collected - Agent.csv";
			//$file="c:/xampp/htdocs/sites/wallboards/data/data_live/S -Cash Collected - Agent.csv";
			$csv= file_get_contents($file);
			$csv= str_replace(array('£'),'',$csv);
			$array = array_map("str_getcsv", explode("\n", $csv));
			foreach ($array as $key=>$arr) {
				if ($arr[0]) {
						$out['data'][$arr[0]]=substr(str_replace(array(',','"'),'',$arr[1].$arr[2]),0,'-3');
				}
			}


			foreach ($out['data'] as $key=>$data) {
				if ($key=="Profiling" || $key=="Claims" || $key=="Eagle") {
						$fique_all = $fique_all + $data;
						$outp['data']['Legal']=$fique_all;
				} else {
						$fique_other = $fique_other + $data;
						$outp['data'][$key]=$data;
				}
			}
			$out['data']['Legal']=$fique_all;
			arsort($outp['data']);
			foreach ($outp['data'] as $lkey=>$data){
				$outp['labels'][]=$lkey;
			}

			$json['labels'] = $outp['labels'];
			$json['updated_time'] = "last updated ".date('H:i:s');
			$json['data'] = array_values($outp['data']);

			//print_r($json);
			//echo "<br>";
			echo json_encode($json);

		}







		if($api == "chart_f_g_topcharts") {
			$model_caseflow = new Application_Model_Caseflow;


			$model_phasing = new Application_Model_Table_Phasings;
			$phasing = $model_phasing->fetchRow(array('date = ?'=>date('m-d')))->perc;

			$forecast_model_phasing = new Application_Model_Table_ForecastPhasings;
			$forecast_phasing = $forecast_model_phasing->fetchRow(array('day = ?'=>date('d'), 'client_sector = ?'=>0));
			$forecast_phasing = round($phasing + $forecast_phasing->perc_cash_forecast, 2);

			$from = date('Y-m-01');
			$to = date('Y-m-d');
			$sortbycolumn = 'perc_rev';
			$sortbycolumnF = 'target';
			$sortbycolumnG = 'cash';
			$month = date("Y-m");

			$StoreCttModal = new Application_Model_Table_Storedctts();
			$data = $StoreCttModal->collector_cash($sortbycolumn,'DESC',date('Y-m'));
			$dataF = $StoreCttModal->collector_cash($sortbycolumnF,'DESC',date('Y-m'));
			$data_topcash = $StoreCttModal->collector_cash($sortbycolumnG,'DESC',date('Y-m'));

			// $data = $model_caseflow->collector_cash($from,$to,$sortbycolumn,1,$month);
			// $dataF = $model_caseflow->collector_cash($from,$to,$sortbycolumnF,1,$month);
			// $data_topcash = $model_caseflow->collector_cash($from,$to,$sortbycolumnG,1,$month);
			$count = 0;



			$names=array();
			$line=array();
			$bar=array();
			foreach ($dataF as $key => $value) {
				$names[]=$value['op_code'];
				$line[]=$value['target'];
				$forecastF[]=$value['forecast_cash_f'];
				$bar[]=$value['revenue'];
				$yesterday[]=0;
			}
			foreach ($data as $key => $value) {
				$namesG[]=$value['op_code'];
				$lineG[]=$phasing;
				$forecastLineG[] = $forecast_phasing;
				$forecastG[]=$value['forecast_cash'];
				$barG[]=$value['perc_rev'];
			}
			foreach ($data_topcash as $key => $value) {
				$namesTC[]=$value['op_code'];
			}

			/*
			foreach ($acsv as $agent){ if ($agent[0]=="AUTO" || $agent[0]=="") { } else {
				$names['perc_target'][]=$agent[0];
				$bar['perc_target'][]= strval(round(floatval($agent[9]) * 100, 0));
			}}
			foreach ($bcsv as $agent){ if ($agent[0]=="AUTO" || $agent[0]=="") { } else {
				$names['pound_collected'][]=$agent[0];
				$bar['pound_collected'][]= $agent[7];
			}}*/

			/*echo '<pre>';
				print_r($data);
				//print_r($line);
				//print_r($bar);
			echo '</pre>';*/

			$final_array['chart_f']['names']=$names;
			$final_array['chart_f']['line']=$line;
			$final_array['chart_f']['forecast_cash_f']=$forecastF;

			$final_array['chart_f']['bar']=$bar;
			$final_array['chart_f']['yesterday']=$yesterday;


			$final_array['chart_g']['names']=$namesG;
			$final_array['chart_g']['line']=$lineG;
			$final_array['chart_g']['forecast']=$forecastG;
			$final_array['chart_g']['forecast_phasing']=$forecastLineG;
			$final_array['chart_g']['bar']=$barG;


			$final_array['chart_topcash']['names']=$namesTC;






			echo json_encode($final_array);


		}


		if($api=="chats_live"){

			$token=get_token_data()['access_token'];

			$data = Get_Recent_Chats();


			echo json_encode($data);

		}

		if ($api=="test_test") {

			$token=get_token_data()['access_token'];

			$date_month= date('Y-m-').'01'; 			// this variable holds the date at the start of the month
			$date_today= date('Y-m-d');				// this holds today's date
			$date_week = get_date_monday();

			// using the dates we retrieve the data we need and then put it into an array
			$chats_this_today = Get_Total_Chats($date_today,$date_today);
			$chats_this_week = Get_Total_Chats($date_today,$date_week);
			$chats_this_month = Get_Total_Chats($date_today,$date_month);

			$avrg_RT_Day = Get_AvrgR_Time($date_today, $date_today);
			$avrg_RT_Week = Get_AvrgR_Time($date_today, $date_week);
			$avrg_RT_Month = Get_AvrgR_Time($date_today,$date_month);

			$live_users = Get_Live_Users();

			$data['chats_today']=($chats_this_today->total);
			$data['chats_week']=($chats_this_week->total);
			$data['chats_month']=($chats_this_month->total);

			$data['avrg_RT_day']=$avrg_RT_Day."s";
			$data['avrg_RT_week']=$avrg_RT_Week."s";
			$data['avrg_RT_month']=$avrg_RT_Month."s";
			$data['live_users'] = $live_users;

			// This just stops a glitch where an average response time would appear before there where any chats registered
			if ($data['chats_today'] == 0){
				$data['avrg_RT_day'] = "0s";
			}
			if ($data['chats_week'] == 0){
				$data['avrg_RT_week'] = "0s";
			}
			if ($data['chats_month'] == 0){
				$data['avrg_RT_month'] = "0s";
			}

			echo json_encode($data);

		}

		if ($api=="email_stats") {

			global $fdow,$ldow;
			$emailautoMapper = new Application_Model_Emailauto();

			// get all of the tickets for each time period, including what the tickets were made and solved by
			$day_tickets = $emailautoMapper->fetchAllTickets((date('Y-m-d').' 00:00:00'),(date('Y-m-d').' 23:59:59'));
			$week_tickets = $emailautoMapper->fetchAllTickets((get_date_monday().' 00:00:00'),(date('Y-m-d').' 23:59:59'));
			$month_tickets = $emailautoMapper->fetchAllTickets((date('Y-m-01').' 00:00:00'),(date('Y-m-d').' 23:59:59'));

			// get all of the comments (touches) for each time period
			$day_comments = $emailautoMapper->fetchAllComments((date('Y-m-d').' 00:00:00'),(date('Y-m-d').' 23:59:59'));
			$week_comments = $emailautoMapper->fetchAllComments((get_date_monday().' 00:00:00'),(date('Y-m-d').' 23:59:59'));
			$month_comments = $emailautoMapper->fetchAllComments((date('Y-m-01').' 00:00:00'),(date('Y-m-d').' 23:59:59'));

			// put all of the arrays into $data array so that they can all be sent over to the wallboard
			$data['day_tickets']=$day_tickets;
			$data['week_tickets']=$week_tickets;
			$data['month_tickets']=$month_tickets;
			$data['day_comments']=$day_comments;
			$data['week_comments']=$week_comments;
			$data['month_comments']=$month_comments;

			echo json_encode($data);
		}

		if ($api=="update_gdata") {

			$datenow = date('Y-m-d');// date now
			$mon = new DateTime($datenow);
			$fri = new DateTime($datenow);
			$mon->modify('monday this week');
			$fdow=$mon->format('Y-m-d');
			$fri->modify('sunday this week');
			$ldow=$fri->format('Y-m-d');
			$month_start = strtotime('first day of this month', time());
			$month_start = date('Y-m-d', $month_start);
			$month_end = strtotime('last day of this month', time());
			$month_end = date('Y-m-d', $month_end);


			$googleMapper = new Application_Model_Table_GoogleT();
			$gdatat = $googleMapper->GoogleData(date('Y-m-d'),date('Y-m-d'));
			$gdataw = $googleMapper->GoogleData($fdow,$ldow);
			$gdatam = $googleMapper->GoogleData($month_start,$month_end);

			$out['today']['users']=$gdatat[0]['users'];
			$out['today']['newusers']=$gdatat[0]['newusers'];
			$out['today']['sessions']=$gdatat[0]['sessions'];
			$out['today']['organicSearches']=$gdatat[0]['organicSearches'];
			$out['today_data']=array($gdatat[0]['users'],$gdatat[0]['newusers'],$gdatat[0]['organicSearches']);

			$out['week']['users']=$gdataw[0]['users'];
			$out['week']['newusers']=$gdataw[0]['newusers'];
			$out['week']['sessions']=$gdataw[0]['sessions'];
			$out['week']['organicSearches']=$gdataw[0]['organicSearches'];
			$out['week_data']=array($gdataw[0]['users'],$gdataw[0]['newusers'],$gdataw[0]['organicSearches']);

			$out['month']['users']=$gdatam[0]['users'];
			$out['month']['newusers']=$gdatam[0]['newusers'];
			$out['month']['sessions']=$gdatam[0]['sessions'];
			$out['month']['organicSearches']=$gdatam[0]['organicSearches'];
			$out['month_data']=array($gdatam[0]['users'],$gdatam[0]['newusers'],$gdatam[0]['organicSearches']);






			$paycrsMapper = new Application_Model_Paycrs();

			$arrangement = $paycrsMapper->fetchAllPayArrangements(date('Y-m-d'),date('Y-m-d'));

			$arrangedatat = $paycrsMapper->fetchAllPayArrangements(date('Y-m-d'),date('Y-m-d'));
			$todayarr=$arrangedatat[0]['arrangementTotalCard']+$arrangedatat[0]['arrangementTotalDirectDebit'];

			$arrangedataw = $paycrsMapper->fetchAllPayArrangements($fdow,$ldow);
			$weekarr=$arrangedataw[0]['arrangementTotalCard']+$arrangedataw[0]['arrangementTotalDirectDebit'];

			$arrangedatam = $paycrsMapper->fetchAllPayArrangements($month_start,$month_end);
			$montharr=$arrangedatam[0]['arrangementTotalCard']+$arrangedatam[0]['arrangementTotalDirectDebit'];

			$out['arrangement']['today']=$todayarr;
			$out['arrangement']['week']=$weekarr;
			$out['arrangement']['month']=$montharr;




			$paycrsMapper = new Application_Model_Paycrs();
			$paydatat = $paycrsMapper->fetchAllPay(date('Y-m-d'),date('Y-m-d'));


			$todayparr=$paydatat[0]['totalValue'];
			$paydataw = $paycrsMapper->fetchAllPay($fdow,$ldow);
			$weekparr=$paydataw[0]['totalValue'];
			$paydatam = $paycrsMapper->fetchAllPay($month_start,$month_end);
			$monthparr=$paydatam[0]['totalValue'];

			$out['cashcollected']['today']="£".number_format($todayparr,0);
			$out['cashcollected']['week']="£".number_format($weekparr,0);
			$out['cashcollected']['month']="£".number_format($monthparr,0);

			$json_data = json_encode($out);
			echo $json_data;

		}


		if ($api=="update_glive") {

		$datenow = date('Y-m-d');// date now
		$mon = new DateTime($datenow);
		$fri = new DateTime($datenow);
		$mon->modify('monday this week');
		$fdow=$mon->format('Y-m-d');
		$fri->modify('sunday this week');
		$ldow=$fri->format('Y-m-d');
		$month_start = strtotime('first day of this month', time());
		$month_start = date('Y-m-d', $month_start);
		$month_end = strtotime('last day of this month', time());
		$month_end = date('Y-m-d', $month_end);

		require_once '..\vendor\autoload.php';
		require_once '..\vendor\google\functions.php';

			$profile = 'ga:81202652';
			$KEY_FILE_LOCATION = '..\My Project-05e774380241.json';
			$client = new Google_Client();
			$client->setApplicationName("Hello Analytics Reporting");
			$client->setAuthConfig($KEY_FILE_LOCATION);
			$client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
			$analytics2 = new Google_Service_Analytics($client);
			$profile = getFirstProfileId($analytics2);
			$response = getResults($analytics2,$profile);



			foreach ($response->rows as $row) {
				$total=$total+$row[1];
				//echo $row[0]."-".$row[1];
			}
			echo $total;

		}
		if ($api=="update_paycrsmoney") {

			$datenow = date('Y-m-d');// date now
			$mon = new DateTime($datenow);
			$fri = new DateTime($datenow);
			$mon->modify('monday this week');
			$fdow=$mon->format('Y-m-d');
			$fri->modify('sunday this week');
			$ldow=$fri->format('Y-m-d');
			$month_start = strtotime('first day of this month', time());
			$month_start = date('Y-m-d', $month_start);
			$month_end = strtotime('last day of this month', time());
			$month_end = date('Y-m-d', $month_end);

			$paycrsMapper = new Application_Model_Paycrs();
			$paydatat = $paycrsMapper->fetchAllPay(date('Y-m-d'),date('Y-m-d'));


			$todayparr=$paydatat[0]['totalValue'];

			$paydataw = $paycrsMapper->fetchAllPay($fdow,$ldow);
			$weekparr=$paydataw[0]['totalValue'];

			$paydatam = $paycrsMapper->fetchAllPay($month_start,$month_end);
			$monthparr=$paydatam[0]['totalValue'];

			echo "<div class='col-md-4'>
			<p>Today </p><h3>£".number_format($todayparr,2)."</h3> <br> (".$paydatat[0]['totalTransactions']." Transactions) </div>";

			echo "<div class='col-md-4'>
			<p>This Week </p><h3>£".number_format($weekparr,2)."</h3> <br> (".$paydataw[0]['totalTransactions']." Transactions) </div>";

			echo "<div class='col-md-4'>
			<p>This Month</p><h3>£".number_format($monthparr,2)."</h3>  <br> (".$paydatam[0]['totalTransactions']." Transactions)</div>";

		}



		if ($api=="update_test") {

			require_once '../library/Dialer/Dialer.php';
			$startofday=strtotime(date('Y-m-d')." 00:00:01");
			$endofday=strtotime(date('Y-m-d')." 23:59:59");

			$options['fields']='';
			//$options['groupby']='';
			//$options['groupby']='';
			$options['range']=$startofday.":".$endofday;
			//$agents = api_reporting('calls', $options);
			$agents = api_db('cdr_log', 'read', $options);
			//echo "<pre>";
			//print_r($agents);
			//echo "</pre>";
			/*
			 [callid] => 14609381803
			[qid] => 541658
			[dataset] => 0
			[urn] => 0
			[agent] => 937803
			[ddi] => 07905680131
			[cli] => 01422324510
			[ringtime] => 19.3317
			[duration] => 3.68808
			[result] => TPT
			[outcome] => 505963
			[type] => out
			[datetime] => 1531813587
			[answer] => 2018-07-17 08:46:48
			[disconnect] => 2018-07-17 08:46:51
			[last_update] => 2018-07-17 08:49:40
			[carrier] => bt-ix
			[flags] => recorded,processed
			[terminate] => dxi|bye
			[customer] => 0
			[customer_cost] => 0
			*/
			$i=0;
			$ij=0;
			foreach ($agents['list'] as $data) {
				if ($data['ringtime']>=45 ) { $ij++; }$i++; $out['data'][]=$data;
			}
			$out['total']=$i;
			$out['total_aban']=$ij;
			if ($ij) { $tim=$ij/$i*100; } else { $tim=0; }
			$out['total_aban_perc']=$tim;

			echo "<pre>";
			print_r($out);
			echo "</pre>";
		}

		if ($api=="update_testa") {

			require_once '../library/Dialer/Dialer.php';
			$incoming = api_ajax("incoming");
			echo "<pre>";
			print_r($incoming);
			echo "</pre>";

		}



	}



	private function screen_1() {

		global $fdow,$ldow,$month_start,$month_end;


	}

	private function screen_2() {

		global $fdow,$ldow,$month_start,$month_end;


	}

	private function screen_3() {

		global $fdow,$ldow,$month_start,$month_end;


	}

	private function screen_5() {

		global $fdow,$ldow,$month_start,$month_end;


	}

	private function screen_8() {

		global $fdow,$ldow,$month_start,$month_end;


	}

	private function screen_10() {

		global $fdow,$ldow,$month_start,$month_end;


	}


}
