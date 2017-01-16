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
        }
        catch (\Exception $exc){
            // e.g. $url = "http://s.com/welcome"
            $url = $this->app->url('page.welcome');
        }
        return $this->app->redirect($url);
    }
}

