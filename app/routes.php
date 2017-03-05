<?php
// app/routes.php

use Studio\Controllers\RegExController;
use Studio\Controllers\WelcomeController;
use Studio\Controllers\GalleryController;
use Studio\Controllers\ExhibitionsController;
use Studio\Controllers\AboutTheWorkController;
use Studio\Controllers\CvController;
use Studio\Controllers\ContactController;
use Studio\Controllers\FlagController;
use Studio\Controllers\DefaultController;
use Studio\Controllers\WorkController;
use Studio\Controllers\DemoController;
use Studio\Controllers\HtmlController;

try {
    // a) mount application controllers
    // b) mount util controllers
    // c) define route(s)

    // a) mount application controllers
    $this->my_app->mount('/', new WelcomeController($helper, $menuHelper, $url_generator, $blade));
    $this->my_app->mount('/gall/', new GalleryController($helper, $menuHelper, $url_generator, $blade, $db));
    $this->my_app->mount('/work/', new WorkController($helper, $menuHelper, $blade, $db));
    $this->my_app->mount('/exhibitions/', new ExhibitionsController($helper, $menuHelper, $blade));
    $this->my_app->mount('/about_the_work/', new AboutTheWorkController($helper, $menuHelper, $blade));
    $this->my_app->mount('/cv/', new CvController($helper, $menuHelper, $url_generator, $blade));
    $this->my_app->mount('/contact/', new ContactController($helper, $menuHelper, $url_generator, $visitor, $mailer, $blade, $config_parameters));
    $this->my_app->mount('/{language}/flag/', new FlagController($helper, $menuHelper, $session, $url_generator, $redirect));

    // b) mount util controllers
    $this->my_app->mount('/default/', new DefaultController($helper, $menuHelper, $blade));
    $this->my_app->mount('/demo/', new DemoController($helper, $menuHelper));
    $this->my_app->mount('/regex/', new RegExController($blade));
    $this->my_app->mount('/html_helper/', new HtmlController($blade));

    // c) define route(s)
    $this->my_app->get('/test_CSS', function() use ($helper)  {
        $values = array('name'=>'Rene');
        $data = array('context' => $helper->getContext(), 'values'=> $values);
        $view = $this->my_app['blade']->view('test_CSS', $data);
        return $view;
    });

    // redirect request for favicon to index.php
    $this->my_app->get('testje', function() {
        return 'dit is een route testje';
    });

    // redirect request for favicon to index.php
    $this->my_app->get('*', function() {
        return 'index.php';
    });

} catch (\Exception $exc) {
    echo $exc->getMessage();
}
