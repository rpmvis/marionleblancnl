<?php

namespace Studio\Controllers;

use app\Helpers\Helper;
use app\Helpers\MenuHelper;
use Aea\Model\BladeProxy;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;

class DefaultController extends BaseController implements ControllerProviderInterface{
    protected $blade;

    public function __construct(Helper $helper, MenuHelper $menuHelper, BladeProxy $blade){
        parent::__construct($helper, $menuHelper);

        $this->blade = $blade;
    }

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
        $data = array('context' => $this->context, 'menu_context' => $this->menu_context, 'values'=> $values);
        $view = $this->blade->view('layouts.default', $data);
        return $view;
    }
}
