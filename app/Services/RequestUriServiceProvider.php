<?php

namespace app\Services;

use Pimple\ServiceProviderInterface;

class RequestUriServiceProvider implements ServiceProviderInterface{

    public function register(\Pimple\Container $app)
    {
        $app['requesturi'] = function() : RequestUriServiceProvider {
            return $this;
        };
    }

    public function uri(): string{
        if (isset($_SERVER['REQUEST_URI'])){
            var_dump("NOU MOE:" . $_SERVER['REQUEST_URI']);
            return $_SERVER['REQUEST_URI'];
        }
        else return '/';

    }
}
