<?php
namespace App\Controller;


use App\Entity\Image;
use App\Entity\Trick;
use App\Form\DeleteImageType;
use App\Form\ImgType;
use App\Service\Uploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MediaController extends AbstractController
{
    /**
     * @Route("/trick/{id}/add/media}", name="app_trick_add_medias", requirements={"id"="\d+","media_id"="\d+" })
     * @param Trick $trick
     * @param Request $request
     * @param Uploader $uploader
     * @return Response
     */
    public function addMediasTrick(Trick $trick, Request $request, Uploader $uploader): Response
    {
        $this->denyAccessUnlessGranted('edit', $trick);

        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(ImgType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $media =  $uploader->saveImage($form->getData());
            $media->setTrick($trick);
            $em->persist($media);
            $em->flush();
            $this->addFlash('success', "Votre photo a bien été ajouté.");
            return $this->redirectToRoute('app_trick_view', ['slug' => $trick->getSlug()]);
        }
        return $this->render('module/form_media.html.twig', ['form' => $form->createView(), 'id' => $trick->getId(),'route' => 'app_trick_add_medias']);
    }

    /**
     * @Route("/trick/{id}/edit/media/{media_id}", name="app_trick_edit_medias", requirements={"id"="\d+","media_id"="\d+" })
     * @param Trick $trick
     * @param Request $request
     * @param Uploader $uploader
     * @return Response
     */
    public function editMediasTrick(Trick $trick,int $media_id, Request $request, Uploader $uploader, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('edit', $trick);
        $image = $em->getRepository(Image::class)->find($media_id);
        $form = $this->createForm(ImgType::class, $image);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $media =  $uploader->saveImage($form->getData());
            $media->setTrick($trick);
            $em->persist($media);
            $em->flush();
            $this->addFlash('success', "Votre photo a bien été modifié.");
            return $this->redirectToRoute('app_trick_view', ['slug' => $trick->getSlug()]);
        }
        return $this->render('module/form_media.html.twig', ['form' => $form->createView(), 'id' => $trick->getId(),'route' => 'app_trick_add_medias']);
    }

    /**
     * @Route("/trick/media/{id}/delete",name="app_trick_delete_media",requirements={"id"="\d+"})
     * @param Image $image
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Uploader $uploader
     * @return Response
     */
    public function deleteMedia(Image $image, Request $request, EntityManagerInterface $entityManager, Uploader $uploader): Response
    {
        $trick = $image->getTrick();
        $this->denyAccessUnlessGranted('edit', $trick);

        $form = $this->createForm(DeleteImageType::class, $image);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $image = $form->getData();
            if ($uploader->deleteImage($image)) {
                $entityManager->remove($image);

                $entityManager->flush();

                $this->addFlash('success', "L'image à bien été supperimé.");
                return $this->redirectToRoute('app_trick_view', ['slug' => $trick->getSlug()]);
            }else{
                $this->addFlash('danger', "Impossible de supprimer l'image !");
                return $this->redirectToRoute('app_trick_view', ['slug' => $trick->getSlug()]);
            }
        }
        return $this->render('module/form_delete_media.html.twig',
            ['form' => $form->createView(),
                'image' => $image
            ] );
    }

    /**
     * @Route("/trick/{id}/edit_une/", name="app_trick_edit_medias_une", requirements={"id"="\d+"})
     * @param Trick $trick
     * @param Request $request
     * @param Uploader $uploader
     * @return Response
     */
    public function editMediaUne(Trick $trick, Request $request, Uploader $uploader): Response
    {
        $this->denyAccessUnlessGranted('edit', $trick);

        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(ImgType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $media =  $uploader->saveImage($form->getData());
            $media->setTrick($trick);
            $trick->setImgDefault($media);
            $em->persist($media);
            $em->persist($trick);
            $em->flush();
            $this->addFlash('success', "L'image de couverture a bien été mise à jours.");
            return $this->redirectToRoute('app_trick_view', ['slug' => $trick->getSlug()]);
        }
        return $this->render('module/form_media.html.twig', ['form' => $form->createView(), 'id' => $trick->getId(), 'route' => 'app_trick_edit_medias_une']);
    }

}
