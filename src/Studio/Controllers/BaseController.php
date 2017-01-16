<?php

namespace Studio\Controllers;

use app\MyApplication;
use app\Helpers\Helper;
//use Symfony\Component\HttpFoundation\RequestStack;

class BaseController{
    protected $app;
    protected $helper;
    protected $context;
    protected $locale;

    public function __construct(Helper $helper) {
        $this->helper = $helper;
        $this->app = $this->helper->getMyApp();
        $this->context = $helper->getContext();
        $this->locale = $this->context['locale'];
        $this->iniRoutes();
    }

    /**
     * Routes initialization
     * implementation can be overridden by extending classes
     * @return void
     */
    protected function iniRoutes() {

    }
}

