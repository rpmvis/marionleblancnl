<?php

namespace Studio\Controllers;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\RedirectResponse;

class FlagController extends BaseController implements ControllerProviderInterface{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];
        $controllers->get('', function () {
            return $this->Redirect();}
        )
        ->assert('language', 'nl|en')
        ->bind('flag.redirect');
        return $controllers;
    }

    public function Redirect():RedirectResponse{
        try{
            // e.g. $url = "/exhibitions/charity"
            $url = $this->app['session']->get('previous_route');
            $pattern_flag = "/^\/nl|en\/flag/";
            if (preg_match($pattern_flag, $url) !== 0)
                $url = $this->app->url('/');
        }
        catch (\Exception $exc){
            // e.g. $url = "http://s.com/welcome"
            $url = $this->app->url('/');
        }
        return $this->app->redirect($url);
    }
}

