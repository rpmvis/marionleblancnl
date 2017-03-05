<?php

namespace Studio\Controllers;

use Symfony\Component\HttpFoundation\Response;
use app\Helpers\Helper;
use app\Helpers\MenuHelper;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Swift_Mailer;
use Aea\Model\BladeProxy;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Studio\Models\Visitor;

class ContactController extends BaseController implements ControllerProviderInterface{
    protected $urlGenerator;
    protected $visitor;
    protected $mailer;
    protected $blade;
    protected $config_parameters;

    public function __construct(Helper $helper, MenuHelper $menuHelper, UrlGenerator $urlGenerator,  Visitor $visitor, 
                                Swift_Mailer $mailer, BladeProxy $blade, array $config_parameters){
        parent::__construct($helper, $menuHelper);

        $this->urlGenerator = $urlGenerator;
        $this->visitor = $visitor;
        $this->mailer = $mailer;
        $this->blade = $blade;
        $this->config_parameters = $config_parameters;
    }

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
        $main_menu = $this->menu_context['active_menu']; // 'contact'
        $active_tabmenu = $request->get('tab_menu');
        $tabmenu_items = $this->menuHelper->getTabMenuItems($main_menu, $active_tabmenu, $this->locale);
        $this->menu_context['tabmenu_items'] = $tabmenu_items;

        // get header
        $key1 = "$main_menu.$active_tabmenu";
        $header = $this->helper->transValue($key1, 'header');
        $values['table_header'] = $header;

        switch ($active_tabmenu){
            case 'studio_visit':
                $viewname = 'pages.visitor_visit_request';
                // $values['table_rows']: no translation, no need for table_rows
                // all is handled in dutch page 'studio_visit'
                $data = array('context' => $this->context, 'menu_context' => $this->menu_context, 'values'=> $values);
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
                    $replace = $this->urlGenerator->generate('/') .'web/resources/appImages/BlueDot.gif';

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
                $data = array('context' => $this->context, 'menu_context' => $this->menu_context, 'values'=> $values);

                $viewname = 'layouts.tabular';
                break;
        }

        // return view
        $view = $this->blade->view($viewname, $data);
        return $view;
    }

    function handlePost():Response{

        $errmsg = $this->visitor->validate();
        if ($errmsg === '') { /* ok */ }
        else {return new Response($errmsg);}

        // mail to visitor
        $body = "<link href='" . $this->context['url_CSS'] . "' rel='stylesheet'  type='text/css'/>\r\n";
        $body .= $this->visitor->visitor_visit_confirmed_html();

        $cfg_prms = $this->config_parameters;
        $msg = \Swift_Message::newInstance();
        $msg->setSubject('Ontvangstbevestiging aanvraag atelierbezoek');
        $msg->setFrom(array($cfg_prms['mail.noreply']));
        $msg->setTo(array($this->visitor->bezEmail ));

        $msg->setBody($body , 'text/html'); // add html content
        $msg->addPart($this->visitor->artist_visit_confirmed_plain(), 'text/plain'); // add plain text
        $this->mailer->send($msg);

        // mail to artist
        $msg = \Swift_Message::newInstance();
        $msg->setSubject('Aanvraag atelierbezoek via web site');
        $msg->setFrom(array($cfg_prms['mail.noreply']));
        $msg->setTo(array($cfg_prms['mail.username']));
        $msg->setBody($body , 'text/html'); // add html content
        $msg->addPart($this->visitor->artist_visit_confirmed_plain(), 'text/plain'); // add plain text
        $this->mailer->send($msg);

        // response sent to web page
        $response = $this->visitor->visitor_visit_confirmed_thanks_html();
        return new Response($response, 201);
    }
}
