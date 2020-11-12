<?php

class Admin_Bootstrap extends Zend_Application_Module_Bootstrap {

    protected function _initRoutes() {
        $router = Zend_Controller_Front::getInstance()->getRouter();
        $urlPrefix = 'admin';

        $router->addRoute('admin-dashboard' , new Zend_Controller_Router_Route("/$urlPrefix",
            array(
                'module' => 'admin',
	        'controller' => 'index',
	        'action' => 'index',
            )
        ));

		$router->addRoute('admin-wallboard' , new Zend_Controller_Router_Route("/$urlPrefix/wallboard",
            array(
                'module' => 'admin',
          'controller' => 'wallboard',
          'action' => 'index',
            )
        ));

        $router->addRoute('admin-wallboard-local' , new Zend_Controller_Router_Route("/$urlPrefix/wallboard_local/:screen",
            array(
                'module' => 'admin',
          'controller' => 'wallboard',
          'action' => 'indexlocal',
            )
        ));

        $router->addRoute('admin-loginmaster' , new Zend_Controller_Router_Route("/loginmaster",
            array(
                'module' => 'admin',
          'controller' => 'users',
          'action' => 'loginmaster',
            )
        ));


    $router->addRoute('admin-static' , new Zend_Controller_Router_Route("/$urlPrefix/wallboard_static/:screen",
            array(
                'module' => 'admin',
          'controller' => 'wallboard',
          'action' => 'indexstatic',
            )
        ));



		$router->addRoute('admin-wallboard' , new Zend_Controller_Router_Route("/$urlPrefix/wallboard/api/:api",
            array(
                'module' => 'admin',
	        'controller' => 'wallboard',
	        'action' => 'api',
            )
        ));

		$router->addRoute('admin-wallboard-test' , new Zend_Controller_Router_Route("/$urlPrefix/wallboard/apitest/:api",
            array(
                'module' => 'admin',
	        'controller' => 'wallboardtest',
	        'action' => 'apitest',
            )
        ));

		$router->addRoute('admin-legal' , new Zend_Controller_Router_Route("/$urlPrefix/legal",
            array(
                'module' => 'admin',
	        'controller' => 'index',
	        'action' => 'legal',
            )
        ));

		$router->addRoute('admin-csr' , new Zend_Controller_Router_Route("/$urlPrefix/campaign_success_report",
            array(
                'module' => 'admin',
	        'controller' => 'index',
	        'action' => 'csr',
            )
        ));

    $router->addRoute('admin-clients' , new Zend_Controller_Router_Route("/$urlPrefix/get-client-list",
        array(
            'module' => 'admin',
      'controller' => 'index',
      'action' => 'getclients',
        )
    ));

		$router->addRoute('admin-clients-api' , new Zend_Controller_Router_Route("/api-client/:apikey",
            array(
                'module' => 'default',
	        'controller' => 'index',
	        'action' => 'getclients',
            )
        ));


        $router->addRoute('admin-users' , new Zend_Controller_Router_Route("/$urlPrefix/users",
            array(
                'module' => 'admin',
	        'controller' => 'users',
	        'action' => 'index',
            )
        ));

        $router->addRoute('admin-users-delete' , new Zend_Controller_Router_Route("/$urlPrefix/users/delete/:admin_id",
            array(
                'module' => 'admin',
	        'controller' => 'users',
	        'action' => 'delete',
            )
        ));

        $router->addRoute('admin-user-add' , new Zend_Controller_Router_Route("/$urlPrefix/users/add/",
            array(
                'module' => 'admin',
	        'controller' => 'users',
	        'action' => 'add',
            )
        ));

        $router->addRoute('admin-user-editadmin' , new Zend_Controller_Router_Route("/$urlPrefix/users/editadmin/:admin_id",
            array(
                'module' => 'admin',
	        'controller' => 'users',
	        'action' => 'editadmin',
            )
        ));

        $router->addRoute('admin-messages' , new Zend_Controller_Router_Route("/$urlPrefix/messages",
         array(
          'module' => 'admin',
          'controller' => 'users',
          'action' => 'messages',
         )
        ));

        $router->addRoute('admin-forgot-password' , new Zend_Controller_Router_Route("/$urlPrefix/forgot-password",
            array(
                'module' => 'admin',
	        'controller' => 'users',
	        'action' => 'forgotpassword',
            )
        ));

        $router->addRoute('admin-change-password' , new Zend_Controller_Router_Route("/$urlPrefix/change-password",
            array(
                'module' => 'admin',
	        'controller' => 'users',
	        'action' => 'changepassword',
            )
        ));

        $router->addRoute('admin-reset-password' , new Zend_Controller_Router_Route("/$urlPrefix/reset-password/:user_id/:key",
            array(
                'module' => 'admin',
	        'controller' => 'users',
	        'action' => 'resetpassword',
            )
        ));

        $router->addRoute('admin-login' , new Zend_Controller_Router_Route("/$urlPrefix/login",
            array(
                'module' => 'admin',
	        'controller' => 'users',
	        'action' => 'login',
            )
        ));

        $router->addRoute('admin-local-login' , new Zend_Controller_Router_Route("/$urlPrefix/locallogin/:key/:group",
            array(
                'module' => 'admin',
	        'controller' => 'users',
	        'action' => 'locallogin',
            )
        ));


        $router->addRoute('admin-logout' , new Zend_Controller_Router_Route("/$urlPrefix/logout",
            array(
                'module' => 'admin',
	        'controller' => 'users',
	        'action' => 'logout',
            )
        ));


        $router->addRoute('admin-collectors' , new Zend_Controller_Router_Route("/$urlPrefix/collectors",
                array(
                     'module' => 'admin',
                     'controller' => 'collectors',
                     'action' => 'index',
                )
        ));
        $router->addRoute('admin-collectors-status' , new Zend_Controller_Router_Route("/$urlPrefix/collectors/:user/:status",
                array(
                     'module' => 'admin',
                     'controller' => 'collectors',
                     'action' => 'activeinactive',
                )
        ));

        $router->addRoute('admin-collectors-edit' , new Zend_Controller_Router_Route("/$urlPrefix/collectors/:user",
                array(
                     'module' => 'admin',
                     'controller' => 'collectors',
                     'action' => 'edit',
                )
        ));

        $router->addRoute('admin-ctt' , new Zend_Controller_Router_Route("/$urlPrefix/ctt",
                array(
                     'module' => 'admin',
                     'controller' => 'collectors',
                     'action' => 'ctt',
                )
        ));

        $router->addRoute('admin-prevctt' , new Zend_Controller_Router_Route("/$urlPrefix/previous-ctt",
                array(
                     'module' => 'admin',
                     'controller' => 'collectors',
                     'action' => 'prevctt',
                )
        ));

        $router->addRoute('admin-xp' , new Zend_Controller_Router_Route("/$urlPrefix/gamification",
                array(
                     'module' => 'admin',
                     'controller' => 'collectors',
                     'action' => 'gamification',
                )
        ));
        $router->addRoute('admin-gameinfo' , new Zend_Controller_Router_Route("/$urlPrefix/gameinfo",
                array(
                     'module' => 'admin',
                     'controller' => 'index',
                     'action' => 'gameinfo',
                )
        ));
        $router->addRoute('admin-gamemosts' , new Zend_Controller_Router_Route("/$urlPrefix/gamemosts",
                array(
                     'module' => 'admin',
                     'controller' => 'index',
                     'action' => 'getgamemosts',
                )
        ));

        $router->addRoute('admin-update-method' , new Zend_Controller_Router_Route("/$urlPrefix/update/:method",
        array(
            'module' => 'admin',
            'controller' => 'wallboard',
            'action' => 'update',
        )
    ));


    }
}
