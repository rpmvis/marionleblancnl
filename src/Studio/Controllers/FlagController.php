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
        $controllers->get('', function () {
            return $this->Redirect();}
        )
        ->assert('language', 'nl|en')
        ->bind('flag.redirect');
        return $controllers;
    }

    public function Redirect() : RedirectResponse{
        try{
            $url = $this->session->get("previous_route");

            if ($url !== null){
                // e.g. $url = "/exhibitions/charity"
                $pattern_flag = "/^\/nl|en\/flag/";
                if (preg_match($pattern_flag, $url) !== 0)
                    $url = $this->urlGenerator->generate('/');
                else
                    $url = $this->urlGenerator->generate('/');
            } else {
                $url = $this->urlGenerator->generate('/');
            }

        }
        catch (\Exception $exc){
            // e.g. $url = "http://s.com/welcome"
            $url = $this->urlGenerator->generate('/');
        }

        return  $this->redirect->redirect($url);
    }
}

