<?php

use StudioTest\Controllers\WebTestCaseBase;

class WorkControllerTest extends WebTestCaseBase
{
    /** @dataProvider dataLanguages
     *  @param string $language
     *  @param string $work_route
     *  @param string $bgcolor
     */
    public function testWorkController_galleries1(string $language, string $work_route, string $bgcolor)
    {
        // $route: nl/work/gall/galleries1/gallery1/g/1/2014-s-07/%23FFFFFF?work=work
        $route = "/$language/$work_route/$bgcolor";
        $crawler = $this->getCrawler($route);

        // response is OK
        $this->responseTest();

        $value = $language == 'nl' ? 'Terug' : 'Back';
        $this->assertHtmlContains($value);
    }

    public function dataLanguages(){
        return array(
//            {language}/work/{main_menu}    /{tab_menu}/{gallery_type}/{slice_nr}/{code}/   {bgcolor}
//                    nl/work/gall/galleries1/gallery1  /g             /1          /2014-s-07/%23FFFFFF
            array('nl', 'work/gall/galleries1/gallery1/g/1/2014-s-07', '%23FFFFFF'),
            array('nl', 'work/gall/galleries1/gallery1/g/1/2014-s-07', '%23FFFFD7'),
            array('en', 'work/gall/galleries1/gallery1/g/1/2014-s-07', '%23FFFFFF'),
            array('en', 'work/gall/galleries1/gallery1/g/1/2014-s-07', '%23FFFFD7')
        );
    }
}

