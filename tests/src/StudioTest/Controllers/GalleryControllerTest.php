<?php

use StudioTest\Controllers\WebTestCaseBase;

class GalleryControllerTest extends WebTestCaseBase
{
    /** @dataProvider dataLanguages
     *  @param string $language */
    public function testGalleryController_galleries1(string $language)
    {
        $route = "/$language/gall/galleries1";
        $crawler = $this->getCrawler($route);

        // response is OK
        $this->responseTest();

        $value = $language == 'nl' ? 'Galerie 1' : 'Gallery 1';
        $this->assertHtmlContains($value);
    }

    /** @dataProvider dataLanguages
     *  @param string $language */
    public function testGalleryController_galleries2(string $language)
    {
        $route = "/$language/gall/galleries2";
        $crawler = $this->getCrawler($route);

        // response is OK
        $this->responseTest();

        $value = $language == 'nl' ? 'Kamerschermen' : 'Chamber screens';
        $this->assertHtmlContains($value);
    }

    public function dataLanguages(){
        return array(array('nl'), array('en'));
    }
}

