<?php

class Default_Bootstrap extends Zend_Application_Module_Bootstrap {

    protected function _initRoutes() {
        $router = Zend_Controller_Front::getInstance()->getRouter();

        $router->addRoute('default-unsubscribe' , new Zend_Controller_Router_Route("/unsubscribe",
            array(
                'module' => 'default',
	        'controller' => 'index',
	        'action' => 'unsubscribe',
            )
        ));

        $router->addRoute('apple_notifications' , new Zend_Controller_Router_Route("/apple_notifications/:apikey",
            array(
                'module' => 'default',
                'controller' => 'index',
                'action' => 'apple',
            )
        ));


    }


}
