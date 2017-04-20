<?php

use StudioTest\Controllers\WebTestCaseBase;

class AboutTheWorkControllerTest extends WebTestCaseBase
{
    /** @dataProvider dataLanguages
     *  @param string $language */
    public function testAboutTheWorkController_about_the_work(string $language)
    {
        $route = "/$language/about_the_work/about_the_work";
        $crawler = $this->getCrawler($route);

        // response is OK
        $this->responseTest();

        $value = $language == 'nl' ? 'De kracht van stilte' : 'The Strength of Silence';
        $this->assertHtmlContains($value);
    }

    /** @dataProvider dataLanguages
     *  @param string $language */
    public function testAboutTheWorkController_about_the_work_geometry(string $language)
    {
        $route = "/$language/about_the_work/geometry";
        $crawler = $this->getCrawler($route);

        // response is OK
        $this->responseTest();

        $value = $language == 'nl' ? 'inspiratiebron' : 'source of inspiration';
        $this->assertHtmlContains($value);
    }

    /** @dataProvider dataLanguages
     *  @param string $language */
    public function testAboutTheWorkController_about_the_work_publications(string $language)
    {
        $route = "/$language/about_the_work/publications";
        $crawler = $this->getCrawler($route);

        // response is OK
        $this->responseTest();

        $value = $language == 'nl' ? 'Patronen verwijzen' : 'Patterns refer';
        $this->assertHtmlContains($value);
    }

    /** @dataProvider dataLanguages
     *  @param string $language */
    public function testAboutTheWorkController_about_the_work_literature(string $language)
    {
        $route = "/$language/about_the_work/literature";
        $crawler = $this->getCrawler($route);

        // response is OK
        $this->responseTest();

        $value = $language == 'nl' ? 'Drieluik 1' : 'Triptych 1';
        $this->assertHtmlContains($value);
    }

    public function dataLanguages(){
        return array(array('nl'), array('en'));
    }
}
