<?php

use app\MyApplication;

error_reporting(E_ALL);
//ini_set('display_errors', TRUE);
//ini_set('display_startup_errors', TRUE);

try {

    require_once realpath(__DIR__).'/vendor/autoload.php';

    // create MyApplication $app, extended from Silex application
    $my_app = new MyApplication();

    $my_app->bootstrap();

    $my_app->run();

} catch (\Exception $exc) {
    // catch and report any stray exceptions...
    echo $exc->getMessage();
}


