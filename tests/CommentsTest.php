<?php


namespace App\Tests;


use App\Entity\Trick;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CommentsTest extends WebTestCase
{
    public function testAddCommentUserLogin(){
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        /** @var User $user */
        $user = $entityManager->find(User::class, 1);
        $trick = $entityManager->find(Trick::class, 1);
        $client->loginUser($user);


        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate("app_trick_view",["slug" => $trick->getSlug()]));

        $client->submit($crawler->filter("form[name=comment]")->form([
            "comment[content]" => "test comment",

        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

}