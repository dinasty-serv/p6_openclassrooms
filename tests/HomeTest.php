<?php


namespace App\Tests;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeTest extends WebTestCase
{
    public function test(){
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

    }

}