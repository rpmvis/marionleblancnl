<?php

namespace tests\Studio\Controllers;

class WelcomeControllerTest extends WebTestCase_base
{
    public function testWelcomeController()
    {
        $client = $this->createClient();
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/');

        // $this->assertTrue(true);
        $this->assertTrue($client->getResponse()->isOk());
        $find = 'Welkom';
        $this->assertContains($find, $crawler->filter('body')->text(), "'$find' has NOT been found in body text.");
    }
}
