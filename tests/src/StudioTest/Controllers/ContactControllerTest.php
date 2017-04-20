<?php

use StudioTest\Controllers\WebTestCaseBase;
use \Mockery as m;
//use VisitorMock;

class ContactControllerTest extends WebTestCaseBase
{
    public function testContactController_contact_studio_visit_POST(){
        $dummy = new Studio\Models\VisitorMock();
        $visitor = $dummy->getVisitor();

        $route = "/nl/contact/studio_visit"; // this page is only in nl language
        $crawler = $this->getCrawler($route);

        // response is OK
        $this->assertTrue($this->getClient()->getResponse()->isOk());

        $form = $crawler->selectButton('submit')->form();
        $form['bezDatum'] = $visitor->bezDatum;
        $form['bezAanhef'] = $visitor->bezAanhef;
        $form['bezNaam'] = $visitor->bezNaam;
        $form['bezTelefoon'] = $visitor->bezTelefoon;
        $form['bezBelVanaf'] = $visitor->bezBelVanaf;
        $form['bezBelTot'] = $visitor->bezBelTot;
        $form['bezEmail'] = $visitor->bezEmail;
        $form['bezOpmerking'] = $visitor->bezOpmerking;

        $crawler = $this->getclient()->submit($form);

        $content = $this->getClient()->getResponse()->getContent();
        $parameters = $this->getClient()->getRequest()->request->all();
        $this->assertEquals($visitor->bezDatum, $parameters['bezDatum']);
        $this->assertEquals($visitor->bezAanhef, $parameters['bezAanhef']);
        $this->assertEquals($visitor->bezNaam, $parameters['bezNaam']);
        $this->assertEquals($visitor->bezTelefoon, $parameters['bezTelefoon']);
        $this->assertEquals($visitor->bezBelVanaf, $parameters['bezBelVanaf']);
        $this->assertEquals($visitor->bezBelTot, $parameters['bezBelTot']);
        $this->assertEquals($visitor->bezEmail, $parameters['bezEmail']);
        $this->assertEquals($visitor->bezOpmerking, $parameters['bezOpmerking']);
    }

    /** @dataProvider dataLanguages
     *  @param string $language */
    public function testContactController_contact_(string $language)
    {
        $route = "/$language/contact";
        $crawler = $this->getCrawler($route);

        // response is OK
        $this->responseTest();

        $value = $language === 'nl'? "Wilt u reageren" : "Would you like to respond";
        $this->assertHtmlContains($value);
    }

    public function testContactController_nl_contact_visit()
    {
        $route = "/nl/contact/studio_visit"; // this page is only in nl language
        $crawler = $this->getCrawler($route);

        // response is OK
        $this->assertTrue($this->getClient()->getResponse()->isOk());

        $value = 'Aanvraag atelierbezoek';
        $this->assertHtmlContains($value);
    }

    /** @dataProvider dataLanguages
     *  @param string $language */
    public function testContactController_contact_links(string $language)
    {
        $route = "/$language/contact/links"; // this page is only in nl language
        $crawler = $this->getCrawler($route);

        // response is OK
        $this->assertTrue($this->getClient()->getResponse()->isOk());

        $value = $language === 'nl'? "Internationaal Reizend Project" : "International Traveling Project";
        $this->assertHtmlContains($value);
    }

    /** @dataProvider dataLanguages
     *  @param string $language */
    public function testContactController_contact_about_this_site(string $language)
    {
        $route = "/$language/contact/about_this_site"; // this page is only in nl language
        $crawler = $this->getCrawler($route);

        // response is OK
        $this->assertTrue($this->getClient()->getResponse()->isOk());

        $value = $language === 'nl'? "site is in onderhoud" : "site is maintained";
        $this->assertHtmlContains($value);
    }

    public function dataLanguages(){
        return array(array('nl'), array('en'));
    }
}
