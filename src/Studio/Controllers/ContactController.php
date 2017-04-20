<?php

namespace Studio\Controllers;

use Symfony\Component\HttpFoundation\Response;
use app\Helpers\Helper;
use app\Helpers\MenuHelper;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Aea\Model\BladeProxy;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Studio\Models\Visitor;
use app\Services\EmailServiceInterface;

class ContactController extends BaseController implements ControllerProviderInterface{
    protected $urlGenerator;
    protected $visitor;
    protected $mailer;
    protected $blade;
    protected $config_parameters;

    public function __construct(Helper $helper, MenuHelper $menuHelper, UrlGenerator $urlGenerator, Visitor $visitor,
        EmailServiceInterface $mailer, BladeProxy $blade, array $config_parameters){
        parent::__construct($helper, $menuHelper);

        $this->urlGenerator = $urlGenerator;
        $this->visitor = $visitor->getVisitor();
        $this->mailer = $mailer;
        $this->blade = $blade;
        $this->config_parameters = $config_parameters;
    }

    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];
        $route = '/{language}/contact/{tab_menu}';
        $controllers->get($route, function (Request $request) {
            // called from vendor/symfony/http-kernel/HttpKernel.phphandleRaw
            return $this->getResponse($request);}
        )
            ->assert('language', 'nl|en')->value('language', 'nl')
            ->value('tab_menu', 'contact')
            ->bind('page.contact');

        $controllers->post('/nl/contact/studio_visit', function (Request $request) {
            return $this->handlePost($request);}
        );
        return $controllers;
    }

    public function getResponse(Request $request):string{
        $this->setContext();

        // set tabmenu_items: 'contact','studio_visit','links', 'about_this_site'
        $main_menu = $this->menu_context['active_menu']; // 'contact'
        $active_tabmenu = $request->get('tab_menu');
        $tabmenu_items = $this->menuHelper->getTabMenuItems($main_menu, $active_tabmenu);
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
                switch ($active_tabmenu){
                    case 'contact':
                        if (count($rows)>= 1) {
                            // nl/contact/studio_visit: only available in nl language
                            $url = $this->urlGenerator
                                ->generate('page.contact', array('language'=> 'nl', 'tab_menu'=>'studio_visit'));
                            $url_caption = 'atelierbezoek';

                            $href = "<a href='$url' >$url_caption</a>";

                            // replace %href_atelierbezoek% with href for form atelierbezoek
                            $rows[0]['descr'] = str_replace('%href_atelierbezoek%', $href, $rows[0]['descr']);
                        }
                        break;
                    case 'links':
                        $find = '%BlueDot%';
                        $replace = $this->urlGenerator->generate('/') .'web/resources/appImages/BlueDot.gif';

                        for ($i=0; $i < count($rows) ; $i++){
                            $row = $rows[$i];
                            foreach($field_names as $name){
                                $row[$name] = str_replace($find, $replace, $row[$name]);
                            }
                            $rows[$i] = $row;
                        }
                        break;
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

    function handlePost(Request $request):Response{
        $this->visitor->setVisitor($request->request->all());

        $errmsg = $this->visitor->validate();
        if ($errmsg === '') { /* ok */ }
        else {return new Response($errmsg);}

        $this->setContext();

        // set tabmenu_items: 'contact','studio_visit','links', 'about_this_site'
        $main_menu = $this->menu_context['active_menu']; // 'contact'
        $active_tabmenu = 'studio_visit';
        $tabmenu_items = $this->menuHelper->getTabMenuItems($main_menu, $active_tabmenu);
        $this->menu_context['tabmenu_items'] = $tabmenu_items;

        // send mails
        $body_base = "<link href='" . $this->context['url_CSS'] . "' rel='stylesheet'  type='text/css'/>\r\n";
        $this->mail_to_visitor($body_base);
        $this->mail_to_artist($body_base);

        // send response sent to web page to give direct feedback to visitor
        $data = array('context' => $this->context, 'menu_context' => $this->menu_context, 'visitor'=> $this->visitor);
        $viewname = 'pages.visitor_visit_confirmed_thanks_html';
        $view = $this->blade->view($viewname, $data);
        return new Response($view, 201);
    }

    function mail_to_visitor(string $body_base){
        // mail to visitor
        $data = array('visitor' => $this->visitor);

        $body = $body_base .
                $this->blade->view('pages.visitor_visit_confirmed_html', $data);
        $part = $this->blade->view('pages.visitor_visit_confirmed_plain', $data); // pages.artist_visit_confirmed_html?

        $this->mailer->send(function(\App\Services\EmailHelper $helper) use($body, $part) {
            $helper->setMustHaveFrom(true);
            $helper->setMustHaveSubject(true);
            $msg = $helper->getMsg();
            $msg->setSubject('Ontvangstbevestiging aanvraag atelierbezoek');
            $msg->setFrom(array($this->config_parameters['mail.noreply']));
            $msg->setTo(array($this->visitor->bezEmail ));
            $msg->setBody($body, 'text/html');   // add html content
            $msg->addPart($part, 'text/plain'); // add plain text
        });
    }

    function mail_to_artist(string $body_base){
        // mail to artist
        $data = array('visitor' => $this->visitor);

        $body = $body_base .
            $this->blade->view('pages.artist_visit_confirmed_html', $data);
        $part = $this->blade->view('pages.artist_visit_confirmed_plain', $data); // pages.artist_visit_confirmed_html?

        $this->mailer->send(function(\App\Services\EmailHelper $helper) use($body, $part) {
            $helper->setMustHaveFrom(true);
            $helper->setMustHaveSubject(true);
            $msg = $helper->getMsg();
            $msg->setSubject('Aanvraag atelierbezoek via web site');
            $msg->setFrom(array($this->config_parameters['mail.noreply']));
            $msg->setTo(array($this->config_parameters['mail.username']));
            $msg->setBody($body, 'text/html'); // add html content
            $msg->addPart($part, 'text/plain'); // add plain text
        });
    }
}
