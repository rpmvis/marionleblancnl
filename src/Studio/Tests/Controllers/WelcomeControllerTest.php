<?php

use Studio\Tests\Controllers\WebTestCase_base;

class WelcomeControllerTest extends WebTestCase_base
{
    public function testWelcomeController()
    {
        $client = $this->createClient();
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/');

        // response is OK
        $this->assertTrue($client->getResponse()->isOk());

        // header contains "text/html"
        $this->assertRegExp("/text\/html/", $client->getResponse()->headers->get('Content-Type'));

        // string 'Welkom' (1st method)
        $find = 'Welkom';
        $this->assertContains($find, $crawler->filter('body')->text(), "'$find' has NOT been found in body text.");

        // string 'Welkom' (2nd method
        $actual = $crawler->filter('html:contains("Welkom")')->count();
        $expected = 1;
        $this->assertGreaterThanOrEqual($expected, $actual);

        // header info
        //   Marion Le Blanc beeldend kunstenaar
        $ul = $crawler->filter('ul')->eq(0);
        $l1 = $ul->children()->first();
        $span = $l1->children()->first()->text();
        $this->assertEquals("Marion Le Blanc beeldend kunstenaar", $span);
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

        $this->assertContains("Welkom", $l1);
        $this->assertContains("Galerieën I", $l2);
        $this->assertContains("Galerieën II", $l3);
        $this->assertContains("Exposities", $l4);
        $this->assertContains("Teksten over het werk", $l5);
        $this->assertContains("Kort CV", $l6);
        $this->assertContains("Contact", $l7);
        $this->assertRegExp("/nederlands|english/", $l8);
        $this->assertContains("☰", $l9);

        // footer
        //   string "Copyright...Le Blanc"
        $div = $crawler->filterXPath('//footer/div')->first()->text();
        $this->assertRegExp("/Copyright.*Le Blanc/", $div);
        //   script
        $script = $crawler->filterXPath('//footer/scripts/script')->first()->text();
        $this->assertContains("function click_myTopnav()", $script);
        $this->assertContains("function click_myTabnav()", $script);
    }

//    public function testWelcomeContent(){
//    }
}
