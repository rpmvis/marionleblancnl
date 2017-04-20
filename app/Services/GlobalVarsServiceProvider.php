<?php

namespace app\Services;

use Pimple\ServiceProviderInterface;

class GlobalVarsServiceProvider implements ServiceProviderInterface, GlobalVarsServiceProviderInterface{

    public function __construct(){
        // var_dump('1');
    }

    public function register(\Pimple\Container $app)
    {
        $app['globalvars'] = function() : GlobalVarsServiceProvider {
            return $this;
        };
    }

    public function getBaseUrl(): string{
        if (isset($_SERVER['SERVER_NAME'])){
            // set $baseUrl = site's base url
            $url = sprintf(
                "%s://%s",
                isset($_SERVER['HTTPS']) &&
                $_SERVER['HTTPS'] != 'off' ?
                    'https' :
                    'http',
                $_SERVER['SERVER_NAME']
            );

            $url = rtrim($url, '/\\');

        } else {
            // in test
            $url = 'http://s.com';
        }
        return $url;
    }

    public function getRequestUri(): string{
        if (isset($_SERVER['REQUEST_URI'])){
            return $_SERVER['REQUEST_URI'];
        }
        else return '/';
    }
}
