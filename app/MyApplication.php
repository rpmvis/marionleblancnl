<?php

namespace app;

use Silex\Application;


class MyApplication extends Application
{
    // class MyApplication: extension of Silex application

    // extend class with UrlGeneratorTrait 'path' and 'url' methods
    use Application\UrlGeneratorTrait;

    public function __construct(){
        parent::__construct();
    }

    function bootstrap(){
        // bootstrap MyApplication
        $bootstrap = new Bootstrap($this);
    }
}
