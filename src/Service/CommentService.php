<?php


namespace App\Service;


use App\Entity\Trick;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CommentService
{
    private $user;

    private $em;

    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $em)
    {
        $this->user = $tokenStorage->getToken()->getUser();
        $this->em = $em;
    }

    /**
     * @param $data
     * @param Trick $trick
     */
    public function newComment($data, Trick $trick)
    {
        $comment = $data->getData();
        $comment->setTrick($trick);
        $comment->setUser($this->user);
        $this->em->persist($comment);
        $this->em->flush();

    }
}