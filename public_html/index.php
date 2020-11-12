<?php
// if ($_SERVER['REMOTE_ADDR']=="192.168.50.115" || $_SERVER['REMOTE_ADDR']=="192.168.113.2") {

  /**
   * Application Bootstrap
   * (c) Teabag Studios 2013
   * @copyright Teabag Studios <http://teabagstudios.com/>
   */
error_reporting(0);
  //error_reporting(E_ERROR | E_PARSE);

  ini_set('memory_limit', '256M');
  ini_set('max_execution_time', 300);
  defined('ROOT_PATH') || define('ROOT_PATH', realpath('..'));
  defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath('../application'));
  defined('PUBLIC_PATH') || define('PUBLIC_PATH', realpath('../public_html'));
  defined('REPORT_PATH') || define('REPORT_PATH', dirname(__DIR__) . '/var/log/reports/');
  defined('LIBRARY_PATH') || define('LIBRARY_PATH', realpath('../library'));
  defined('SESSION_PATH') || define('SESSION_PATH', realpath('../var/session'));

  defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

  date_default_timezone_set('Europe/London');

  set_include_path(
      LIBRARY_PATH . PATH_SEPARATOR .
      get_include_path()
  );
  set_include_path(
      ROOT_PATH . PATH_SEPARATOR .
      get_include_path()
  );
  /** Zend_Application */
  require_once 'Livechat/Chat.php';
  require_once 'Zend/Application.php';
  require_once 'Essendex/autoload.php';


  try {

      // Create application, bootstrap, and run
      $application = new Zend_Application(
          APPLICATION_ENV,
          APPLICATION_PATH . '/configs/application.ini'
      );

      $options = array(
          'resources' => array(
              'modules' => array(),
          ),
      );

      $application->setOptions($options);
      $application->setBootstrap(APPLICATION_PATH . '/Bootstrap.php', 'Bootstrap');
      $application->bootstrap();
      $application->run();

  } catch (Exception $e) {

      if (APPLICATION_ENV == 'development') {
          echo '<h1 style="font:bold 1.5em sans-serif;">Exception</h1>';
          echo '<pre style="background:#ffc;padding:10px;">' . $e->getMessage() . '</pre>';
          echo '<h2 style="font:bold 1em sans-serif;">Trace</h2>';
          echo '<pre style="background:#ffe;padding:10px;">' . $e->getTraceAsString() . '</pre>';
      }

  }

// } else {
//   echo 'Maintainance Mode!';
// }
