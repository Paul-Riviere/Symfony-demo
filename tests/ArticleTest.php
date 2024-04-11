<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ArticleTest extends WebTestCase
{
    protected function createAuthenticatedClient($username = 'DwightBernard0', $password = 'password')
    {
        $client = static::createClient();
        $client->request(
        'POST',
        '/api/login_check',
        [],
        [],
        ['CONTENT_TYPE' => 'application/json'],
        json_encode([
            'username' => $username,
            'password' => $password,
        ])
        );

        var_dump($client->getResponse());

        $data = json_decode($client->getResponse()->getContent(), true);

        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

        return $client;
    }

    public function testGetArticles(): void
    {
        $client = $this->createAuthenticatedClient();

        $crawler = $client->request('GET', '/api/articles');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertIsArray($data);
        $this->assertEquals(count($data), 4);
    }

    public function testGetArticlesNotAuthenticated(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/api/articles');

        $this->assertResponseStatusCodeSame(401);
    }
}
