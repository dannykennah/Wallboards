<?php

class Admin_IndexController extends Teabag_Controller_Action {

    public function indexAction(){

		$form = new Admin_Form_StartWallboard();
		$sform = new Admin_Form_StartSpecificWallboard();
		$oform = new Admin_Form_OpenTimes();
        $this->view->form = $form;
        $this->view->sform = $sform;
        $this->view->oform = $oform;

		$groupsMapper = new Application_Model_Table_Wallgroups();
        $screensMapper = new Application_Model_Table_Wallscreens();

        $openingTimesMapper = new Application_Model_Table_CustomOpeningTimes();

        $todaysopening = $openingTimesMapper->fetchRow(array('date = ? '=>date('Y-m-d')));

        if ($todaysopening) {
            $todaysopening->start = date('H:i', strtotime($todaysopening->start));
            $todaysopening->end = date('H:i', strtotime($todaysopening->end));
            $this->view->oform->populate($todaysopening->ToArray());
        }
        //$exportModel = new Application_Model_Export();
		$screens = $screensMapper->fetchAllScreens();
		$SGM = $groupsMapper->fetchGroupMembers();

    // var_dump($SGM);
    // exit;
		$boardgroups = $groupsMapper->fetchAllBoards();
        // Search
		$postValues = $this->_request->getPost();

        if (($postValues) != false) {
            if ($oform->isValid($postValues)) {
                $row = $openingTimesMapper->fetchRow(array('date = ? '=>$_POST['date']));
                if ($row) {
                    $row->start = date('Y-m-d H:i:s', strtotime($_POST['date'].' '.$_POST['start']));
                    $row->end = date('Y-m-d H:i:s', strtotime($_POST['date'].' '.$_POST['end']));
                    $row->save();
                }
                else{
                    $insert = array(
                        'date'=>$_POST['date'],
                        'start'=>date('Y-m-d H:i:s', strtotime($_POST['date'].' '.$_POST['start'])),
                        'end'=>date('Y-m-d H:i:s', strtotime($_POST['date'].' '.$_POST['end'])),
                    );
                    $openingTimesMapper->createRow($insert)->save();
                }
            }
        }

		$this->view->screens = $screens;
		$this->view->boardgroups = $boardgroups;
		$this->view->SGM = $SGM;

    }


