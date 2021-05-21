<?php

namespace App\Controller;


use App\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    /**
     * @Route("/loadTrickComment/{trick}/{page}", name="app_load_tricks_comments", defaults={"page"=null})
     * @param int $trick
     * @param int $page
     * @return Response
     */
    public function loadComments(int $trick, int $page){
        $max = 5;

        $em = $this->getDoctrine()->getManager();
        $comments = $em->getRepository(Comment::class)->

        createQueryBuilder('a')
            ->where('a.trick='.$trick)
            ->setFirstResult(($page*$max)-$max)
            ->setMaxResults($max)
            ->orderBy('a.id', 'DESC');

        return $this->render('module/comment.html.twig', ['comments' => $comments->getQuery()->getResult()]);
    }
}
