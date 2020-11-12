<?php

/**
 * Handle public requests.
 */
class IndexController extends Teabag_Controller_Action
{

    /**
     * Forward requests for the homepage to the admin as there's
     * no public element.
     */
	 	public function indexAction()
    {
        $this->forward('index', 'index', 'admin');
    }
		public function appleAction()
		{
			$appleMapper = new Application_Model_Table_Applenotifications();
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(true);
			$apikey = $this->getRequest()->getParam('apikey');
			if ($apikey=='ybtzWYOksw4UoKpCRc_Generate') {

				if ($_POST) {
					$userMapper = new Application_Model_Table_AdminUsers();
					$userFound = $userMapper->fetchRow(array('username = ?'=>$_POST['Email']));

					 if ($userFound){
						 $imput = rand(0000,9999);
						 $random_code = str_pad($imput, 4, "0000", STR_PAD_LEFT);

						 $userFound->oneoffcode=$random_code;
						 $userFound->save();

						 $message = new \Esendex\Model\DispatchMessage(
								 "CRS", /* Send from */
								 $userFound->phone, /* Send to any valid number */
								 $random_code." \r\n Use the above code to login into CRS Websites \r\n ",
								\Esendex\Model\Message::SmsType
						 );
						 $authentication = new \Esendex\Authentication\LoginAuthentication(
								 "EX0293115", /* Your Esendex Account Reference */
								 "stuarte@creditresourcesolutions.co.uk", /* Your login email address */
								 "Nfo0JK8DJ4dmVhTS" /* Your password */
						 );
						 $service = new \Esendex\DispatchService($authentication);
						 $result = $service->send($message);
						 //print $result->id();
						 $response['repsonse'][]='SMS Sent';
						 $response['sms_code']=$random_code;


					 } else {
						 	$response['repsonse'][] = 'Access but no user found';
					 }
				 } else {
					 $response['repsonse'][] = 'Access but no post';
				 }

			} else if ($apikey=='ybtzWYOksw4UoKpCRc') {

				if ($_POST) {


					$data=array(
						'name'=>$_POST['Name'],
						'app'=>$_POST['App'],
						'device'=>$_POST['Device'],
						'debtcode'=>$_POST['debtcode']
					);
					$userFound = $appleMapper->fetchRow(array('device = ?'=>$_POST['Device']));
					if(!$userFound){
						$new_user = $appleMapper->createRow($data);
						$new_user->last_access=date('Y-m-d H:i:s');
						$new_user->save();
						$response['repsonse'][] = 'User Added';
					} else {
						$userFound->last_access=date('Y-m-d H:i:s');
						$userFound->save();
						$response['repsonse'][] = 'User Found';
					}
				} else {
						$response['repsonse'][] = 'Access but no post';
				}
			} else {
				$response['repsonse'][] =  'No Access';
			}
			echo json_encode($response,true);
		}

    public function getclientsAction()
    {
		$apikey = $this->getRequest()->getParam('apikey');
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if ($apikey=='ybtzWYOksw4UoKpCRc') {
			$debtorsMapper = new Application_Model_Table_Debtors();
			$clients = $debtorsMapper->fetchApiClients();
			//echo $clients;
			echo $this->_helper->json($clients);
		} else {
			echo "incorrect api key";
		}
    }

    /**
     * Handle requests for no further email communication.
     */
    public function unsubscribeAction()
    {

        if (($postValues = $this->_request->getPost()) != false) {

            // Process unsubscribe

            $debtorsMapper = new Application_Model_Table_Debtors();
            $unsubscribesMapper = new Application_Model_Table_Unsubscribes();

            $hash_config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/unsubscribe.ini');

            $unsubscribe = $unsubscribesMapper->findByEmail($postValues['email']);
            if (sizeof($unsubscribe) > 0) {
                throw new Zend_Controller_Action_Exception('This page does not exist');
            }

            $foundMatch = false;
            $debtors = $debtorsMapper->fetchByEmail($postValues['email']);
            foreach ($debtors as $debtor) {

                $testHash = sha1($debtor->debtor_code . $hash_config->hash_salt);
                if ($testHash === $postValues['hash']) {
                    $foundMatch = true;
                    $this->view->success = true;

                    $unsubscribe = $unsubscribesMapper->createRow(array(
                        'email' => $postValues['email']
                    ));
                    $unsubscribe->save();

                }

            }

            if (!$foundMatch) {
                throw new Zend_Controller_Action_Exception('This page does not exist');
            }

        } elseif (($getValues = $this->_request->getQuery()) != false) {

            // Show warning and confirm

            $this->view->form = new Default_Form_Unsubscribe();
            $this->view->form->populate($getValues);

        } else {
            throw new Zend_Controller_Action_Exception('This page does not exist');
        }

    }

}
