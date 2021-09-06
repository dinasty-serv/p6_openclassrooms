<?php
namespace App\Controller;


use App\Entity\Trick;
use App\Entity\Video;
use App\Form\DeleteVideoType;
use App\Form\VideoType;
use App\Service\CommentService;
use App\Service\VideoService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VideoController extends AbstractController
{

    /**
     * @Route("/trick/{id}/add/video/", name="app_trick_add_video", requirements={"id"="\d+"})
     * @param Trick $trick
     * @param Request $request
     * @param CommentService $videoService
     * @return Response
     */
    public function addVideoTrick(Trick $trick, Request $request, VideoService $videoService): Response
    {
        $this->denyAccessUnlessGranted('edit', $trick);
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(VideoType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $video = $form->getData();
            $video->setTrick($trick);
            $video =  $videoService->getUrl($video);
            $em->persist($video);
            $em->flush();
            $this->addFlash('success', "Votre vidéo a bien été ajouté.");
            return $this->redirectToRoute('app_trick_view', ['slug' => $trick->getSlug()]);
        }
        return $this->render('module/form_video.html.twig', ['form' => $form->createView(), 'id' => $trick->getId(),'route' => 'app_trick_add_video']);
    }

    /**
     * @Route("/trick/{id}/edit/video/{video_id}", name="app_trick_edit_video", requirements={"id"="\d+","video_id"="\d+"})
     * @param Trick $trick
     * @param int $video_id
     * @param Request $request
     * @param CommentService $videoService
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function editVideoTrick(Trick $trick, int $video_id, Request $request, VideoService $videoService, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('edit', $trick);
        $video = $em->getRepository(Video::class)->find($video_id);
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $video = $form->getData();
            $video->setTrick($trick);
            $video =  $videoService->getUrl($video);
            $em->persist($video);
            $em->flush();
            $this->addFlash('success', "Votre vidéo a bien été modifié.");
            return $this->redirectToRoute('app_trick_view', ['slug' => $trick->getSlug()]);
        }
        return $this->render('module/form_video.html.twig', ['form' => $form->createView(), 'id' => $trick->getId(),'route' => 'app_trick_add_video']);
    }

    /**
     * @Route("/trick/video/{id}/delete",name="app_trick_delete_video",requirements={"id"="\d+"})
     * @param Video $video
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function deleteVideo(Video $video, Request $request, EntityManagerInterface $entityManager): Response
    {

        $trick = $video->getTrick();
        $this->denyAccessUnlessGranted('edit', $trick);

        $form = $this->createForm(DeleteVideoType::class, $video);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $video = $form->getData();
            $entityManager->remove($video);
            $entityManager->flush();
            $this->addFlash('success', "La video a bien été supprimer.");
            return $this->redirectToRoute('app_trick_view', ['slug' => $trick->getSlug()]);
        }
        return $this->render('module/form_delete_video.html.twig',['form' => $form->createView(), 'video' => $video] );
    }
}
