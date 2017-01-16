<?php

namespace Studio\Controllers;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use app\Helpers\HtmlHelper;

class HtmlController extends BaseController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];
        $controllers->get('', function () {
                return $this->getResponse();
            }
        )
        ->bind('get.page.html_helper');

        $controllers->post('', function(Request $request){
            return $this->postResponse($request);
        }
        )->bind('post.page.html_helper');
        return $controllers;
    }

    public function getResponse():string{
        $view = $this->app['blade']->view('helpers.html_helper');
        return $view;
    }

    public function postResponse(Request $request):string{
        $html_input = $request-> request->get('html_input');
        $helper = new HtmlHelper();
        $html_output = $helper->convertTable($html_input);

        $values = array('html_input' =>$html_input, 'html_output'=>$html_output);
        $view = $this->app['blade']->view('helpers.html_helper', $values);
        return $view;
    }
}
