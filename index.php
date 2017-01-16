<?php

use app\MyApplication;
use Silex\Application;

error_reporting(E_ALL);
//ini_set('display_errors', TRUE);
//ini_set('display_startup_errors', TRUE);

try {
    define( 'PATH_ROOT', realpath(__DIR__) );
    require_once PATH_ROOT.'/vendor/autoload.php';

    if (substr(PATH_ROOT, 0, 10) == '/home/rene') // to be improved
        define( 'APP_ENV', 'dev');
    else define( 'APP_ENV', 'prod');

    // create MyApplication $app, extended from Silex application
    $my_app = new MyApplication();

    $blog = $my_app['controllers_factory'];
    $blog->get('/blog', function () {
        return 'Blog home page';
    });

    $my_app->bootstrap();

    $my_app->run();

} catch (\Exception $exc) {
    // catch and report any stray exceptions...
    echo $exc->getMessage();
}


