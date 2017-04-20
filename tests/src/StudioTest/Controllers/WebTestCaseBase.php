<?php

namespace StudioTest\Controllers;

// NO use of Silex\WebTestCase; is does NOT support the Client methods
// - enableProfiler
// - getProfile
use Silex\WebTestCase;
use Symfony\Component\HttpKernel\Client;
use tests\app\BootstrapTest;
use app\MyApplication;
use Symfony\Component\DomCrawler\Crawler;

class WebTestCaseBase extends WebTestCase
{
    protected $translator;
    private $client = null;
    private $crawler = null;

    public function createApplication()
    {
        $app = new MyApplication();
        $app['debug'] = true;
        $app['session.test'] = true;
        unset($app['exception_handler']); // unset: it is easier to get raw exceptions than HTML pages

        $bootstrap = new BootstrapTest($app);
        $bootstrap->bootstrap();

        return $app;
    }

    function getClient(): Client {
        if ($this->client == null) {
            $this->client = $this->createClient();
            $this->client->followRedirects(true);
        }
        return $this->client;
    }

    function getCrawler(string $route, string $method = 'GET'): Crawler {
        $_SERVER['REQUEST_URI'] = $route;
        $this->crawler = $this->getClient()->request($method, "http://s.com$route");
        return $this->crawler;
    }

    function responseTest($headers_contain_string = "/text\/html/"){
        $response = $this->getClient()->getResponse();
        $this->assertTrue($response->isOk(), "Response is not OK");

        // header contains "text/html"
        $this->assertRegExp($headers_contain_string, $response->headers->get('Content-Type'), "Response headers do not contain string '$headers_contain_string'");
    }

    function assertHtmlContains(string $value, $expected_count = 1){
        $actual_count = $this->crawler->filter("html:contains('$value')")->count();
        if ($actual_count === 0) $msg = "String '$value' was not found in html.";
        else $msg = "String '$value' was found $actual_count time(s) in html. Expected was $expected_count time(s).";
        $this->assertEquals($expected_count, $actual_count, $msg);
    }
}
