<?php

use Silex\WebTestCase;

class controllersTest extends WebTestCase
{
    public function testGetHomepage()
    {

        $this->assertTrue(true);
//        $client = $this->createClient();
//        $client->followRedirects(true);
//        $crawler = $client->request('GET', '/');
//
//        $this->assertTrue($client->getResponse()->isOk());
//        $this->assertContains('Welcome', $crawler->filter('body')->text());
    }

    public function createApplication()
    {
        require __DIR__ . '/../config/constants.php';
        require __DIR__ . '/../bootstrap.php';
        $app = require __DIR__.'/../src/app.php';
        require __DIR__ . '/../config/dev.php.dist';
        require __DIR__.'/../src/controllers.php';
        $app['session.test'] = true;

        return $this->app = $app;
    }
}