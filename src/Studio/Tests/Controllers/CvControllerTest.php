<?php

use Studio\Tests\Controllers\WebTestCase_base;

class CvControllerTest extends WebTestCase_base
{
    public function testCvController()
    {
        $client = $this->createClient();
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/cv');

        // $this->assertTrue(true);
        $this->assertTrue($client->getResponse()->isOk());
        $find = 'Wat maakt zij?';
        $this->assertContains($find, $crawler->filter('body')->text(), "'$find' has NOT been found in body text.");
    }
}
