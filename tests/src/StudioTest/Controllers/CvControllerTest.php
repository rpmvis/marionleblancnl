<?php

use StudioTest\Controllers\WebTestCaseBase;

class CvControllerTest extends WebTestCaseBase
{
    /** @dataProvider dataLanguages
     *  @param string $language */
    public function testGalleryController(string $language)
    {
        $route = "/$language/cv";
        $crawler = $this->getCrawler($route);

        // response is OK
        $this->responseTest();

        $value = $language == 'nl' ? 'Wat maakt zij' : 'What does she produce';
        $this->assertHtmlContains($value);
    }

    public function dataLanguages(){
        return array(array('nl'), array('en'));
    }
}