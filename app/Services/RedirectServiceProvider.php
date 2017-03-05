<?php

namespace app\Services;

use Pimple\ServiceProviderInterface;
use app\MyApplication;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RedirectServiceProvider implements ServiceProviderInterface{
    private $app;

    public function __construct(MyApplication $my_app){
        $this->app = $my_app;
    }

    public function redirect($url) : RedirectResponse{
        return $this->app->redirect($url);
    }

    public function register(\Pimple\Container $container)
    {
        $container['redirect'] = function () : RedirectServiceProvider {
            return $this;
        };
    }
}
