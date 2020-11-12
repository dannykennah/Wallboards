<?php
/**
 * Class for Customer Users
 */
class Admin_UsersController extends Teabag_Controller_Action {

    public function init() {
        parent::init();

          $notificationsModel = new Application_Model_Table_Notifications();
  								$not = $notificationsModel->fetchNotificationsCount();
          $this->view->thenotcount = $not->count;

    }

    public function indexAction(){
        $userModel = new Application_Model_Table_AdminUsers();
        $where = array('is_deleted = 0');
        $users = $userModel->fetchAll($where, 'created DESC');

        $page = $this->getRequest()->getParam('page');
        $paginator = $users->getPaginator($page);
        $this->view->users = $paginator;
    }

    public function deleteAction(){
        $adminUserId = $this->getRequest()->getParam('admin_id');
        $adminUsersMapper = new Application_Model_Table_AdminUsers();

        $adminUser = $adminUsersMapper->fetchRow(array('admin_user_id = ?' => $adminUserId));
        if($adminUser){
            $adminUser->softDelete();
        }
        $this->_helper->FlashMessenger->addMessage('User has been deleted' , 'successful');
        $this->_helper->redirector->goToRouteAndExit(array(), 'admin-users', true);
    }

    public function messagesAction(){

       $notificationsModel = new Application_Model_Table_Notifications();
       $not = $notificationsModel->fetchNotificationsAll();
       $notnext = $notificationsModel->fetchNotificationsAll();
       $this->view->userid=$this->_currentUser->admin_user_id;

       $page = $this->getRequest()->getParam('page');
       $paginator = $not->getPaginator($page);


       $this->view->notifications=$paginator;
       foreach ($notnext as $no) {
        $no->read_receipt=1;
        $no->save();
       }
       //$this->view->messages=$not;
       //print_r($not);
        //echo 'test';
    }

