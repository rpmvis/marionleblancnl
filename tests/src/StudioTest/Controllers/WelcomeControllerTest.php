<?php

use StudioTest\Controllers\WebTestCaseBase;

class WelcomeControllerTest extends WebTestCaseBase
{
    /** @dataProvider dataLanguages
     *  @param string $language */
    public function testWelcomeController(string $language)
    {
        $route = "/$language/welcome";
        $crawler = $this->getCrawler($route);

        // response is OK
        $this->responseTest();

        // string 'Welkom'
        $value = $language == 'nl' ? 'Welkom' : 'Welcome';
        $this->assertHtmlContains($value);

        // header info
        //   Marion Le Blanc beeldend kunstenaar
        $value = $language == 'nl' ? 'Marion Le Blanc beeldend kunstenaar' : 'Marion Le Blanc visual artist';
        $ul = $crawler->filter('ul')->eq(0);
        $l1 = $ul->children()->first();
        $span = $l1->children()->first()->text();
        $this->assertEquals($value, $span, "'$value' has NOT been found in html span element.");
        //  logo image
        $l2 = $ul->children()->eq(1);
        $img = $l2->children()->first();

        $ul = $crawler->filter('ul')->eq(1);
        $l1 = $ul->children()->first()->text();
        $l2 = $ul->children()->eq(1)->text();
        $l3 = $ul->children()->eq(2)->text();
        $l4 = $ul->children()->eq(3)->text();
        $l5 = $ul->children()->eq(4)->text();
        $l6 = $ul->children()->eq(5)->text();
        $l7 = $ul->children()->eq(6)->text();
        $l8 = $ul->children()->eq(7)->text();
        $l9 = $ul->children()->last()->text();

        // test menu items
        $value = $language == 'nl' ? 'Welkom' : 'Welcome';
        $this->assertContains($value, $l1, "'$value' has NOT been found in html li element.");
        $value = $language == 'nl' ? 'Galerieën I' : 'Galleries I';
        $this->assertContains($value, $l2, "'$value' has NOT been found in html li element.");
        $value = $language == 'nl' ? 'Galerieën II' : 'Galleries II';
        $this->assertContains($value, $l3, "'$value' has NOT been found in html li element.");
        $value = $language == 'nl' ? 'Exposities' : 'Exhibitions';
        $this->assertContains($value, $l4, "'$value' has NOT been found in html li element.");
        $value = $language == 'nl' ? 'Teksten over het werk' : 'Texts about the work';
        $this->assertContains($value, $l5, "'$value' has NOT been found in html li element.");
        $value = $language == 'nl' ? 'Kort CV' : 'CV';
        $this->assertContains($value, $l6, "'$value' has NOT been found in html li element.");
        $value = $language == 'nl' ? 'Contact' : 'Contact';
        $this->assertContains($value, $l7, "'$value' has NOT been found in html li element.");
        $value = $language == 'nl' ? 'english' : 'nederlands';
        $this->assertContains("$value", $l8, "'$value' has NOT been found in html li element.");
        $this->assertContains("☰", $l9, "'☰' has NOT been found in html li element.");

        // footer
        //   string "Copyright...Le Blanc"
        $div = $crawler->filterXPath('//footer/div')->first()->text();
        $this->assertRegExp("/Copyright.*Le Blanc/", $div);

        //  script
        $script = $crawler->filterXPath('//footer/scripts/script')->first()->text();
        $this->assertContains("function click_myTopnav()", $script);
        $this->assertContains("function click_myTabnav()", $script);
    }

    public function dataLanguages(){
        return array(array('nl'), array('en'));
    }
}
