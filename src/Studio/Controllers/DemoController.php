<?php
// src/Studio/Controller/WorkController.php
namespace Studio\Controllers;

use app\Helpers\Helper;
use app\Helpers\MenuHelper;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;

class DemoController extends BaseController implements ControllerProviderInterface{

//    public function __construct(Helper $helper, MenuHelper $menuHelper){
//        parent::__construct($helper, $menuHelper);
//    }

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
