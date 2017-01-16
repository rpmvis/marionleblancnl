<?php
// src/Studio/Controller/WorkController.php
namespace Studio\Controllers;

use app\MyApplication;
use app\Helpers\Helper;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Silex;
use Symfony\Component\HttpFoundation\Request;


class DemoController extends BaseController implements ControllerProviderInterface{
    protected $request;

    public function __construct(Helper $helper){
        parent::__construct($helper);
    }

    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];
        $controllers->get('', function () {return $this->ShowText();}
            )
        ->bind('demo');
        return $controllers;
    }

    public function ShowText(){
        return "this is some text." ;
    }
}
