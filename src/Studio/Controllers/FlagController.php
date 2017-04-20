<?php

namespace Studio\Controllers;

use app\Helpers\Helper;
use app\Helpers\MenuHelper;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGenerator;
use app\Services\RedirectServiceProvider;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\RedirectResponse;


class FlagController extends BaseController implements ControllerProviderInterface{
    protected $session;
    protected $urlGenerator;
    protected $redirect;

    public function __construct(Helper $helper, MenuHelper $menuHelper, Session $session, UrlGenerator $urlGenerator, RedirectServiceProvider $redirect){
        parent::__construct($helper, $menuHelper);

        $this->session = $session;
        $this->urlGenerator = $urlGenerator;
        $this->redirect = $redirect;
    }

    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];
        $route = '/{language}/flag';
        $controllers->get($route, function () {
            return $this->Redirect();}
        )
        ->assert('language', 'nl|en')
        ->bind('flag.redirect');
        return $controllers;
    }

    public function Redirect() : RedirectResponse{
        try{
            $this->setContext();
            
            $url = $this->session->get("previous_route");

            // switch language at this point
            $locale = $this->locale;
            if (preg_match("/\/nl|en\//", $url) !== 0){
                $url2 = "/$locale/" . substr($url, 4);

                // handle exception of not translated page
                if ($url2 == '/en/contact/studio_visit') {
                    $url2 = '/en/contact/contact';
                }
            } else {
                $url2 = "/$locale/welcome";
            }
        }

        catch (\Exception $exc){
            // e.g. $url2 = "http://s.com/welcome"
            $url2 = '/';
        }

        return  $this->redirect->redirect($url2);
    }
}

