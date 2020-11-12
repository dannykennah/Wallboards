<?php
global $fdow,$ldow,$month_start,$month_end;
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

class Admin_WallboardController extends Teabag_Controller_Action {

    public function indexAction(){
        global $fdow,$ldow,$month_start,$month_end;
    		$this->view->pageTitle = 'Wallboards';

    		$screensMapper = new Application_Model_Table_WallgroupData();
    		$this->_helper->_layout->setLayout('wallboard');

    		$postValues = $this->_request->getPost();

    		$billy="hello world";

    		$this->view->billy=$billy;

    		$this->view->postdata = $postValues;
            // print_r($postValues);

            if ($postValues['group'] && $postValues['submit'] != 'specific') {
        		$screens = $screensMapper->fetchByGroupID($postValues['group']);
                $this->view->screens = $screens;

                foreach ($screens as $screen) {
                  if  ($screen->screen_id==2) {

                    $googlestat = new Application_Model_Table_Googlestats();

                    $where = array(
  'date = ?' => date('Y-m-d')
);
// $wherea = array(
//   'date >= ?' => date('Y-m-01',strtotime('-1 month')),
//   'date <= ?' => date('Y-m-d',strtotime('-1 month')),
// );
// $datamonths = $googlestat->fetchmonths(date('Y'));
$data = $googlestat->fetchall($where);

                    $this->view->googlestat = $data;
                    //$this->view->{"data_".$screen->screen_id} = $screen;

                  }
                  if  ($screen->screen_id==56) {



                    $SystaskM = new Application_Model_Table_SystemTasks();
                    $tasks = $SystaskM->fetchtasks();
                    $this->view->tasks = $tasks;

                    $replication = new Application_Model_Table_Replications();
                    $reps = $replication->fetchrep();
                    $this->view->fetchReps = $reps;

                    $batchcard = new Application_Model_Paycrs();
                    $batchcard_records = $batchcard->fetchbatchcardruns();

                    $this->view->batchCards = $batchcard_records;

                  }
                  if  ($screen->screen_id==50) {



                    $SystaskM = new Application_Model_Table_SystemTasks();
                    $tasks = $SystaskM->fetchtasks();
                    $this->view->tasks = $tasks;

                    $replication = new Application_Model_Table_Replications();
                    $reps = $replication->fetchrep();
                    $this->view->fetchReps = $reps;

                    $batchcard = new Application_Model_Paycrs();
                    $batchcard_records = $batchcard->fetchbatchcardruns();

                    $this->view->batchCards = $batchcard_records;

                  }
                  else if  ($screen->screen_id==51) {
                    $Redwood = new Application_Model_Redwood();

                    $tasks = $Redwood->fetchCR();
                    $this->view->redwoodtasks = $tasks;

                    $tasks2 = $Redwood->fetchCR2();
                    $this->view->tasks2 = $tasks2;

                    $fetchProjects = $Redwood->fetchProjects();
                    $this->view->fetchProjects= $fetchProjects;

                    $fetchActions = $Redwood->fetchActions();

                    $actions = array();

                    foreach($fetchActions AS $entry){

                        $people = unserialize($entry['assign_user_id']);

                        $people2 = array();

                        foreach($people AS $person){

                            require_once 'Zend/Db/Adapter/Pdo/Mysql.php';

                            $db2 = new Zend_Db_Adapter_Pdo_Mysql(array(
                              'host'     => 'creditresourcesolutions.co.uk',
                              'username' => 'redwood',
                              'password' => '0yPu0l6_',
                              'dbname'   => 'redwood'
                            ));
                            $db2->getConnection();


                            $select = $db2->select()
                                    ->from('admin_users', array('*'))
                                    ->where('admin_user_id = '.$person);

                            $result = $db2->fetchAll($select);

                            array_push($people2,$result[0]['fullname']);
                            }

                            $entry['assign_user_id'] = $people2;
                            array_push($actions,$entry);
                            }

                    $this->view->fetchActions = $actions;

                    echo "<pre style='color:white'>";
                        print_r($fetchActions->assign_user_id);
                    echo "</pre>";

                    //total number of complaints logged MTD
                  } else if  ($screen->screen_id==41) {
                      $Redwood = new Application_Model_Redwood();

                      //total number of Complaints logged MTD
                      $type = $Redwood->fetchComplaint(2);
                      $this->view->type = $type;

                      //total number of EOD logged MTD
                      $type1 = $Redwood->fetchComplaint(1);
                      $this->view->type1 = $type1;

                      //Acknowledgement
                      $status = $Redwood->fetchStatus(1);
                      $this->view->$status = $status;

                      $curr_date = date("m-d");
                      $days2 = 0;
                      $days5 = 0;
                      $daysover= 0;


                      foreach($status as $statuses){
                        $complaint_date = $statuses->$complaint_date;
                        if ($corr_date-$complaint_date =< 2)
                         {
                          $days2++;
                        }
                        elseif ($corr_date-$complaint_date =< 5 => 2)
                        {
                          $days5++;
                        }
                           else {
                             $daysover++;
                           }


                         }
                      //
                      // //Investigation 4 weeks
                      // $status1 = $Redwood->fetchStatus(2);
                      // $this->view->$status1 = $status1;
                      // foreach{ ($status1 as  )
                      //
                      // }
                      //
                      // //Investigation 8 weeks
                      // $status2 = $Redwood->fetchStatus(3);
                      // $this->view->$status2 = $status2;
                      // foreach{ ($status2 as )
                      //
                      // }
                      //
                      // //Under FOS Investigation
                      // $status3 = $Redwood->fetchStatus(5);
                      // $this->view->$status3 = $status3;
                      // foreach{ ($status3 as )
                      //
                      //
                      // }
                      //
                      // $acknowledgment=strtotime(date('Y-m');

                      }

                    $this->view->{"data_".$screen->screen_id} = $screen;
                    //$this->view->screen_data = $this->{"screen_".$screen->screen_id}();
                }
            }

            if ($postValues['screen'] && $postValues['submit'] == 'specific') {
                $screens_q = $screensMapper->select()
                                         ->distinct()
                                         ->from('wall_displayscreens','*')
                                         ->where('screen_id = ?', $postValues['screen']);
                $screens = $screensMapper->fetchAll($screens_q);
                $this->view->screens = $screens;
                foreach ($screens as $screen) {
                    $this->view->{"data_".$screen->screen_id} = $screen;
                    //$this->view->screen_data = $this->{"screen_".$screen->screen_id}();
                }

            }
    }