    public function changepasswordAction(){
        $form = new Admin_Form_Resetpassword();
        $adminUsersMapper = new Application_Model_Table_AdminUsers();
        $adminUser = $adminUsersMapper->fetchRow(array('admin_user_id = ?' => $this->_currentUser->admin_user_id));
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->_request->getPost())) {
                $values = $form->getValues();
                //update password
                $adminUser->hashPassword($values['new_password']);
                $adminUser->save();

                $this->_helper->FlashMessenger->addMessage('Your password has been edited' , 'successful');
                $this->_helper->redirector->goToRouteAndExit(array(), 'admin-dashboard', true);
            }
        }
        $this->view->form = $form;
    }

    /**
     * Allows new admin users to be created
     */
    public function addAction() {
        $form = new Admin_Form_CreateAdminUser();
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->_request->getPost())) {
                $values = $form->getValues();
                $adminMapper = new Application_Model_Table_AdminUsers();
                $userFound = $adminMapper->fetchByUsername($values['username']);
                if(!$userFound){
                    $values['is_active'] = 1;
                    $values['is_admin'] = 1;
                    $adminUser = $adminMapper->createRow($values);
                    $adminUser->hashPassword($values['new_password']);
                    $adminUser->save();
                    $this->_helper->FlashMessenger->addMessage('The user has been created' , 'successful');
                    $this->_helper->redirector->goToRouteAndExit(array(), 'admin-users', true);
                } else {
                    $this->view->messages['error'] = "This username already exists";
                }
            }
        }
        $this->view->form = $form;
    }

    public function editadminAction() {

        $form = new Admin_Form_CreateAdminUser(array('is_edit' => 'true'));


        $adminId = $this->getRequest()->getParam('admin_id');
        $adminMapper = new Application_Model_Table_AdminUsers();

        $adminUser = $adminMapper->fetchRow(array('admin_user_id = ?' => $adminId));

        if(!$adminUser){
            throw new Zend_Controller_Dispatcher_Exception('Unknown client ID');
        }


        if($this->getRequest()->isPost()){
            if($form->isValid($this->getRequest()->getPost())){

                $data = $form->getValues();
                $adminUser->setFromArray($data);
                $adminUser->hashPassword($data['new_password']);
                $adminUser->save();
                $this->view->messages['successful'] = 'User saved successfully';
            }
        } else{
            $form->populate($adminUser->toArray());
        }

        $this->view->form = $form;
    }

    public function localloginAction(){

          $boardgroup = $this->getRequest()->getParam('group');
          $key = $this->getRequest()->getParam('key');

          if ($key=="wWBjrLFnvF") {

            $form = new Admin_Form_Login();
            $adapter = new Teabag_Auth_Adapter_DbTable(null, 'admin_users', 'username', 'password',
                  'SHA1(CONCAT(password_salt, ?))');
             $adapter->setIdentity('Admin');
             $adapter->setCredential('QAwsed1!');
             $auth = Zend_Auth::getInstance();
             $result = $auth->authenticate($adapter);

             if ($result->isValid()) {
                 //successful login
                 $storage = new Zend_Auth_Storage_Session();
                 $resultset = $adapter->getResultRowObject(null, array('password' , 'password_salt'));
                 $resultset->user = 'admin';
                 $storage->write($resultset);

                 $this->_helper->redirector->goToRouteAndExit(array('screen'=>$boardgroup), 'admin-wallboard-local', true);

             } else {
                 //login unsuccessful
                 //$this->_helper->redirector->goToRouteAndExit(array(), 'admin-login');
             }

            $this->view->loginForm = $form;
           } else{
             //$this->_helper->redirector->goToRouteAndExit(array(), 'admin-login');
           }

    }

    public function loginAction(){
        if($_GET['key']){
            $sitesModal = new Application_Model_Table_Sites();
            $siteInfo = $sitesModal->fetchRow(array('site_id = ?'=>7));
            $access_key_posted = substr($_GET['key'], 0, 50);
            $master_key_posted = substr($_GET['key'], 50, 100);
            if ($access_key_posted == $siteInfo->access_key) {
                $adapter = new Teabag_Auth_Adapter_DbTable(null, 'admin_users', 'master_key', 'master_key');
                   $adapter->setIdentity($master_key_posted);
                   $adapter->setCredential($master_key_posted);
                   $auth = Zend_Auth::getInstance();
                   $result = $auth->authenticate($adapter);
                   if ($result->isValid()) {
                       //successful login
                       $storage = new Zend_Auth_Storage_Session();
                       $resultset = $adapter->getResultRowObject(null, array('password' , 'password_salt'));
                       $resultset->user = 'admin';

                       $storage->write($resultset);
                       $this->_helper->redirector->goToRouteAndExit(array(), 'admin-dashboard');

                       //$this->_helper->FlashMessenger->addMessage('You were logged in successfully', 'successful');
                       if(strpos($this->_helper->lastUrl()->getRequestUri(), 'logout') || strpos($this->_helper->lastUrl()->getRequestUri(), 'login')){
                           //if last url was logout redirect user to dashboard
                           $this->_helper->redirector->goToRouteAndExit(array(), 'admin-dashboard');
                       } else {
                           //else redirect user to the url they requested
                           $this->_helper->redirector->gotoUrl($this->_helper->lastUrl()->getRequestUri(), array('prependBase' => false));
                       }


                   } else {
                       //login unsuccessful
                       $this->view->messages['error'] = 'Your email address or password was incorrect';
                   }
            }
        }
        else{
            // header("Location: http://master.halifax.creditresourcesolutions.co.uk/");
            // die();
            $form = new Admin_Form_Login();
            if($this->getRequest()->isPost()){
                if($form->isValid($this->getRequest()->getPost())){
                    $adapter = new Teabag_Auth_Adapter_DbTable(null, 'admin_users', 'username', 'password',
                            'SHA1(CONCAT(password_salt, ?))');
                       $adapter->setIdentity($form->getValue('email_address'));
                       $adapter->setCredential($form->getValue('new_password'));
                       $auth = Zend_Auth::getInstance();
                       $result = $auth->authenticate($adapter);

                       if ($result->isValid()) {
                           //successful login
                           $storage = new Zend_Auth_Storage_Session();
                           $resultset = $adapter->getResultRowObject(null, array('password' , 'password_salt'));
                           $resultset->user = 'admin';
                           $storage->write($resultset);
                           $this->_helper->FlashMessenger->addMessage('You were logged in successfully', 'successful');
                           if(strpos($this->_helper->lastUrl()->getRequestUri(), 'logout') || strpos($this->_helper->lastUrl()->getRequestUri(), 'login')){
                               //if last url was logout redirect user to dashboard
                               $this->_helper->redirector->goToRouteAndExit(array(
                                    ), 'admin-dashboard');
                           } else {
                               //else redirect user to the url they requested
                               $this->_helper->redirector->gotoUrl($this->_helper->lastUrl()->getRequestUri(), array('prependBase' => false));
                           }
                       } else {
                           //login unsuccessful
                           $this->view->messages['error'] = 'Your email address or password was incorrect';
                       }
                }
            }
            $this->view->loginForm = $form;
        }
    }
    public function loginmasterAction(){
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        switch ($_SERVER['HTTP_ORIGIN']) {
            case 'http://master.halifax.creditresourcesolutions.co.uk': case 'http://master.halifax.creditresourcesolutions.co.uk':
            header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']);
            header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
            header('Access-Control-Max-Age: 1000');
            header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
            break;
        }

        if($_POST['site'] && $_POST['key'] && !$_POST['action']){
            $access_key_posted = substr($_POST['key'], 0, 50);
            $master_key_posted = substr($_POST['key'], 50, 100);
            $sitesModal = new Application_Model_Table_Sites();
            $usersModal = new Application_Model_Table_AdminUsers();
            $siteInfo = $sitesModal->fetchRow(array('site_id = ?'=>7));
            $userInfo = $usersModal->fetchRow(array('master_key = ?'=>$master_key_posted));

            if($userInfo && $siteInfo->access_key == $access_key_posted){
                echo "authenticated";
            }
            else {
                echo "not found";
            }

        }
        if($_POST['site'] && $_POST['key'] && $_POST['action']=='add_user'){
            $access_key_posted = substr($_POST['key'], 0, 50);
            $master_key_posted = substr($_POST['key'], 50, 100);
            $sitesModal = new Application_Model_Table_Sites();
            $usersModal = new Application_Model_Table_AdminUsers();
            $siteInfo = $sitesModal->fetchRow(array('site_id = ?'=>7));
            $userInfo = $usersModal->fetchRow(array('username LIKE ?'=>$_POST['username'],'is_deleted = ?'=>0));

            if($siteInfo->access_key == $access_key_posted){
                if($userInfo){
                    $userInfo->master_key = $master_key_posted;
                    $userInfo->save();
                    echo "sorted";
                }
                else {
                    $values = array('username'=>$_POST['username'],'op_code'=>$_POST['op_code'],'is_active'=>1,'is_admin'=>$_POST['is_admin'],'master_key'=>$master_key_posted);
                    $adminUser = $usersModal->createRow($values);
                    $adminUser->hashPassword('QAwsed1');
                    $id = $adminUser->save();
                    echo "sorted";
                }
            }

        }

    }

    /**
     * Action to log a user out
     */
    public function logoutAction() {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        //redirect user
        $this->_helper->redirector->goToRouteAndExit(array(), 'admin-login');
    }

    /**
     * Allow a user to reset their password
     * Sends the user an email with the activation link
     */
    public function forgotpasswordAction() {
        $form = new Admin_Form_ResendPassword();
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->_request->getPost())) {
                //check user is registered
                $values = $form->getValues();
                $siteUsersMapper = new Application_Model_Table_AdminUsers();
                $exists = $siteUsersMapper->fetchByUsername($values['email_address']);
                if($exists){
                    //user exists
                    $recoveryEmailsMapper = new Application_Model_Table_AdminUserRecoveryEmails();
                    $recoveryEmail = $recoveryEmailsMapper->createRow();
                    $recoveryEmail->admin_user_id = $exists->admin_user_id;
                    $recoveryEmail->email_address = $exists->username;
                    $recoveryEmail->hashActivationKey();
                    $recoveryEmail->save();
                }
                $this->_helper->FlashMessenger->addMessage('You password has been reset, please check your email' , 'successful');
                $this->_helper->redirector->goToRouteAndExit(array(), 'admin-dashboard', true);
            }
        }
        $this->view->form = $form;
    }

    /**
     * Allows the user to type in a new password if the activation key matches
     */
    public function resetpasswordAction(){
        $key = urldecode($this->getRequest()->getParam('key'));
        $admin_id = $this->getRequest()->getParam('user_id');
        if($key && $admin_id){
            //check hash key exists and is not expired
            $recoveryEmailsMapper = new Application_Model_Table_AdminUserRecoveryEmails();
            $recoverEmail = $recoveryEmailsMapper->fetchByActivationKey($key, $admin_id);
            if(!$recoverEmail){
                //not found so redirect
                $this->_helper->redirector->goToRouteAndExit(array(
                    'module' => 'default',
                    'controller' => 'index',
                    'action' => 'index',
                ), 'default', true);
            }

            $form = new Admin_Form_Resetpassword();
            if ($this->getRequest()->isPost()) {
                if ($form->isValid($this->_request->getPost())) {
                    $values = $form->getValues();
                    //update password
                    $adminMapper = new Application_Model_Table_AdminUsers();
                    $customer = $adminMapper->fetchRow("admin_user_id = $recoverEmail->admin_user_id");
                    $customer->hashPassword($values['new_password']);
                    $customer->save();

                    //remove from recovery email so that password cannot be reset again
                    $recoveryEmailsMapper->delete("admin_user_id = $recoverEmail->admin_user_id");
                    $this->_helper->FlashMessenger->addMessage('Your password has been reset, you may now login using your new password' , 'successful');
                    $this->_helper->redirector->goToRouteAndExit(array(), 'admin-dashboard', true);
                }
            }
            $this->view->form = $form;
        } else {
            //redirect as not valid
            $this->_helper->redirector->goToRouteAndExit(array(
                    'module' => 'default',
                    'controller' => 'index',
                    'action' => 'index',
                ), 'default', true);
        }
    }

    public function createclientuserAction() {

        $form = new Admin_Form_CreateClientUser();

        $clientModel = new Application_Model_Table_Clients();
        $clients = $clientModel->fetchClient(false);

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->_request->getPost())) {
                $values = $form->getValues();

                $adminMapper = new Application_Model_Table_AdminUsers();
                $userFound = $adminMapper->fetchByUsername($values['username']);
                if(!$userFound){
                    $values['is_active'] = 1;
                    $values['is_admin'] = 0;
                    $adminUser = $adminMapper->createRow($values);
                    $adminUser->hashPassword($values['new_password']);
                    $id = $adminUser->save();

                    $adminLinkTables = new Application_Model_Table_LinkTables();

                    foreach($values['client'] as $client) {
                       $adminClientLink = $adminLinkTables->createRow(array('admin_user_id'=>$id,'client_id'=>$client));
                       $adminClientLink->save();
                    }

                    $this->_helper->FlashMessenger->addMessage('The user has been created' , 'successful');
                    $this->_helper->redirector->goToRouteAndExit(array(), 'admin-create-client-user', true);
                } else {
                    $this->view->messages['error'] = "This username already exists";
                }
            }
        }

        $this->view->clients = $clients;
        $this->view->form = $form;

    }

     public function editclientuserAction() {

        $form = new Admin_Form_CreateClientUser(array('is_edit' => 'true'));

        $clientModel = new Application_Model_Table_Clients();
        $clientCodes = $clientModel->fetchClient(false);

        $adminId = $this->getRequest()->getParam('admin_id');

        $adminMapper = new Application_Model_Table_AdminUsers();
        $adminUser = $adminMapper->fetchRow(array('admin_user_id = ?' => $adminId));

        $adminLinkTables = new Application_Model_Table_LinkTables();
        $companys = $adminLinkTables->fetchAll(array('admin_user_id = ?' => $adminId));


        if(!$adminUser){
            throw new Zend_Controller_Dispatcher_Exception('Unknown client ID');
        }


        if($this->getRequest()->isPost()){
            if($form->isValid($this->getRequest()->getPost())){

                $data = $form->getValues();
                $adminUser->setFromArray($data);
                $id = $adminUser->save();

                foreach($companys as $company) {
                    $company->delete();
                }

                foreach($data['client'] as $client) {
                       $adminClientLink = $adminLinkTables->createRow(array('admin_user_id'=>$id,'client_id'=>$client));
                       $adminClientLink->save();
                }

                $this->view->messages['successful'] = 'Client saved';
            }
        } else{
            $clients = array();
            foreach($companys->toArray() as $client) {
                $clients[] = $client['client_id'];
            }
            $form->populate($adminUser->toArray() + array('client' => $clients));

        }

        $this->view->clients = $clientCodes;
        $this->view->form = $form;
    }


}
