<?php
namespace app\Services;

use Pimple\ServiceProviderInterface;

class ConvertServiceProvider implements ServiceProviderInterface{

    public function register(\Pimple\Container $app)
    {
        $app['convert'] = function() : ConvertServiceProvider {
            return $this;
        };
    }


}