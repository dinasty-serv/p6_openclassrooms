<?php


namespace App\Tests;


use App\Entity\Trick;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TricksTest extends WebTestCase
{
    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public function testEditTrick(){
        $client = static::createClient();

        $urlGenerator = $client->getContainer()->get("router");

        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        $user = $entityManager->find(User::class, 1);
        $trick = $entityManager->find(Trick::class, 1);
        $client->loginUser($user);


        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate("app_trick_edit",["id" => $trick->getId()]));

        $client->submit($crawler->filter("form[name=trick]")->form([
            "trick[name]" => "Test édit name trick",
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws TransactionRequiredException
     */
    public function testEditTrickUserNotLogin(){
        $client = static::createClient();

        $urlGenerator = $client->getContainer()->get("router");

        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        $trick = $entityManager->find(Trick::class, 1);


       $client->request(Request::METHOD_GET, $urlGenerator->generate("app_trick_edit",["id" => $trick->getId()]));



        $this->assertResponseRedirects($urlGenerator->generate("app_login"), Response::HTTP_FOUND);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public function testDeleteTrickUserNotOwner(){
        $client = static::createClient();

        $urlGenerator = $client->getContainer()->get("router");

        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        $user = $entityManager->find(User::class, 1);
        $trick = $entityManager->find(Trick::class, 1);
        $client->loginUser($user);

         $client->request(Request::METHOD_GET, $urlGenerator->generate("app_trick_delete",["id" => $trick->getId()]));


        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}