	public function gameinfoAction(){
        function get_time_ago( $time )
            {
                $time_difference = time() - $time;

                if( $time_difference < 1 ) { return 'less than 1 second ago'; }
                $condition = array( 12 * 30 * 24 * 60 * 60 =>  'year',
                            30 * 24 * 60 * 60       =>  'month',
                            24 * 60 * 60            =>  'day',
                            60 * 60                 =>  'hour',
                            60                      =>  'minute',
                            1                       =>  'second'
                );

                foreach( $condition as $secs => $str )
                {
                    $d = $time_difference / $secs;

                    if( $d >= 1 )
                    {
                        $t = round( $d );
                        return 'about ' . $t . ' ' . $str . ( $t > 1 ? 's' : '' ) . ' ago';
                    }
                }
            }
        $this->_helper->_layout->disableLayout();

        header('Content-Type: application/json');
        $OperatorModal = new Application_Model_Table_Operators();
        $eventModal = new Application_Model_Table_Awardevents();
        $awardsModal = new Application_Model_Table_Awards();

        // $CaseflowModal = new Application_Model_Caseflow();
        $GameInfo_Logged = new Application_Model_Table_GetGameInfo();
        $awardTotalModal = new Application_Model_Table_Operatorstotals();
        // $CaseflowgameModal = new Application_Model_Caseflowgame();
        if($_GET['month']){
            $operators = $OperatorModal->fetchOperatorTotalsByDate($_GET['month']);
            $operatorsAwardTotals = $awardsModal->awardsTotalsByMonth($_GET['month']);
        } else{
            $operators = $OperatorModal->fetchOperatorTotals();
            $operatorsAwardTotals = $awardsModal->awardsTotals();
        }

         $StoreCttModal = new Application_Model_Table_Storedctts();
         $agents_collector_cash_testingdata = $StoreCttModal->collector_cash(false,false,date('Y-m'));


         foreach ($operatorsAwardTotals as $awtotal) {
             $operators[$awtotal['op_code']][$awtotal['event_group']."xp"]=$awtotal['total_group_xp'];
             $operatorsXPMTD = $GameInfo_Logged->GetMTDXP($awtotal['op_code'], date('Y-m-01'), date('Y-m-d'));
             $operators[$awtotal['op_code']]['XP_MTD']=$operatorsXPMTD;
             $operatorsManual = $GameInfo_Logged->GetManualAwards($awtotal['op_code'], date('Y-m-01'), date('Y-m-d'));
             $operators[$awtotal['op_code']]['manualXP']=$operatorsManual;
             $GetDailyAwards = $GameInfo_Logged->GetDailyAwards($awtotal['op_code']);
             $operators[$awtotal['op_code']]['DAXP']=$GetDailyAwards;

             $latestAchievement = $awardsModal->latestAchievement($awtotal['op_code']);
             $operators[$awtotal['op_code']]['latestAchievement'] = $latestAchievement['name'];

             $TimelatestAchievement = $latestAchievement['created'];
             $operators[$awtotal['op_code']]['time_latestAchievement'] = get_time_ago(strtotime($TimelatestAchievement));
             $operators[$awtotal['op_code']]['time_latestAchievement_sort'] = (strtotime($TimelatestAchievement));

             $accumulatedlatestAchievementPercent = $awardsModal->accumulatedlatestAchievementPercent($awtotal['op_code'],$latestAchievement['name'])['sumofAchievement'];
             $operators[$awtotal['op_code']]['accumulated_latestAchievement_Percent'] = $accumulatedlatestAchievementPercent;
         }
         foreach ($operatorsAwardTotals as $operator) {
           $getGameUserLevel = $GameInfo_Logged->GetGameInfo_OLD($operator['op_code'], date('Y-m-d'), date('Y-m-01'), date('Y-m-d'));
           $operators[$operator['op_code']]['level'] = $getGameUserLevel['level'];
           $operators[$operator['op_code']]['prestige'] = $getGameUserLevel['prestige'];
           $operators[$operator['op_code']]['double'] = $getGameUserLevel['double'];

         }



        $events = $eventModal->fetchAll(array('active = ?'=>1,'e_show = ?'=>1));

        usort($operators, function($a, $b) {
            return $b['time_latestAchievement_sort'] - $a['time_latestAchievement_sort'];
        });
        if ($_GET['test']) {
        echo '<pre>';
         print_r($operators);
        echo '</pre>';
        }

    echo json_encode($operators);







    }





