<?php

namespace Studio\Controllers;

use app\MyApplication;
use app\Helpers\Helper;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Silex;
use Illuminate\View\View;

class WelcomeController extends BaseController implements ControllerProviderInterface{

    public function __construct(Helper $helper){
        parent::__construct($helper);
    }

    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->get("", function(){return $this->getResponse();}
        )
        ->bind('/');

        $controllers->get("welcome/", function(){return $this->getResponse();}
        )
        ->bind('welcome');

        return $controllers;
    }

    public function getResponse():string{
        // get header
        $key1 = "welcome";
        $header = $this->helper->transValue($key1, 'header');
        $this->context['form_type'] = $key1;

        // get rows
        $field_names = array('descr', 'img');
        $rows = $this->helper->transRows($key1, $field_names, 'ASC');

        $url = $this->app['url_generator']
            ->generate('galleries1', array('galleries'=>'galleries1'));
        $url_caption = $this->helper->transValue($key1, 'href_gallery1'); // "eerste galerij" or "first gallery"
        $url_caption = strip_tags($url_caption);

        $href = "<a href='$url' >$url_caption</a>";

        //     replace %href_gallery1% with href for first gallery
        // and replace %img_home_src% with $url2
        if (count($rows)>= 1) {
            $rows[0]['descr'] = str_replace('%href_gallery1%', $href, $rows[0]['descr']);

            $url = $this->helper->getBaseUrl();
            $url2 = $url.'/web/resources/images/_Home/Home.jpg';
            $rows[0]['img'] = str_replace('%img_home_src%', $url2, $rows[0]['img']);
        }

        $values['table_header'] = $header;
        $values['table_field_names'] = $field_names;
        $values['table_rows'] = $rows;
        $data = array('context' => $this->context, 'values'=> $values);

        // return view
        $view = $this->app['blade']->view('layouts.tabular', $data);
        return $view;
        // ***
    }
}



