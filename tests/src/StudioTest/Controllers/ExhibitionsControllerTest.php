<?php

use StudioTest\Controllers\WebTestCaseBase;

class ExhibitionsControllerTest extends WebTestCaseBase
{
    /** @dataProvider dataLanguages
     *  @param string $language */
    public function testExhibitionsController_exhibitions_group(string $language)
    {
        $route = "/$language/exhibitions/group";
        $crawler = $this->getCrawler($route);

        // response is OK
        $this->responseTest();


    }

    /** @dataProvider dataLanguages
     *  @param string $language */
    public function testExhibitionsController_exhibitions_solo(string $language)
    {
        $route = "/$language/exhibitions/solo";
        $crawler = $this->getCrawler($route);

        // response is OK
        $this->responseTest();


    }

    /** @dataProvider dataLanguages
     *  @param string $language */
    public function testExhibitionsController_exhibitions_duo(string $language)
    {
        $route = "/$language/exhibitions/duo";
        $crawler = $this->getCrawler($route);

        // response is OK
        $this->responseTest();


    }

    /** @dataProvider dataLanguages
     *  @param string $language */
    public function testExhibitionsController_exhibitions_stock(string $language)
    {
        $route = "/$language/exhibitions/stock";
        $crawler = $this->getCrawler($route);

        // response is OK
        $this->responseTest();


    }

    /** @dataProvider dataLanguages
     *  @param string $language */
    public function testExhibitionsController_exhibitions_purchases(string $language)
    {
        $route = "/$language/exhibitions/purchases";
        $crawler = $this->getCrawler($route);

        // response is OK
        $this->responseTest();


    }

    /** @dataProvider dataLanguages
     *  @param string $language */
    public function testExhibitionsController_exhibitions_assignment(string $language)
    {
        $route = "/$language/exhibitions/assignment";
        $crawler = $this->getCrawler($route);

        // response is OK
        $this->responseTest();


    }

    /** @dataProvider dataLanguages
     *  @param string $language */
    public function testExhibitionsController_exhibitions_charity(string $language)
    {
        $route = "/$language/exhibitions/charity";
        $crawler = $this->getCrawler($route);

        // response is OK
        $this->responseTest();


    }

    public function dataLanguages(){
        return array(array('nl'), array('en'));
    }
}
