<?php
namespace Acme\Test;

// WebTestCase: abstract class for functional tests
use Silex\WebTestCase as BaseWebTestCase;

class WebTestCase extends BaseWebTestCase
{
    public function createApplication()
    {
        $app = require $this->getApplicationDir().'/app.php';

        return $app;
    }

    public function getApplicationDir()
    {
        // APP_DIR: see phpunit.xml
        // <server name="APP_DIR" value="s.com" />
        return $_SERVER['APP_DIR'];     }
}