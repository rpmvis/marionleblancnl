<?php

namespace Studio\Controllers;

use app\Helpers\MenuHelper;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class CvController extends BaseController implements ControllerProviderInterface{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];
        $controllers->get('{tab_menu}', function (Request $request) {
            return $this->getResponse($request);}
        )
            ->value('tab_menu', 'cv')
            ->assert('tab_menu', '/^cv$/')
            ->bind('page.cv');
        return $controllers;
    }

    public function getResponse(Request $request):string{
        // set tabmenu_items
        $menuHelper = new MenuHelper($this->app, $this->context);
        $main_menu = $this->context['active_menu'];
        $active_tabmenu = $request->get('tab_menu');
        $tabmenu_items = $menuHelper->getTabMenuItems($main_menu, $active_tabmenu);
        $this->context['tabmenu_items'] = $tabmenu_items;

        // set view data
        $key1 = "cv";
        $this->context['form_type'] = $key1;

        // get header
        $header = $this->helper->transValue($key1, 'header');
        // get rows
        $field_names = array('descr');
        $rows = $this->helper->transRows($key1, $field_names, 'ASC');

        // replace %BlueDot% + %img_Portret%
        $find = array('%BlueDot%', '%img_Portret%');
        $replace = array($this->app->url('/').'web/resources/appImages/BlueDot.gif'
                        ,$this->app->url('/').'web/resources/appImages/Portret.jpg');

        for ($i=0; $i < count($find) ; $i++) {
            for ($j = 0; $j < count($rows); $j++) {
                $row = $rows[$j];
                foreach ($field_names as $name) {
                    $row[$name] = str_replace($find[$i], $replace[$i], $row[$name]);
                }
                $rows[$j] = $row;
            }
        }

        $values['table_header'] = $header;
        $values['table_field_names'] = $field_names;
        $values['table_rows'] = $rows;
        $data = array('context' => $this->context, 'values'=> $values);

        // return view
        $view = $this->app['blade']->view('layouts.tabular', $data);
        return $view;
    }
}
