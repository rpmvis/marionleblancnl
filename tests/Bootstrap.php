<?php

use app\MyApplication;
use app\Bootstrap;

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

try {
    // base path
    if (!defined('PATH_ROOT')) {
        define('PATH_ROOT', realpath(__DIR__ . '/../'));


//    require_once PATH_ROOT.'/vendor/autoload.php';

        require_once PATH_ROOT . '/../app/Bootstrap.php';


        $my_app = new MyApplication();

        $bootstrap = new Bootstrap($my_app);

//        // ??? http://php-and-symfony.matthiasnoback.nl/2012/02/silex-set-up-your-project-for-testing-with-phpunit/
//        $app['autoloader']->registerNamespace('Studio\\Tests', __DIR__);


        $app->run();
    }
} catch (\Exception $exc) {
    // catch and report any stray exceptions...
    echo $exc->getMessage();
}
