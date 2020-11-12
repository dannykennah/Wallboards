<?php

class Admin_CollectorsController extends Teabag_Controller_Action {

    public function indexAction(){

      $form = new Admin_Form_Deduct();

      $CaseflowModal = new Application_Model_Caseflow();
      $OperatorModal = new Application_Model_Table_Operators();
      $DeductModal = new Application_Model_Table_Deductions();
      $RedwoodModal = new Application_Model_Redwood();

      $operators = $OperatorModal->fetchAll(array());
      $active_operators = $OperatorModal->fetchAll(array('active = ?'=>1),'name ASC');

      $this->view->active_operators=$active_operators;
      $this->view->operators=$operators;
      $this->view->form=$form;

      $redwoodusers = $RedwoodModal->fetchUsers();
      foreach ($redwoodusers as $user) {

          $new_op = $OperatorModal->fetchRow(array('name = ? '=>$user['fullname']));
        //e/cho $user['fullname']."  ";
        if (!$new_op) {
          $newopp=array(
            'name'=>$user['fullname']
          );
          $new_acc = $OperatorModal->createRow($newopp);
          $newid = $new_acc->save();

          $TotalModal = new Application_Model_Table_Totals();

          $data=array(
           'month'=>date('Y-m'),
           'user_id'=>$newid
          );
          $new_row = $TotalModal->createRow($data);
          $new_row->save();



          //echo 'Added - '.$user['op_code'].' ---- '.$newid.' <br>';
        } else {
          //echo 'exists <br>';
        }
      }

      if ($this->getRequest()->isPost()) {
        if ($form->isValid($this->getRequest()->getPost())){
            $values = $form->getValues();
              $deduct_arr=array(
                'user_id'=>$values['from_operator'],
                'amount'=>"-".$values['amount'],
                'rev'=>"-".$values['rev'],
                'month'=>date('Y-m'),
                'debt_code'=>$values['debt_code'],
                'reason'=>$values['reason']
              );
              $add_arr=array(
                'user_id'=>$values['to_operator'],
                'amount'=>$values['amount'],
                'rev'=>$values['rev'],
                'month'=>date('Y-m'),
                'debt_code'=>$values['debt_code'],
                'reason'=>$values['reason']
              );
              /*echo '<pre>';
                print_r($deduct_arr);
              echo '</pre>';
              echo '<pre>';
                print_r($add_arr);
              echo '</pre>';*/
              if ($values['from_operator']!=0) {
                $ded_to = $DeductModal->createRow($deduct_arr);
                $dedid = $ded_to->save();
              }
              $add_to = $DeductModal->createRow($add_arr);
              $addid = $add_to->save();

              $this->_helper->FlashMessenger->addMessage('Deductions Set '.$addid.' | '.$dedid , 'successful');
              $this->_helper->redirector->goToRouteAndExit(array(), 'admin-collectors', true);


        }
      }


      $from=date('Y-m-01');
      $to=date('Y-m-d');


    }
    public function cttAction(){
        $CaseflowModal = new Application_Model_Caseflow();
        //$CronModal = new Application_Model_Table_Crons();

        //$crontask = $CronModal->fetchRow(array('id = ?'=>1));

        //$nextupdate = date('Y-m-d H:i:s',strtotime($crontask->next_runtime));
        //$this->view->newupdate=$nextupdate;
        //$nextupdate=date('Y-m-d H:i:s',strtotime('+10 minutes')).' 00:00:00';
        // $crontask->last_run=date('Y-m-d H:i:s');
        // $crontask->next_runtime=$nextupdate;
        // $crontask->save();




        $searchby=$_GET['searchby'];
        $order=$_GET['order'];

        $StoreCttModal = new Application_Model_Table_Storedctts();
        $data = $StoreCttModal->collector_cash($searchby,$order,date('Y-m'));
        
        $from=date('Y-m-01 00:00:00');
        $to=date('Y-m-d 23:59:59');

        $lastmonth = date("Y-m");
        //$data = $CaseflowModal->collector_cash($from,$to,$searchby,$order,$lastmonth);
        //
        // $StoreCttModal = new Application_Model_Table_Storedctts();
        // foreach ($data as $row) {
        //
        //  $create = $StoreCttModal->createRow($row);
        //  $create->month=date('Y-m');
        //  $create->save();
        //
        // }
        //
        $this->view->ctt = $data;



    }

