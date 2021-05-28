<?php


namespace App\Tests;


use App\Entity\Trick;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CommentsTest extends WebTestCase
{
    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws TransactionRequiredException
     */
    public function testAddCommentUserLogin(){
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get("router");
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");
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