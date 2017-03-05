<?php

namespace Studio\Controllers;

use app\Helpers\Helper;
use app\Helpers\MenuHelper;
use Aea\Model\BladeProxy;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class AboutTheWorkController extends BaseController implements ControllerProviderInterface{
    protected $blade;

    public function __construct(Helper $helper, MenuHelper $menuHelper, BladeProxy $blade){
        parent::__construct($helper, $menuHelper);
        $this->blade = $blade;
    }

    public function connect(Application $app)

    {
        $controllers = $app['controllers_factory'];
        $controllers->get('/{tab_menu}', function (Request $request) {
            return $this->getResponse($request);}
        )
            ->value('tab_menu', 'about_the_work')
            ->bind('page.about_the_work');
        return $controllers;
    }

    public function getResponse(Request $request):string{
        // a) set context
        // - context tabmenu_items
        //   $active_tabmenu: 'about_the_work','geometry','publications', 'literature'
        $main_menu = $this->menu_context['active_menu'];
        $active_tabmenu = $request->get('tab_menu');
        $tabmenu_items = $this->menuHelper->getTabMenuItems($main_menu, $active_tabmenu, $this->locale);
        $this->menu_context['tabmenu_items'] = $tabmenu_items;

        // - context form_type
        $key1 = "about_the_work." . $active_tabmenu;
        $header = $this->helper->transValue($key1, 'header');
        $this->context['form_type'] = $key1;

        // b) set rows
        switch ($active_tabmenu){
            case 'about_the_work':
                $field_names = array('descr');
                $order = 'ASC';
                break;
            case 'geometry':
                $field_names = array('descr', 'descr2');
                $order = 'ASC';
                break;
            case 'publications':
                $field_names = array('period', 'descr');
                $order = 'DESC';
                break;
            case 'literature':
                $field_names = array('descr', 'descr2');
                $order = 'ASC';
                break;
            default:
                throw new \Exception('AboutTheWorkController:getResponse: unknown $about_the_work_type!');
        }

        $rows = $this->helper->transRows($key1, $field_names, $order);

        // c) set view data
        $values['table_header'] = $header;
        $values['table_field_names'] = $field_names;
        $values['table_rows'] = $rows;
        $view_data = array('context' => $this->context, 'menu_context' => $this->menu_context, 'values'=> $values);

        // d) return view
        $view = $this->blade->view('layouts.tabular', $view_data);
        return $view;
    }
}