    public function prevcttAction(){
        $CaseflowModal = new Application_Model_Caseflow();

        $searchby=$_GET['searchby'];
        $order=$_GET['order'];

        $from=date('Y-m-d',strtotime('first day of last month')).' 00:00:00';
        $to=date('Y-m-d',strtotime('last day of last month')).' 23:59:59';

        // $from='2019-05-01 00:00:00';
        // $to='2019-05-31 23:59:59';

        $lastmonth = date("Y-m", strtotime($from));
        //$data = $CaseflowModal->collector_cash($from,$to,$searchby,$order,$lastmonth);

        $StoreCttModal = new Application_Model_Table_Storedctts();
        $data = $StoreCttModal->collector_cash($searchby,$order,$lastmonth);



        $this->view->ctt = $data;



    }

    public function editAction(){

        $form = new Admin_Form_Editop(array('is_edit' => 'true'));
        $targetform = new Admin_Form_Target();


        $user = $this->getRequest()->getParam('user');
        $OperatorModal = new Application_Model_Table_Operators();
        $TargetsModal = new Application_Model_Table_Targets();
        $DeductModal = new Application_Model_Table_Deductions();

        $userid = $OperatorModal->fetchRow(array('user_id = ?' => $user));
        $prevtargets = $TargetsModal->fetchPrevTargets($user);
        $targets = $TargetsModal->fetchRow(array('user_id = ?' => $user,'month = ?'=>date('Y-m')));
        $deduct = $DeductModal->fetchAll(array('user_id = ?' => $user,'month = ?'=>date('Y-m')));
        $form->populate($userid->toArray());
        if ($targets) {
         $targetform->populate($targets->toArray());
        }
        if ($this->getRequest()->isPost()) {
          if ($_POST['submit_target'] && $targetform->isValid($this->getRequest()->getPost())){
            //echo 'valid';
            $values = $targetform->getValues();
            $values['month'] = date('Y-m');
            $values['user_id'] = $user;
            if (!$targets) {
             $newTarget = $TargetsModal->createRow($values);
             $newTarget->save();
            } else {
             $targets->setFromArray($values);
             $targets->save();
            }

            $this->_helper->FlashMessenger->addMessage('Target Set' , 'successful');
            $this->_helper->redirector->goToRouteAndExit(array('user'=>$user), 'admin-collectors-edit', true);
          }
          if ($_POST['submit_edit'] && $form->isValid($this->getRequest()->getPost())){
            //echo 'valid';
            $valuess = $form->getValues();

            $edit_user = $userid->setfromarray($valuess);
            $edit_user->save();

            $this->_helper->FlashMessenger->addMessage('User Updated' , 'successful');
            $this->_helper->redirector->goToRouteAndExit(array('user'=>$user), 'admin-collectors-edit', true);

          }
        }





        $this->view->prevtargets = $prevtargets;
        $this->view->targets = $targets;
        $this->view->deductions = $deduct;
        $this->view->form = $form;
        $this->view->targetform = $targetform;

    }
    public function activeinactiveAction(){

      $this->getHelper('Layout')->disableLayout();
      $this->getHelper('ViewRenderer')->setNoRender();
      $user = $this->getRequest()->getParam('user');
      $status = $this->getRequest()->getParam('status');

      $OperatorModal = new Application_Model_Table_Operators();

      $operator = $OperatorModal->fetchRow(array('user_id = ?'=>$user));
      $operator->active = $status;
      $operator->save();
      if ($status==1) { $txt = 'active'; $type="successful"; } else { $txt = 'not active'; $type="warning"; }

      $this->_helper->FlashMessenger->addMessage('User '.$operator->name.' is now '.$txt,$type);
      $this->_helper->redirector->goToRouteAndExit(array(), 'admin-collectors', true);

    }


}
