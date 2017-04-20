<?php

namespace Studio\Controllers;

use app\Helpers\Helper;
use app\Helpers\MenuHelper;
use Aea\Model\BladeProxy;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class ExhibitionsController extends BaseController implements ControllerProviderInterface{
    protected $blade;

    public function __construct(Helper $helper, MenuHelper $menuHelper, BladeProxy $blade){
        parent::__construct($helper, $menuHelper);

        $this->blade = $blade;
    }

    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];
        $route = '/{language}/exhibitions/{tab_menu}';
        $controllers->get($route, function (Request $request) {
            return $this->getResponse($request);}
        )
        ->assert('language', 'nl|en')
        ->value('language', 'nl')
        ->value('tab_menu', 'group')
        ->bind('page.exhibitions');
        return $controllers;
    }

    public function getResponse(Request $request):string{
        $this->setContext();

        // set tabmenu_items
        $main_menu = $this->menu_context['active_menu']; // exhibitions
        $active_tabmenu = $request->get('tab_menu');
        $tabmenu_items = $this->menuHelper->getTabMenuItems($main_menu, $active_tabmenu);
        $this->menu_context['tabmenu_items'] = $tabmenu_items;

        // get header
        $key1 = "exhibitions." . $active_tabmenu;
        $header = $this->helper->transValue($key1, 'header');
        $this->context['form_type'] = $key1;

        // get rows
        $field_names = array('period', 'descr');
        $rows = $this->helper->transRows($key1, $field_names, 'DESC');

        $values['table_header'] = $header;
        $values['table_field_names'] = $field_names;
        $values['table_rows'] = $rows;
        $data = array('context' => $this->context, 'menu_context' => $this->menu_context, 'values'=> $values);

        // return view
        $view = $this->blade->view('layouts.tabular', $data);
        return $view;
    }
}