    public function indexlocalAction(){
        global $fdow,$ldow,$month_start,$month_end;
    		$this->view->pageTitle = 'Wallboards';

    		$screensMapper = new Application_Model_Table_WallgroupData();
    		$this->_helper->_layout->setLayout('wallboard');

    		$screen = $this->getRequest()->getParam('screen');

    		$billy="hello world";

    		$this->view->billy=$billy;
    		$screens = $screensMapper->fetchByGroupID($screen);

    		$this->view->screens = $screens;

    		foreach ($screens as $screen) {
    			$this->view->{"data_".$screen->screen_id} = $screen;
    			//$this->view->screen_data = $this->{"screen_".$screen->screen_id}();
    		}

    }


    public function indexstaticAction(){
        $this->_helper->_layout->setLayout('wallboard');
    		$this->view->pageTitle = 'Wallboards';

    		$this->_helper->_layout->setLayout('wallboard');

        $screen = $this->getRequest()->getParam('screen');

    		//$screens = $screensMapper->fetchByGroupID(2);
    		$this->view->screen = $screen;


    }

    public function updateAction(){


          $this->getHelper('Layout')
               ->disableLayout();

          $this->getHelper('ViewRenderer')
             ->setNoRender();

      $method = $this->getRequest()->getParam('method');

      $data = new Application_Model_Table_SystemHistorys();

      if($method === 'misite'){
        $action = $data->addhistory('18','misite_files_uploaded');
      }

      if($method === 'campaignimport'){
        $action = $data->addhistory('27','Campaign Debtor imports');
      }

      if($method === 'emaildma'){
        $action = $data->addhistory('32','Auto Emails to DMAs');
      }

      if($method === 'compassimports'){
        $action = $data->addhistory('35','Compass portal imports');
      }

      if($method === 'compassexports'){
        $action = $data->addhistory('36','Compass portal exports');
      }

      if($method === 'cpaemailcaseflow'){
        $action = $data->addhistory('37','CPA Emails sent to caseflow.');
      }

      if($method === 'morningreport'){
        $action = $data->addhistory('39','Morning report completed.');
      }

      if($method === 'misiteexports'){
        $action = $data->addhistory('40','MI site exports');
      }

      if($method === 'myresponse'){
        $action = $data->addhistory('41','My response update');
      }

    }