    public function getgamemostsAction(){
        $this->_helper->_layout->disableLayout();
        $awardeventsModal = new Application_Model_Table_Awardevents();
        $CaseflowgameModal = new Application_Model_Caseflowgame();
        $OperatorModal = new Application_Model_Table_Operators();
        $targets = new Application_Model_Table_Targets();
        $targets = $targets->fetchAllTargets(date('Y-m'))->ToArray();
        $awardsModal = new Application_Model_Table_Awards();
        $events = $awardeventsModal->fetchAll(array('active = ? '=>'1','frequency = ? '=>'1'));




        function cmp($a, $b)
            {
            return $a['perc'] <=> $b['perc'];
            }

    function run_daily_event($event, $CaseflowgameModal, $targets, $awardsModal, $awardeventsModal, $OperatorModal){
            $jsonArray = array();
            if ($event->event_id==10){
            $final = array();
            $accounts_worked = $CaseflowgameModal->most_accounts_worked_today();

            foreach ($accounts_worked as $op_worked) {
              $out[$op_worked['op_code']]['op_code']=$op_worked['op_code'];
              $department = $OperatorModal->OperatorsDepartment($op_worked['op_code'])->ToArray();
              $out[$op_worked['op_code']]['department']=$department['department'];
              $out[$op_worked['op_code']]['name']=$department['name'];
              $out[$op_worked['op_code']]['total']=$op_worked['total'];
              foreach ($targets as $op_target) {
                if ($op_worked['op_code']==$op_target['op_code']) {
                    $percent =  ((int)$op_worked['total']/(int)$op_target['worked_target'])*100;
                    $out[$op_worked['op_code']]['target']=$op_target['worked_target'];
                    $out[$op_worked['op_code']]['perc']=$percent;
                }
              }
            }


            usort($out, "cmp");
            $out = array_reverse($out);


            foreach ($out as $op) {
              if ($op['perc']==$out[0]['perc']){
                $final[$op['op_code']]=$op;
              }
            }

            // echo '<pre>';
            // echo '<h2>most accounts worked yesterday</h2>';
            //   print_r($out);
            // echo '</pre>';
            $jsonArray['accounts_worked'] = $out;

            }


            elseif ($event->event_id==11){
            $final = array();
            $revenue = $CaseflowgameModal->most_revenue_today();

            foreach ($revenue as $op_rev) {
            $out[$op_rev['op_code']]['op_code']=$op_rev['op_code'];
            $department = $OperatorModal->OperatorsDepartment($op_rev['op_code'])->ToArray();
            $out[$op_rev['op_code']]['department']=$department['department'];
            $out[$op_rev['op_code']]['name']=$department['name'];
            $out[$op_rev['op_code']]['total']=$op_rev['cash'];
            foreach ($targets as $op_target) {
                if ($op_rev['op_code']==$op_target['op_code']) {
                  $percent =  ((int)$op_rev['cash']/(int)$op_target['target'])*100;
                  $out[$op_rev['op_code']]['target']=$op_target['target'];
                  $out[$op_rev['op_code']]['perc']=$percent;
               }
            }
            }

            usort($out, "cmp");
            $out = array_reverse($out);


            foreach ($out as $op) {
            if ($op['perc']==$out[0]['perc']){
              $final[$op['op_code']]=$op;
            }
            }

            // echo '<pre>';
            // echo '<h2>most accounts rev yesterday</h2>';
            // print_r($out);
            // echo '</pre>';
            $jsonArray['revenue'] = $out;

            }



            elseif ($event->event_id==12){
            $final = array();
            $arrangements = $CaseflowgameModal->arrangements_today();

            foreach ($arrangements as $op_arr) {
              $out[$op_arr['op_code']]['op_code']=$op_arr['op_code'];
              $department = $OperatorModal->OperatorsDepartment($op_arr['op_code'])->ToArray();
              $out[$op_arr['op_code']]['department']=$department['department'];
              $out[$op_arr['op_code']]['name']=$department['name'];
              $out[$op_arr['op_code']]['total']=$op_arr['total'];
              foreach ($targets as $op_target) {
                if ($op_arr['op_code']==$op_target['op_code']) {
                    $percent =  ((int)$op_arr['total']/(int)$op_target['arrangements_target'])*100;
                    $out[$op_arr['op_code']]['target']=$op_target['arrangements_target'];
                    $out[$op_arr['op_code']]['perc']=$percent;
                }
              }
            }

            usort($out, "cmp");
            $out = array_reverse($out);


            foreach ($out as $op) {
              if ($op['perc']==$out[0]['perc']){
                $final[$op['op_code']]=$op;
              }
            }

            // echo '<pre>';
            // echo '<h2>most arrangements yesterday</h2>';
            //   print_r($out);
            // echo '</pre>';
            $jsonArray['arrangements'] = $out;

            }
            return $jsonArray;
        }




        $data = [];
        foreach ($events as $event) {
          // echo $event->event_name." <br>";
            $ran = run_daily_event($event, $CaseflowgameModal, $targets, $awardsModal, $awardeventsModal, $OperatorModal);
            foreach ($ran as $key => $item) {
                $data[$key] = $item;
            }
        }
        // echo '<pre>';
        //   print_r($data);
        // echo '</pre>';

        $final = [];
        foreach ($data as $job => $operators) {
            $tempDep = [];
            foreach ($operators as $key => $operator) {
                if(sizeOf($temp)<3 && !in_array($operator['department'], $tempDep)){
                    $final[$operator['department']][$job] = $operator;
                    array_push($tempDep, $operator['department']);
                }
            }
        }
        echo json_encode($final);


    }


}
