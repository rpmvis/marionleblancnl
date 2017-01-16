<?php

namespace Studio\Controllers;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;

class DefaultController extends BaseController implements ControllerProviderInterface{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];
        $controllers->get('', function () {
            return $this->getResponse();}
        )
        ->bind('page.default');
        return $controllers;
    }

    public function getResponse():string{
        $locale = $this->context['locale'];
        switch ($locale){
            case 'en':
                $welcome = "You're very welcome";
                break;
            default:
                $welcome = 'Hartelijk welkom';
                break;
        }

        $values = array('welcome'=>$welcome);
        $data = array('context' => $this->context, 'values'=> $values);
        $view = $this->app['blade']->view('layouts.default', $data);
        return $view;
    }
}