	public function apiAction() {

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

		if ($api=="screen_2_data_token_test") {

      $token_data=get_token_data();
      echo '<pre>';
      print_r($token_data);
      echo '</pre>';

    }

		if ($api=="screen_2_data") {

      /*$token_data=get_token_data();
      echo '<pre>';
      print_r($token_data);
      echo '</pre>';*/

			$date_month= date('Y-m-').'01'; 			// this variable holds the date at the start of the month
			$date_today= date('Y-m-d');				// this holds today's date
			$date_week=get_date_monday();

			// using the dates we retrieve the data we need and then put it into an array
			$chats_this_today = Get_Total_Chats($date_today,$date_today);
			$chats_this_week = Get_Total_Chats($date_week,$date_today);
			$chats_this_month = Get_Total_Chats($date_month,$date_today);
			// $chats_this_week = Get_Total_Chats($date_today,$date_week);
			// $chats_this_month = Get_Total_Chats($date_today,$date_month);
      //
      //
			$avrg_RT_Day = Get_AvrgR_Time($date_today, $date_today);
			$avrg_RT_Week = Get_AvrgR_Time($date_week, $date_today);
			$avrg_RT_Month = Get_AvrgR_Time($date_month,$date_today);
      //
			$live_users = Get_Live_Users();



			$data['chats_today']=($chats_this_today->total);
			$data['chats_week']=($chats_this_week->total);
			$data['chats_month']=($chats_this_month->total);

			$data['avrg_RT_day']=$avrg_RT_Day."s";
			$data['avrg_RT_week']=$avrg_RT_Week."s";
			$data['avrg_RT_month']=$avrg_RT_Month."s";
			$data['live_users'] = $live_users;

			// This just stops a weird glitch where an average response time would appear before there where any chats registered
			if ($data['chats_today'] == 0){
				$data['avrg_RT_day'] = 0;
			}
			if ($data['chats_week'] == 0){
				$data['avrg_RT_week'] = 0;
			}
			if ($data['chats_month'] == 0){
				$data['avrg_RT_month'] = 0;
			}
			echo json_encode($data);

		}


		if ($api=="screen_22_rpc") {
      $array=array();
      $model_caseflow = new Application_Model_Caseflow;

      $queries = array(
        'new_accounts' => "   SELECT COUNT(debt_trans.tran_code) AS value
                              FROM  debt INNER JOIN
                                  debt_trans ON debt.debt_code = debt_trans.debt_code INNER JOIN
                                  debtor ON debt.debt_code = debtor.debt_code
                              WHERE debt_trans.tx_date = CONVERT(DATE, GETDATE()) AND debtor.dr_rectype = 'D' AND	debt_trans.tran_code = 'SU2010'",
        'value_accounts' => " SELECT SUM(debt.dt_debtval) AS value
                              FROM  debt INNER JOIN
                                  debt_trans ON debt.debt_code = debt_trans.debt_code INNER JOIN
                                  debtor ON debt.debt_code = debtor.debt_code
                              WHERE	debt_trans.tx_date = CONVERT(DATE, GETDATE()) AND debtor.dr_rectype = 'D' AND	debt_trans.tran_code = 'SU2010'",
        'letters_sent' => "   SELECT COUNT(debt_trans.tran_code) AS value
                              FROM  debt INNER JOIN
                                    debt_trans ON debt.debt_code = debt_trans.debt_code INNER JOIN
                                    debtor ON debt.debt_code = debtor.debt_code
                              WHERE			debt_trans.tx_date = CONVERT(DATE, GETDATE()) AND debtor.dr_rectype = 'D' AND	(debt_trans.tran_code LIKE 'RD4%')",
        'sms_sent' => "       SELECT		COUNT(debt_trans.tran_code) AS value
                              FROM  debt INNER JOIN
                                    debt_trans ON debt.debt_code = debt_trans.debt_code INNER JOIN
                                    debtor ON debt.debt_code = debtor.debt_code
                              WHERE	debt_trans.tx_date = CONVERT(DATE, GETDATE()) AND
                                    debtor.dr_rectype = 'D' AND	(
                                    debt_trans.tran_code = 'RD6000' OR
                                    debt_trans.tran_code = 'RD6001') AND
                                    debt_trans.dc_code LIKE 'SMS%' AND
                                    (LEN(debtor.dr_phone) > 8 OR LEN(debtor.dr_phone2) > 8 OR LEN(debtor.dr_phone3) > 8)",
        'ivr_sent' => "       SELECT COUNT(debt_trans.tran_code) AS value
                              FROM  debt INNER JOIN
                                    debt_trans ON debt.debt_code = debt_trans.debt_code INNER JOIN
                                    debtor ON debt.debt_code = debtor.debt_code
                              WHERE	debt_trans.tx_date = CONVERT(DATE, GETDATE()) AND
                                    debtor.dr_rectype = 'D' AND	(
                                    debt_trans.tran_code = 'RD6000' OR
                                    debt_trans.tran_code = 'RD6001') AND
                                    debt_trans.dc_code LIKE 'IVR%' AND
                                    (LEN(debtor.dr_phone) > 8 OR LEN(debtor.dr_phone2) > 8 OR LEN(debtor.dr_phone3) > 8)",
        'process_emails' => " SELECT COUNT(debt_trans.tran_code) AS value
                              FROM  debt INNER JOIN
                                    debt_trans ON debt.debt_code = debt_trans.debt_code INNER JOIN
                                    debtor ON debt.debt_code = debtor.debt_code
                              WHERE	debt_trans.tx_date = CONVERT(DATE, GETDATE()) AND
                                    debtor.dr_rectype = 'D' AND	(
                                    debt_trans.tran_code = 'RD6000' OR
                                    debt_trans.tran_code = 'RD6001') AND
                                    debt_trans.dc_code LIKE 'EMA%' AND
                                    debtor.dr_email LIKE '%@%'",
        'outbound_rpc' => " SELECT COUNT(debt_trans.tran_code) AS value
                              FROM  debt INNER JOIN
                                    debt_trans ON debt.debt_code = debt_trans.debt_code INNER JOIN
                                    debtor ON debt.debt_code = debtor.debt_code
                              WHERE	debt_trans.tx_date = CONVERT(DATE, GETDATE()) AND debtor.dr_rectype = 'D' AND	(debt_trans.tran_code = 'MO1051' OR
                                    debt_trans.tran_code = 'MO1121' OR
                                    debt_trans.tran_code = 'MO1130' OR
                                    debt_trans.tran_code = 'MO1160' OR
                                    debt_trans.tran_code = 'MO1999')",
        'inbound_rpc' => " SELECT COUNT(debt_trans.tran_code) AS value
                              FROM  debt INNER JOIN
                                    debt_trans ON debt.debt_code = debt_trans.debt_code INNER JOIN
                                    debtor ON debt.debt_code = debtor.debt_code
                              WHERE	debt_trans.tx_date = CONVERT(DATE, GETDATE()) AND
                                    debtor.dr_rectype = 'D' AND	(
                                    debt_trans.tran_code = 'MO1011' OR
                                    debt_trans.tran_code = 'MO1012' OR
                                    debt_trans.tran_code = 'MO1013' OR
                                    debt_trans.tran_code = 'MO1050' OR
                                    debt_trans.tran_code = 'MO1412' OR
                                    debt_trans.tran_code = 'MO1413' OR
                                    debt_trans.tran_code = 'MO1991' OR
                                    debt_trans.tran_code = 'MO2299' OR
                                    debt_trans.tran_code = 'MO1227' OR
                                    debt_trans.tran_code = 'MO2599' OR
                                    debt_trans.tran_code = 'MO1043' OR
                                    debt_trans.tran_code = 'MO1045' OR
                                    debt_trans.tran_code = 'MO2343')",
        'inbound_email' => " SELECT COUNT(debt_trans.tran_code) AS value
                              FROM  debt INNER JOIN
                                    debt_trans ON debt.debt_code = debt_trans.debt_code INNER JOIN
                                    debtor ON debt.debt_code = debtor.debt_code
                              WHERE	debt_trans.tx_date = CONVERT(DATE, GETDATE()) AND
                                    debtor.dr_rectype = 'D' AND	(
                                    debt_trans.tran_code = 'MO1043' OR
                                    debt_trans.tran_code = 'MO1045' OR
                                    debt_trans.tran_code = 'MO1144' OR
                                    debt_trans.tran_code = 'MO1148') ",
        'collector_email' => " SELECT COUNT(debt_trans.tran_code) AS value
                              FROM  debt INNER JOIN
                                    debt_trans ON debt.debt_code = debt_trans.debt_code INNER JOIN
                                    debtor ON debt.debt_code = debtor.debt_code
                              WHERE	debt_trans.tx_date = CONVERT(DATE, GETDATE()) AND
                                    debtor.dr_rectype = 'D' AND	(
                                    debt_trans.tran_code = 'MO1042' OR
                                    debt_trans.tran_code = 'MO1041' OR
                                    debt_trans.tran_code = 'MO1046' OR
                                    debt_trans.tran_code = 'MO2343')",
        'web_chats' => " SELECT COUNT(debt_trans.tran_code) AS value
                              FROM  debt INNER JOIN
                                    debt_trans ON debt.debt_code = debt_trans.debt_code INNER JOIN
                                    debtor ON debt.debt_code = debtor.debt_code
                              WHERE	debt_trans.tx_date = CONVERT(DATE, GETDATE()) AND
                                    debtor.dr_rectype = 'D' AND	(
                                    debt_trans.tran_code = 'MO2599')",
        'web_messages' => " SELECT	COUNT(debt_trans.tran_code) AS value
                            FROM    debt INNER JOIN
                                    debt_trans ON debt.debt_code = debt_trans.debt_code INNER JOIN
                                    debtor ON debt.debt_code = debtor.debt_code
                            WHERE		debt_trans.tx_date = CONVERT(DATE, GETDATE()) AND
                                    debtor.dr_rectype = 'D' AND	(
                                    debt_trans.tran_code = 'MO2513')",
        'call_backs' => " SELECT		COUNT(debt_trans.tran_code) AS value
                            FROM    debt INNER JOIN
                                    debt_trans ON debt.debt_code = debt_trans.debt_code INNER JOIN
                                    debtor ON debt.debt_code = debtor.debt_code
                            WHERE		debt_trans.tx_date = CONVERT(DATE, GETDATE()) AND
                                    debtor.dr_rectype = 'D' AND	(
                                    debt_trans.tran_code = 'MO2299')",
        'total_plans' => " SELECT		COUNT(debt_trans.tran_code) AS value
                            FROM    debt INNER JOIN
                                    debt_trans ON debt.debt_code = debt_trans.debt_code INNER JOIN
                                    debtor ON debt.debt_code = debtor.debt_code
                            WHERE		debt_trans.tx_date = CONVERT(DATE, GETDATE()) AND
                                    debtor.dr_rectype = 'D' AND	(
                                    debt_trans.tran_code LIKE 'RP1%' OR debt_trans.tran_code = 'MO2512')",
        'eagle_plans' => " SELECT		COUNT(debt_trans.tran_code) AS value
                            FROM    debt INNER JOIN
                                    debt_trans ON debt.debt_code = debt_trans.debt_code INNER JOIN
                                    debtor ON debt.debt_code = debtor.debt_code
                            WHERE		debt_trans.tx_date = CONVERT(DATE, GETDATE()) AND
                                    debtor.dr_rectype = 'D' AND	(
                                    debt_trans.tran_code = 'RP1799' OR
                                    debt_trans.tran_code = 'RP1800' OR
                                    debt_trans.tran_code = 'RP1801' OR
                                    debt_trans.tran_code = 'RP1802')",
        'dma_plans' => " SELECT     COUNT(debt_trans.tran_code) AS value
                            FROM    debt INNER JOIN
                                    debt_trans ON debt.debt_code = debt_trans.debt_code INNER JOIN
                                    debtor ON debt.debt_code = debtor.debt_code
                            WHERE		debt_trans.tx_date = CONVERT(DATE, GETDATE()) AND
                                    debtor.dr_rectype = 'D' AND	(
                                    debt_trans.tran_code = 'RP1522' OR
                                    debt_trans.tran_code = 'RP1524' OR
                                    debt_trans.tran_code = 'RP1541' OR
                                    debt_trans.tran_code = 'RP1542')",
        'web_plans' => " SELECT	    COUNT(debt_trans.tran_code) AS value
                            FROM    debt INNER JOIN
                                    debt_trans ON debt.debt_code = debt_trans.debt_code INNER JOIN
                                    debtor ON debt.debt_code = debtor.debt_code
                            WHERE		debt_trans.tx_date = CONVERT(DATE, GETDATE()) AND
                                    debtor.dr_rectype = 'D' AND	(
                                    debt_trans.tran_code = 'MO2512')",
        'revenue' => " SELECT			  SUM (debt_trans.tx_amount) AS value
                       FROM         debt INNER JOIN
                                    debt_trans ON debt.debt_code = debt_trans.debt_code INNER JOIN
                                    debtor ON debt.debt_code = debtor.debt_code
                        WHERE			  debt_trans.tx_date = CONVERT(DATE, GETDATE()) AND
                                    debtor.dr_rectype = 'D' AND	(
                                    debt_trans.tran_code LIKE 'CC5%')",
        'fees_costs' => "  SELECT	  SUM(
                                    (CASE WHEN debt_trans.tran_code LIKE 'DR%' AND debt_trans.tx_amount > 0 THEN (debt_trans.tx_fees+debt_trans.tx_costs)
                                    WHEN debt_trans.tran_code LIKE 'DR%' AND debt_trans.tx_amount < 0 THEN ((debt_trans.tx_fees * -1)+(debt_trans.tx_costs* -1)) ELSE 0 END)) AS value
                           FROM debt INNER JOIN
                           debt_trans ON debt.debt_code = debt_trans.debt_code INNER JOIN
                           debtor ON debt.debt_code = debtor.debt_code
                           WHERE			debt_trans.tx_date = CONVERT(DATE, GETDATE()) AND
                           debtor.dr_rectype = 'D' AND	(debt_trans.tran_code LIKE 'DR%')      ",
        'payments' => " SELECT			SUM (debt_trans.tx_amount) AS value
                        FROM        debt INNER JOIN
                                    debt_trans ON debt.debt_code = debt_trans.debt_code INNER JOIN
                                    debtor ON debt.debt_code = debtor.debt_code
                        WHERE			  debt_trans.tx_date = CONVERT(DATE, GETDATE()) AND
                                    debtor.dr_rectype = 'D' AND	(
                                    debt_trans.tran_code LIKE 'DR%')",

      );

      foreach($queries as $key=>$sql) {
        $txtval=$model_caseflow->caseflow_query($sql)['rows'][0]['value'];
        if ($txtval) {
          $out=$txtval;
        } else {
          $out=0;
        }
        $results[$key] = $out;

      }

      echo json_encode($results);

    }
		if ($api=="update_sector_agent") {
      $array=array();
			$model_caseflow = new Application_Model_Caseflow;
			// $q1 = "SELECT        sum(tl_netamt) as totalcash
			// 			FROM          tranlist
			// 			WHERE         (tran_code LIKE 'DR%')";
			// $result1 = $model_caseflow->caseflow_query($q1);
      //
      //
			// $totalcollected = round($result1['rows'][0]['totalcash'],0);

			$date = date('Y-m-d');
			$q = "SELECT       client.clcompanynumber, SUM(debt_trans.tx_amount) AS totalcash
						FROM         debt_trans INNER JOIN
                         client ON debt_trans.client_code = client.client_code
					  WHERE        debt_trans.tx_date = CONVERT(DATE,'$date') AND (debt_trans.tran_code LIKE 'DR%')
						GROUP BY 		 client.clcompanynumber";
			$results = $model_caseflow->caseflow_query($q);
      // echo '<pre>';
      //   print_r($results);
      // echo '</pre>';
  			foreach ($results['rows'] as $value) {

  				if ($value['clcompanynumber']) {
  						$out['data'][$value['clcompanynumber']]=substr(str_replace(array(',','"'),'',$value['totalcash']),0,'-3');
  				}

  			}




      if ($out['data']) {
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
      } else {
        $outp['labels'][]='Replicating Out Of Sync';
        $outp['data']['no_data']=0.00;
      }

			$json['sector']['labels'] = $outp['labels'];
			$json['sector']['updated_time'] = "last updated ".date('H:i:s');
			$json['sector']['data'] = array_values($outp['data']);





      $CaseflowModal = new Application_Model_Caseflow();
			$data = $CaseflowModal->collector_cash_today();
      if ($data) {
  			foreach ($data as $key => $valuea) {
  				//$out[$value['op_code']]=$value['totalcash'];
          if ($valuea['op_code']) {
            $outd['data'][$valuea['op_code']]=substr(str_replace(array(',','"'),'',$valuea['totalcash']),0,'-3');
          }
  			}
      } else {
        $outd['data']['Replicating Out Of Sync']=0.00;
      }



			foreach ($outd['data'] as $key=>$data) {
				if ($key=="Profiling" || $key=="Claims" || $key=="Eagle") {
						$fique_all = $fique_all + $data;
						$outpd['data']['Legal']=$fique_all;
				} else {
						$fique_other = $fique_other + $data;
						$outpd['data'][$key]=$data;
				}
			}
			$outd['data']['Legal']=$fique_all;
			arsort($outpd['data']);
			foreach ($outpd['data'] as $lkey=>$data){
				$outpd['labels'][]=$lkey;
			}

			$json['agent']['labels'] = $outpd['labels'];
			$json['agent']['updated_time'] = "last updated ".date('H:i:s');
			$json['agent']['data'] = array_values($outpd['data']);





			//print_r($json);
			//echo "<br>";
			echo json_encode($json);


		}

