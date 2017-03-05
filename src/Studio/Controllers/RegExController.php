<?php

namespace Studio\Controllers;

use Aea\Model\BladeProxy;
use app\Helpers\RegExHelper;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Silex\Api\ControllerProviderInterface;

class RegExController implements ControllerProviderInterface{
    protected $blade;

    public function __construct(BladeProxy $blade){
        $this->blade = $blade;
    }

    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];
        $controllers->get('', function () {
            return $this->getResponse();}
        )->bind('get.page.regex');

        $controllers->post('', function (Request $request) {
            return $this->getPostResponse($request);}
        )->bind('post.page.regex');

        return $controllers;
    }

    function exception_handler($exception): void {
        echo "Uncaught exception: " , $exception->getMessage(), "\n";
    }

    public function getResponse():string{
        $test_string = "Enter test string here...";
        $pattern = '';
        $matches = array('no matches found');
        $matches_count = 0;
        $groups = null;

        $values = array(
            'test_string' =>$test_string, 'pattern'=>$pattern,
            'matches'=>$matches, 'matches_count'=>$matches_count, 'groups'=>$groups);
        $view = $this->blade->view('helpers.regex', $values);
        return $view;
    }

   public function getPostResponse(Request $request){
        $test_string = $request->get('test_string');
        $pattern = trim($request->get('pattern'));

        $helper = new RegExHelper();
        $matches = $helper->search_all($pattern, $test_string);
//        return new Response("Post of '$test_string' was a success ...<br>output is:<br>$matches");
        if ($matches and empty($matches[0]) === false){
            $matches2 = $matches[0];
            $matches_count = count($matches2);
            $groups =  empty($matches[1]) === false ? $matches[1] : array('no groups found');

        } else {
            $matches2 = array('no matches found');
            $matches_count = 0;
            $groups = null;
        }
        $values = array(
            'test_string' =>$test_string, 'pattern'=>$pattern,
            'matches'=>$matches2, 'matches_count'=>$matches_count, 'groups'=>$groups);
        $view = $this->blade->view('helpers.regex', $values);
        return $view;
    }
}
