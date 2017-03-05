<?php

use Studio\Tests\Controllers\WebTestCase_base;

class ContactControllerTest extends WebTestCase_base
{
    public function testContactController()
    {
        $client = $this->createClient(array('REQUEST_URI' => "/contact/studio_visit"));
        $crawler = $client->request('GET', '/contact/studio_visit');

        // response is OK
        $this->assertTrue($client->getResponse()->isOk());

    }
}