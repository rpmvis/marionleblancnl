<?php

namespace Studio\Tests\Controllers;

use Silex\WebTestCase;
use app\MyApplication;

class WebTestCase_base extends WebTestCase
{

    public function createApplication()
    {
        $app = new MyApplication();
        $app['debug'] = true;
        $app['session.test'] = true;
        unset($app['exception_handler']);

        $app->bootstrap();
        return $app;
    }
}


