<?php

namespace Studio\Controllers;

use app\Helpers\Helper;
use app\Helpers\MenuHelper;
use Aea\Model\BladeProxy;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;

class WelcomeController extends BaseController implements ControllerProviderInterface{
    protected $urlGenerator;
    protected $blade;

    public function __construct(Helper $helper, MenuHelper $menuHelper, UrlGenerator $urlGenerator, BladeProxy $blade){
        parent::__construct($helper, $menuHelper);
        $this->urlGenerator = $urlGenerator;
        $this->blade = $blade;
    }

    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $route = '/';
        $controller = $controllers->get($route, function(){return $this->getResponse();}
        )->bind('/');

        $route = '/{language}/welcome/';
        $controllers->get($route, function(){return $this->getResponse();}
        )
        ->assert('language', 'nl|en')
        ->value('language', 'nl')
        ->bind('welcome');

        return $controllers;
    }

    public function getResponse():string{
        // get header
        $this->setContext();

        $key1 = "welcome";
        $header = $this->helper->transValue($key1, 'header');
        $this->context['form_type'] = $key1;

        // get rows
        $field_names = array('descr', 'img');
        $rows = $this->helper->transRows($key1, $field_names, 'ASC');

        $url = $this->urlGenerator
            ->generate('galleries1', array('language'=> $this->locale, 'galleries'=>'galleries1'));
        $url_caption = $this->helper->transValue($key1, 'href_gallery1'); // "eerste galerij" or "first gallery"
        $url_caption = strip_tags($url_caption);

        $href = "<a href='$url' >$url_caption</a>";

        if (count($rows)>= 1) {
            // replace %href_gallery1% with href for first gallery
            $rows[0]['descr'] = str_replace('%href_gallery1%', $href, $rows[0]['descr']);

            // replace %img_home_src% with $url2
            $url = $this->helper->getBaseUrl();
            $url2 = $url.'/web/resources/images/_Home/Home.jpg';
            $rows[0]['img'] = str_replace('%img_home_src%', $url2, $rows[0]['img']);
        }

        $values['table_header'] = $header;
        $values['table_field_names'] = $field_names;
        $values['table_rows'] = $rows;
        $data = array('context' => $this->context, 'menu_context' => $this->menu_context, 'values'=> $values);

        // return view
        $view = $this->blade->view('layouts.tabular', $data);
        return $view;
        // ***
    }
}