		if ($api=="update_sector") {

			$file="..\data\data_live\S - Cash Collected - Sectors.csv";
			//$file="c:/xampp/htdocs/sites/wallboards/data/data_live/S - Cash Collected - Sectors.csv";
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

      $sagepayModel = new Application_Model_Table_Sagepay();
      $sagepay = $sagepayModel->getSagePayCounts();
      $out['sagepay']=$sagepay;

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

      $googleAMapper = new Application_Model_Table_GoogleA();

      $theent = $googleAMapper->fetchRow('id = 1');
      echo $theent->active;

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



    if ($api=="update_incalldata_test") {
        header('Content-Type: application/json');

        $i=0;
        $j=0;
        $k=0;

        $callsMapper = new Application_Model_Table_Calls();
        $fetchthecall = $callsMapper->fetchall(array('type = ?'=>1),'answered ASC');
        //$arr2 = array_msort($incoming['list'], array('connected'=>SORT_DESC));
        foreach($fetchthecall as $call){

          $out['list'][$i]['queue']=$call->queue;
          $out['list'][$i]['callerid']=$call->callerid;
          $out['list'][$i]['agent']=$call->agent;
          $out['list'][$i]['answered']=$call->answered;
          $out['list'][$i]['class']=$call->class;
          $i++;
        }
        // array_flip($out['list']);
        // echo '<pre>';
        // print_r(array_flip($out['list']));
        // echo '<pre>';
        $fetchtheoutcall = $callsMapper->fetchall(array('type = ?'=>2));

        foreach($fetchtheoutcall as $callout){

          $out['outgoing_list'][$i]['queue']=$callout->queue;
          $out['outgoing_list'][$i]['callid']=$callout->callid;
          $out['outgoing_list'][$i]['callerid']=$callout->callerid;
          $out['outgoing_list'][$i]['agent']=$callout->agent;
          $out['outgoing_list'][$i]['answered']=$callout->answered;
          $out['outgoing_list'][$i]['class']=$callout->class;
          $out['outgoing_list'][$i]['type']=2;
          $i++;
        }


        $fetchtheoutcall = $callsMapper->fetchall(array('type = ?'=>4));
        $i=0;
        foreach($fetchtheoutcall as $callout){
          $out['agent_list'][$i]['name']=$callout->agent;
          $out['agent_list'][$i]['uid']=$callout->callid;
          $out['agent_list'][$i]['status']=$callout->callerid;
          $out['agent_list'][$i]['statuscode']=$callout->queue;
          $out['agent_list'][$i]['answered']=$callout->answered;
          $out['agent_list'][$i]['call']=false;
          $out['agent_list'][$i]['class']=$callout->class;
          $i++;
        }



        $fetchthestatcall = $callsMapper->fetchrow(array('type = ?'=>3,'callid = ?'=>'stats'));
        $que_d = explode(':',$fetchthestatcall->queue);
        $out['queueing_class']=$fetchthestatcall->class;
        $out['livecall']=$que_d[0];
        $out['calls_today']=$que_d[1];
        $out['calls_aban_today']=$que_d[2];
        $out['calls_lh']=$que_d[3];
        $out['calls_aban_lh']=$que_d[4];
        $out['agents_waiting']=$que_d[5];



        // echo '<pre>';
        //   print_r($out);
        // echo '</pre>';

        echo json_encode($out);

        // echo '<pre>';
        //   print_r($out);
        // echo '</pre>';

    }

    if ($api=="update_incalldata") {
        header('Content-Type: application/json');

        $i=0;
        $j=0;
        $k=0;

        $callsMapper = new Application_Model_Table_Calls();
        $fetchthecall = $callsMapper->fetchall(array('type = ?'=>1),'answered ASC');
        foreach($fetchthecall as $call){

          $out['list'][$i]['queue']=$call->queue;
          $out['list'][$i]['callerid']=$call->callerid;
          $out['list'][$i]['agent']=$call->agent;
          $out['list'][$i]['answered']=$call->answered;
          $out['list'][$i]['class']=$call->class;
          $i++;
        }

        $fetchtheoutcall = $callsMapper->fetchall(array('type = ?'=>2),'answered ASC');

        foreach($fetchtheoutcall as $callout){

          $out['outgoing_list'][$i]['queue']=$callout->queue;
          $out['outgoing_list'][$i]['callid']=$callout->callid;
          $out['outgoing_list'][$i]['callerid']=$callout->callerid;
          $out['outgoing_list'][$i]['agent']=$callout->agent;
          $out['outgoing_list'][$i]['answered']=$callout->answered;
          $out['outgoing_list'][$i]['class']=$callout->class;
          $out['outgoing_list'][$i]['type']=2;
          $i++;
        }


        $fetchtheoutcall = $callsMapper->fetchall(array('type = ?'=>4));
        $i=0;
        foreach($fetchtheoutcall as $callout){
          $out['agent_list'][$i]['name']=$callout->agent;
          $out['agent_list'][$i]['uid']=$callout->callid;
          $out['agent_list'][$i]['status']=$callout->callerid;
          $out['agent_list'][$i]['statuscode']=$callout->queue;
          $out['agent_list'][$i]['answered']=$callout->answered;
          $out['agent_list'][$i]['call']=false;
          $out['agent_list'][$i]['class']=$callout->class;
          $i++;
        }



        $fetchthestatcall = $callsMapper->fetchrow(array('type = ?'=>3,'callid = ?'=>'stats'));
        $que_d = explode(':',$fetchthestatcall->queue);
        $out['queueing_class']=$fetchthestatcall->class;
        $out['livecall']=$que_d[0];
        $out['calls_today']=$que_d[1];
        $out['calls_aban_today']=$que_d[2];
        $out['calls_lh']=$que_d[3];
        $out['calls_aban_lh']=$que_d[4];
        $out['agents_waiting']=$que_d[5];
        $out['queueingcall']=$que_d[6];



        // echo '<pre>';
        //   print_r($out);
        // echo '</pre>';

        echo json_encode($out);

        // echo '<pre>';
        //   print_r($out);
        // echo '</pre>';

    }


		if ($api=="update_incalldata_old") {
			require_once 'Dialer/Dialer.php';

			$incoming = api_ajax("incoming");

			$i=0;
			$j=0;
			$k=0;

			$arr2 = array_msort($incoming['list'], array('connected'=>SORT_DESC));
			foreach ($arr2 as $incoming) {
				$durationtime=gmdate("H:i:s", $incoming['srvtime']-$incoming['answered']);
				$durationtimeq=gmdate("H:i:s", $incoming['srvtime']-$incoming['connected']);
				$seconds=$incoming['srvtime']-$incoming['connected'];
				$agent=false;
				$duration=false;
				$class=false;
				if ($incoming['answered']) { $duration=$durationtime; } else { $duration=$durationtimeq; $j++; }
				if ($incoming['agent']) { $agent=$incoming['agent']; } else { $agent="Not Assigned"; }
				if ($seconds<=300) {
					$class='alert-success'; $k++;
				} elseif ($seconds>=301 && $seconds<=600) {
					$class="alert-warning";
				} else {
					$class="alert-danger";
				}

				$out['list'][$i]['queue']=$incoming['queue'];
				$out['list'][$i]['callerid']=$incoming['callerid'];
				$out['list'][$i]['agent']=$agent;
				$out['list'][$i]['answered']=$duration;
				$out['list'][$i]['class']=$class;
				$i++;
			}
              			 	$out['queueingcall']=$j;

            $outgoing = api_ajax("outgoing");
            $arr3 = array_msort($outgoing['list'], array('connected'=>SORT_DESC));

            foreach ($arr3 as $incoming) {
                $durationtime=gmdate("H:i:s", $incoming['srvtime']-$incoming['answered']);
                $durationtimeq=gmdate("H:i:s", $incoming['srvtime']-$incoming['connected']);
                $seconds=$incoming['srvtime']-$incoming['connected'];
                $agent=false;
                $duration=false;
                $class=false;
                if ($incoming['answered']) { $duration=$durationtime; } else { $duration=$durationtimeq; $j++; }
                if ($incoming['agent']) { $agent=$incoming['agent']; } else { $agent="Not Assigned"; }
                if ($seconds<=300) {
                    $class='alert-success'; $k++;
                } elseif ($seconds>=301 && $seconds<=600) {
                    $class="alert-warning";
                } else {
                    $class="alert-danger";
                }

                $out['outgoing_list'][$i]['queue']=$incoming['queue'];
                $out['outgoing_list'][$i]['callerid']=$incoming['ddi'];
                $out['outgoing_list'][$i]['agent']=$agent;
                $out['outgoing_list'][$i]['answered']=$duration;
                $out['outgoing_list'][$i]['class']=$class;
                $i++;
            }
            // print_r($out['outgoing_list']);
            $openingTimesMapper = new Application_Model_Table_CustomOpeningTimes();
            $todaysopening = $openingTimesMapper->fetchRow(array('date = ? '=>date('Y-m-d')));
            if ($todaysopening) {
                $startofday=strtotime(date($todaysopening->start));
                $endofday=strtotime(date($todaysopening->end));
                $lasthour=strtotime(date('Y-m-d H').":00:00");
                $lasthourend=strtotime(date('Y-m-d H').":59:59");
            }
            else{
                if (date('D') == 'Sat') {
                    $startofday=strtotime(date('Y-m-d')." 09:30:00");
                    $endofday=strtotime(date('Y-m-d')." 12:30:00");
                    $lasthour=strtotime(date('Y-m-d H').":00:00");
                    $lasthourend=strtotime(date('Y-m-d H').":59:59");
                }
                else{
                    $startofday=strtotime(date('Y-m-d')." 08:00:00");
                    $endofday=strtotime(date('Y-m-d')." 19:00:00");
                    $lasthour=strtotime(date('Y-m-d H').":00:00");
                    $lasthourend=strtotime(date('Y-m-d H').":59:59");
                }
            }



			$options2 = array(
				'range'=> $startofday.':'.$endofday,
				'fields'=> 'tm_init,callid,sec_dur,ocid,ocnm,tm_answ',
				'groupby'=>'callid',
			);

			$aban2 = api_reporting("cdr", $options2);
			$ik=0;
			$ikah=0;
			$im=0;
			foreach ($aban2['list'] as $data) {
				//Abandoned Calls//
				if ($data['sec_dur']>25 & $data['tm_answ'] == 0){ $ik++; }

				if ($data['sec_dur']>25 & $data['tm_answ'] == 0 & $data['tm_init']>=$lasthour & $data['tm_init']<=$lasthourend){ $ikah++; }

				if ($data['tm_init']>=$lasthour & $data['tm_init']<=$lasthourend){ $im++; }

			}


				if ($j<2) { $que_class="que_green"; } elseif ($j>2 || $j<5) { $que_class="que_amber"; } else { $que_class="que_red"; }

        //
        // $googleAMapper = new Application_Model_Table_GoogleA();
        //
        // $cc = $googleAMapper->fetchRow('id = 2');
        // $cc->active=$k;
        // $cc->save();
        // $cca = $googleAMapper->fetchRow('id = 3');
        // $cca->active=$j;
        // $cca->save();


				$out['queueing_class']=$que_class;
        // if ($_SERVER['REMOTE_ADDR']=="192.168.50.115") {
        //   $out['queueingcall']=5;
        // } else {

        // }
				$out['livecall']=$k;
				$out['calls_today']=$aban2['total'];
				$out['calls_aban_today']=$ik;
				$out['calls_lh']=$im;
				$out['calls_aban_lh']=$ikah;






				$agents = api_ajax("agents");

				$i=0;
				$j=0;

					//$durationtime=gmdate("H:i:s", $incoming['srvtime']-$incoming['time']);
					//$duration=false;
					//$class=false;
					//if ($incoming['time']) { $duration=$durationtime; } else { $duration="Queueing"; $j++; }
					//if ($incoming['time']) { $class='alert-success'; } else { $class="alert-danger"; }
				$kk=0;
				foreach ($agents['list'] as $agent){
					$durationtime=gmdate("H:i:s", $agent['srvtime']-$agent['time']);
					$seconds=$agent['srvtime']-$agent['time'];

					if ($seconds<=45) {
						$class='alert-success'; $k++;
					} elseif ($seconds>=46 && $seconds<=60) {
						$class="alert-warning";
					} else {
						$class="alert-danger";
					}


					if ($agent['statuscode']) { $statuscode=$agent['statuscode']; } else { $statuscode="IDLE"; }
					$out['agent_list'][$i]['name']=$agent['name'];
					$out['agent_list'][$i]['uid']=$agent['uid'];
					if ($agent['status']=="wait") { $kk++; }
					$out['agent_list'][$i]['status']=$agent['status'];
					$out['agent_list'][$i]['statuscode']=$statuscode;
					$out['agent_list'][$i]['answered']=$durationtime;
					$out['agent_list'][$i]['call']=$agent['call'];
					$out['agent_list'][$i]['class']=$class;
					$i++;
				}
				$out['agents_waiting']=$kk;
        echo '<pre>';
          print_r($out);
        echo '</pre>';
			echo json_encode($out);

		}
		/*
		if ($api=="update_incalldata") {
			require_once '../library/Dialer/Dialer.php';
			$incoming = api_ajax("incoming");


			$i=0;
			$j=0;
			$k=0;

			$arr2 = array_msort($incoming['list'], array('connected'=>SORT_DESC));

			foreach ($arr2 as $incoming) {
				$durationtime=gmdate("H:i:s", $incoming['srvtime']-$incoming['answered']);
				$durationtimeq=gmdate("H:i:s", $incoming['srvtime']-$incoming['connected']);
				$seconds=$incoming['srvtime']-$incoming['connected'];
				$agent=false;
				$duration=false;
				$class=false;
				if ($incoming['answered']) { $duration=$durationtime; } else { $duration=$durationtimeq; $j++; }
				if ($incoming['agent']) { $agent=$incoming['agent']; } else { $agent="Not Assigned"; }
				if ($seconds<=300) {
					$class='alert-success'; $k++;
				} elseif ($seconds>=301 && $seconds<=600) {
					$class="alert-warning";
				} else {
					$class="alert-danger";
				}

				$out['list'][$i]['queue']=$incoming['queue'];
				$out['list'][$i]['callerid']=$incoming['callerid'];
				$out['list'][$i]['agent']=$agent;
				$out['list'][$i]['answered']=$duration;
				$out['list'][$i]['class']=$class;
				$i++;
			}

			$startofday=strtotime(date('Y-m-d')." 08:00:00");
			$endofday=strtotime(date('Y-m-d')." 18:59:59");
			$lasthour=strtotime(date('Y-m-d H').":00:00");
			$lasthourend=strtotime(date('Y-m-d H').":59:59");

			$options2 = array(
				'range'=> $startofday.':'.$endofday,
				'fields'=> 'tm_init,callid,sec_dur,ocid,ocnm,tm_answ',
				'groupby'=>'callid',
			);

			$aban2 = api_reporting("cdr", $options2);
			$ik=0;
			$ikah=0;
			$im=0;
			foreach ($aban2['list'] as $data) {
				//Abandoned Calls//
				if ($data['sec_dur']>25 & $data['tm_answ'] == 0){ $ik++; }

				if ($data['sec_dur']>25 & $data['tm_answ'] == 0 & $data['tm_init']>=$lasthour & $data['tm_init']<=$lasthourend){ $ikah++; }

				if ($data['tm_init']>=$lasthour & $data['tm_init']<=$lasthourend){ $im++; }

			}



				if ($j<2) { $que_class="que_green"; } elseif ($j>2 || $j<5) { $que_class="que_amber"; } else { $que_class="que_red"; }
				$out['queueing_class']=$que_class;
				$out['queueingcall']=$j;
				$out['livecall']=$k;
				$out['calls_today']=$aban2['total'];
				$out['calls_aban_today']=$ik;
				$out['calls_lh']=$im;
				$out['calls_aban_lh']=$ikah;


			echo json_encode($out);

		}



		if ($api=="update_agentdata") {
			require_once '../library/Dialer/Dialer.php';
			$agents = api_ajax("agents");

			$i=0;
			$j=0;

				//$durationtime=gmdate("H:i:s", $incoming['srvtime']-$incoming['time']);
				//$duration=false;
				//$class=false;
				//if ($incoming['time']) { $duration=$durationtime; } else { $duration="Queueing"; $j++; }
				//if ($incoming['time']) { $class='alert-success'; } else { $class="alert-danger"; }
			$kk=0;
			foreach ($agents['list'] as $agent){
				$durationtime=gmdate("H:i:s", $agent['srvtime']-$agent['time']);
				$seconds=$agent['srvtime']-$agent['time'];

				if ($seconds<=45) {
					$class='alert-success'; $k++;
				} elseif ($seconds>=46 && $seconds<=60) {
					$class="alert-warning";
				} else {
					$class="alert-danger";
				}


				if ($agent['statuscode']) { $statuscode=$agent['statuscode']; } else { $statuscode="IDLE"; }
				$out['list'][$i]['name']=$agent['name'];
				$out['list'][$i]['uid']=$agent['uid'];
				if ($agent['status']=="wait") { $kk++; }
				$out['list'][$i]['status']=$agent['status'];
				$out['list'][$i]['statuscode']=$statuscode;
				$out['list'][$i]['answered']=$durationtime;
				$out['list'][$i]['call']=$agent['call'];
				$out['list'][$i]['class']=$class;
				$i++;
			}
				$out['agents_waiting']=$kk;


			//echo "<pre>";
			//print_r($out);
			//echo "</pre>";
			echo json_encode($out);

		}
		*/

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

	private function screen_60() {

		global $fdow,$ldow,$month_start,$month_end;


	}


}
