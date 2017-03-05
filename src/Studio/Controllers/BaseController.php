<?php

namespace Studio\Controllers;

use app\Helpers\Helper;
use app\Helpers\MenuHelper;

class BaseController{
    protected $helper;
    protected $context;
    protected $menu_context;
    protected $locale;
    protected $menuHelper;

    public function __construct(Helper $helper, MenuHelper $menuHelper) {
        $this->helper = $helper;
        $this->menuHelper= $menuHelper;
        $this->context = $helper->getContext();
        $this->menu_context = $menuHelper->getMenuContext();
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

