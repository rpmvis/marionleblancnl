<?php

namespace Studio\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Studio\Controllers\Helpers\Visitor;
use app\Helpers\MenuHelper;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class ContactController extends BaseController implements ControllerProviderInterface{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];
        $controllers->get('/{tab_menu}', function (Request $request) {
            return $this->getResponse($request);}
        )
            ->value('tab_menu', 'contact')
            ->bind('page.contact');
        $controllers->post('/studio_visit', function () {
            return $this->handlePost();}
        );
        return $controllers;
    }

    public function getResponse(Request $request):string{
        // set tabmenu_items
        // $active_tabmenu: 'about_the_work','geometry','publications', 'literature'
        $main_menu = $this->context['active_menu']; // 'contact'
        $active_tabmenu = $request->get('tab_menu');
        $tabmenu_items = $this->menuHelper->getTabMenuItems($main_menu, $active_tabmenu, $this->locale);
        $this->context['tabmenu_items'] = $tabmenu_items;

        // get header
        $key1 = "$main_menu.$active_tabmenu";
        $header = $this->helper->transValue($key1, 'header');
        $values['table_header'] = $header;

        switch ($active_tabmenu){
            case 'studio_visit':
                $viewname = 'pages.visitor_visit_request';
                // $values['table_rows']: no translation, no need for table_rows
                // all is handled in dutch page 'studio_visit'
                $data = array('context' => $this->context, 'values'=> $values);
                break;
            default:
                // set view data
                $this->context['form_type'] = $key1;

                // get rows
                $field_names = array('descr');
                $rows = $this->helper->transRows($key1, $field_names, 'ASC');

                // replace %BlueDot%
                if ($active_tabmenu === 'links'){
                    $find = '%BlueDot%';
                    $replace = $this->app->url('/').'web/resources/appImages/BlueDot.gif';

                    for ($i=0; $i < count($rows) ; $i++){
                        $row = $rows[$i];
                        foreach($field_names as $name){
                            $row[$name] = str_replace($find, $replace, $row[$name]);
                        }
                        $rows[$i] = $row;
                    }
                }

                $values['table_field_names'] = $field_names;
                $values['table_rows'] = $rows;
                $data = array('context' => $this->context, 'values'=> $values);

                $viewname = 'layouts.tabular';
                break;
        }

        // return view
        $view = $this->app['blade']->view($viewname, $data);
        return $view;
    }

    function handlePost():Response{
        $visitor = new Visitor($this->app, $this->context);

        $errmsg = $visitor->validate();
        if ($errmsg === null) { /* ok */ }
        else {return new Response($errmsg);}

        // mail to visitor
        $body = "<link href='" . $this->context['url_CSS'] . "' rel='stylesheet'  type='text/css'/>\r\n";
        $body .= $visitor->visitor_visit_confirmed_html();

        $config_prms = $this->app['config']['parameters'];
        $msg = \Swift_Message::newInstance();
        $msg->setSubject('Ontvangstbevestiging aanvraag atelierbezoek');
        $msg->setFrom(array($config_prms['mail.noreply']));
        $msg->setTo(array($visitor->bezEmail ));

        $msg->setBody($body , 'text/html'); // add html content
        $msg->addPart($visitor->artist_visit_confirmed_plain(), 'text/plain'); // add plain text
        $this->app['mailer']->send($msg);

        // mail to artist
        $msg = \Swift_Message::newInstance();
        $msg->setSubject('Aanvraag atelierbezoek via web site');
        $msg->setFrom(array($config_prms['mail.noreply']));
        $msg->setTo(array($config_prms['mail.username']));
        $msg->setBody($body , 'text/html'); // add html content
        $msg->addPart($visitor->artist_visit_confirmed_plain(), 'text/plain'); // add plain text
        $this->app['mailer']->send($msg);

        // response sent to web page
        $response = $visitor->visitor_visit_confirmed_thanks_html();
        return new Response($response, 201);
    }
}
