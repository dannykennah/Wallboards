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

			include "Zendesk/zendesk.php";

			$data = Get_View_Counts("Finance");

			echo json_encode($data);

		}

		if($api == "business_zendesk"){

			include "Zendesk/zendesk.php";

			// The view counts we want are all packed into an array together then sent to the wallboard
			$data = [Get_View_Counts("Business"), Get_View_Counts("Spark"), Get_View_Counts("Nabuh"), Get_View_Counts("laRedoute"), Get_View_Counts("1stCentral")];

			echo json_encode($data);


		}

		if ($api == "cases_report_day"){

			// This is the filepath for the business wallboard displays CSV file
			$file="..\data\data_live\S- Daily Cases loaded-Wallboard.csv";

			/*
				Get the Contents of the CSV file,
				Look for and remove speech marks and unrecognized characters,
				Map the file into a readable array then send it through.

			*/
			$csv= file_get_contents($file);
			$csv= str_replace(array(chr(163)),'',$csv);
			$csv= str_replace(array('"'),'',$csv);
			$array = array_map("str_getcsv", explode("\n", $csv));


			foreach ($array as $key=>$arr) {
				// Group by first item in array
				if ($arr[0]) {
						$out['data'][$arr[0]][]=$arr;
				}
			}


			//echo "<pre>";
			//print_r($csv);
			//echo "<pre>";

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
				$model_caseflow = new Application_Model_Caseflow;
				$q = "SELECT        sum(tl_netamt) as totalcash
							FROM          tranlist
							WHERE         (tran_code LIKE 'DR%')";
				$result = $model_caseflow->caseflow_query($q);


				echo (int) round($result['rows'][0]['totalcash'],0);

		}

		if ($api == "get_cash_test"){

				$_SESSION['test_cash']=$_SESSION['test_cash']+16121;
				echo $_SESSION['test_cash'];
		}

		if ($api == "get_cash_month"){
			$model_caseflow = new Application_Model_Caseflow;
			$date = date('Y-m-01');
			$q = "SELECT     sum(tx_amount) as totalcashmonth
						FROM       debt_trans
						WHERE      tx_date >= '$date' AND (tran_code LIKE 'DR%')";
			$result = $model_caseflow->caseflow_query($q);


			echo (int) round($result['rows'][0]['totalcashmonth'],0);

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
			$from = date('Y-m-01');
			$to = date('Y-m-d');
			$sortbycolumn = 'perc_rev';
			$sortbycolumnF = 'target';
			$month = date("Y-m");
			$data = $model_caseflow->collector_cash($from,$to,$sortbycolumn,1,$month);
			$dataF = $model_caseflow->collector_cash($from,$to,$sortbycolumnF,1,$month);
			$count = 0;



			$names=array();
			$line=array();
			$bar=array();
			foreach ($dataF as $key => $value) {
				$names[]=$value['op_code'];
				$line[]=$value['target'];
				$bar[]=$value['revenue'];
				$yesterday[]=0;
			}
			foreach ($data as $key => $value) {
				$namesG[]=$value['op_code'];
				$lineG[]=$phasing;
				$barG[]=$value['perc_rev'];

			}

			/*echo '<pre>';
				print_r($data);
				//print_r($line);
				//print_r($bar);
			echo '</pre>';*/

			$final_array['chart_f']['names']=$names;
			$final_array['chart_f']['line']=$line;
			$final_array['chart_f']['bar']=$bar;
			$final_array['chart_f']['yesterday']=$yesterday;


			$final_array['chart_g']['names']=$namesG;
			$final_array['chart_g']['line']=$lineG;
			$final_array['chart_g']['bar']=$barG;



			echo json_encode($final_array);


		}

		if($api == "chart_f_g_topcharts_old") {

			$count = 0;
			$file="..\data\data_live\CTT.csv";
			$csv= file_get_contents($file);
			$csv = array_map("str_getcsv", explode("\n", $csv));
			function method1($a,$b) {
				return ($a[2]<= $b[2]) ? 1 : -1;
			}

			  usort($csv, "method1");
			$names=array();
			$line=array();
			$bar=array();
			foreach ($csv as $agent){ if ($agent[0]=="AUTO" || $agent[0]=="") { } else {
				$names[]=$agent[0];
				$line[]=$agent[2];
				$bar[]=$agent[7];
				$yesterday[]=$agent[5];
			}}

			/*echo '<pre>';
				print_r($names);
				print_r($line);
				print_r($bar);
			echo '</pre>';
			*/
			$final_array['chart_f']['names']=$names;
			$final_array['chart_f']['line']=$line;
			$final_array['chart_f']['bar']=$bar;
			$final_array['chart_f']['yesterday']=$yesterday;






			$count = 0;
			$file="..\data\data_live\CTT.csv";
			$csv= file_get_contents($file);
			$csv = array_map("str_getcsv", explode("\n", $csv));
			function method2($a,$b) {
				return ($a[9]<= $b[9]) ? 1 : -1;
			}

			  usort($csv, "method2");

			  $perc = $csv[0][8];

			$names=array();
			$line=array();
			$bar=array();
			foreach ($csv as $agent){ if ($agent[0]=="AUTO" || $agent[0]=="") { } else {
				$names[]=$agent[0];
				$line[] = str_replace("%", "", $perc);
				$bar[]= strval(round(floatval($agent[9]) * 100, 0));
			}}


			$final_array['chart_g']['names']=$names;
			$final_array['chart_g']['line']=$line;
			$final_array['chart_g']['bar']=$bar;



			echo json_encode($final_array);

		}

		if($api == "update_graph_f") {

			$count = 0;
			$file="..\data\data_live\CTT.csv";
			$csv= file_get_contents($file);
			$csv = array_map("str_getcsv", explode("\n", $csv));
			function method1($a,$b) {
				return ($a[2]<= $b[2]) ? 1 : -1;
			}

			  usort($csv, "method1");
			$names=array();
			$line=array();
			$bar=array();
			foreach ($csv as $agent){ if ($agent[0]=="AUTO" || $agent[0]=="") { } else {
				$names[]=$agent[0];
				$line[]=$agent[2];
				$bar[]=$agent[7];
				$yesterday[]=$agent[5];
			}}

			/*echo '<pre>';
				print_r($names);
				print_r($line);
				print_r($bar);
			echo '</pre>';
			*/
			$final_array['names']=$names;
			$final_array['line']=$line;
			$final_array['bar']=$bar;
			$final_array['yesterday']=$yesterday;

			echo json_encode($final_array);

		}

		if($api == "update_graph_g") {

			$count = 0;
			$file="..\data\data_live\CTT.csv";
			$csv= file_get_contents($file);
			$csv = array_map("str_getcsv", explode("\n", $csv));
			function method2($a,$b) {
				return ($a[9]<= $b[9]) ? 1 : -1;
			}

			  usort($csv, "method2");

			  $perc = $csv[0][8];

			$names=array();
			$line=array();
			$bar=array();
			foreach ($csv as $agent){ if ($agent[0]=="AUTO" || $agent[0]=="") { } else {
				$names[]=$agent[0];
				$line[] = str_replace("%", "", $perc);
				$bar[]= strval(round(floatval($agent[9]) * 100, 0));
			}}


			$final_array['names']=$names;
			$final_array['line']=$line;
			$final_array['bar']=$bar;

			echo json_encode($final_array);

		}

		if($api == "update_topcharts") {

			function methoda($a,$b) {
				return ($a[9]<= $b[9]) ? 1 : -1;
			}
			function methodb($a,$b) {
				return ($a[7]<= $b[7]) ? 1 : -1;
			}
			function methodc($a,$b) {
				return ($a[4]<= $b[4]) ? 1 : -1;
			}


			$count = 0;
			$file="..\data\data_live\CTT.csv";
			$csv= file_get_contents($file);
			$acsv = array_map("str_getcsv", explode("\n", $csv));
			$bcsv = array_map("str_getcsv", explode("\n", $csv));
			$ccsv = array_map("str_getcsv", explode("\n", $csv));

			usort($acsv, "methoda");
			usort($bcsv, "methodb");
			usort($ccsv, "methodc");
			$names=array();
			$line=array();
			$bar=array();
			foreach ($acsv as $agent){ if ($agent[0]=="AUTO" || $agent[0]=="") { } else {
				$names['perc_target'][]=$agent[0];
				$bar['perc_target'][]= strval(round(floatval($agent[9]) * 100, 0));
			}}
			foreach ($bcsv as $agent){ if ($agent[0]=="AUTO" || $agent[0]=="") { } else {
				$names['pound_collected'][]=$agent[0];
				$bar['pound_collected'][]= $agent[7];
			}}
			foreach ($ccsv as $agent){ if ($agent[0]=="AUTO" || $agent[0]=="") { } else {
				$names['yesterday_collected'][]=$agent[0];
				$bar['yesterday_collected'][]= $agent[4];
			}}

			$final_array['names']=$names;
			$final_array['numbers']=$bar;

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

		$datenow = date('Y
