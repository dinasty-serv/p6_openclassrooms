<?php


namespace App\Controller;


use App\Entity\Image;
use App\Entity\Video;
use App\Form\CommentType;
use App\Form\DeleteImageType;
use App\Form\DeleteTrickType;
use App\Form\DeleteVideoType;
use App\Form\ImgType;
use App\Form\TrickType;
use App\Entity\Category;
use App\Form\VideoType;
use App\Service\VideoService;
use App\Util\Util;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Trick;
use App\Service\Uploader;
class TrickController extends AbstractController
{
    /**
     * @Route("/", name="app_home_page")
     * @return Response
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $tricksSlide = $em->getRepository(Trick::class)->findBy([],['id' => 'DESC'],3);
        $tricks = $em->getRepository(Trick::class)->findBy([],['id' => 'DESC'],12);
        return $this->render('trick/home.html.twig', ['trickSlide' => $tricksSlide, 'tricks' => $tricks]);
    }
    /**
     * @Route("/trick/view/{slug}", name="app_trick_view", requirements={"slug"="[a-zA-Z1-9\-_\/]+"})
     * @param Trick $trick
     * @param Request $request
     * @return Response
     */
    public function view(Trick $trick, Request $request):Response
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(CommentType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $comment = $form->getData();
            $comment->setTrick($trick);
            $comment->setUser($this->getUser());
            $em->persist($comment);
            $em->flush();
            $this->addFlash('success', "Votre commentaire a bien été ajouté.");
            return $this->redirectToRoute('app_trick_view', ['slug' => $trick->getSlug()]);
        }
        return $this->render('trick/view.html.twig', ['trick' => $trick, 'form' => $form->createView()]);
    }
    /**
     * @Route("/tricks/{slug_category}", name="app_trick_view_all")
     * @param string|null $slug_category
     * @return Response
     */
    public function viewAllTricks(string $slug_category = null):Response
    {
        $em = $this->getDoctrine()->getManager();
        if ($slug_category){
            $category = $em->getRepository(Category::class)->findOneBy(['slug' => $slug_category]);
            if (!$category) {
                throw $this->createNotFoundException();
            }
            $tricks = $em->getRepository(Trick::class)->findBy(['category' => $category],null,9);
        }else{
            $tricks = $em->getRepository(Trick::class)->findBy([],null,9);
        }
        return $this->render('trick/view_all_tricks.html.twig',['tricks' => $tricks]);
    }
    /**
     * @Route("/trick/create", name="app_trick_create")
     * @param Request $request
     * @param Util $util
     * @param Uploader $uploader
     * @return Response
     */
    public function create(Request $request, Util $util, Uploader $uploader):Response
    {
        $this->denyAccessUnlessGranted('create');

        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(TrickType::class, null,['action' => 'create']);
        $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()){
                $trick = $form->getData();
                $slug = $util->getSlug($trick->getName());
                $trick->setSlug($slug);
                $trick->setUser($this->getUser());
                $media = $uploader->saveImage($form->get('imgDefault')->getData());
                $trick->setImgDefault($media);
                $em->persist($trick);
                $media->setTrick($trick);
                $em->flush();
                $this->addFlash('success','Le trick a bien été ajouté');
                return $this->redirectToRoute('app_trick_view',['slug' => $trick->getSlug()]);
            }
        return $this->render('trick/create.html.twig',['form' =>$form->createView()]);
    }
    /**
     * @Route("/trick/edit/{id}/", name="app_trick_edit", requirements={"id"="\d+"})
     * @param Trick $trick
     * @param Request $request
     * @return Response
     */
    public function edit(Trick $trick, Request $request):Response
    {
        $this->denyAccessUnlessGranted('edit', $trick);

        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()){
                $trick = $form->getData();
                $em->persist($trick);
                $em->flush();
                $this->addFlash('success','Le trick a bien été modifié');
                return $this->redirectToRoute('app_trick_view',['slug' => $trick->getSlug()]);
            }
        return $this->render('trick/edit.html.twig',['trick' => $trick, 'form' =>$form->createView()]);
    }
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
     * @Route("/trick/{id}/add/video/", name="app_trick_add_video", requirements={"id"="\d+"})
     * @param Trick $trick
     * @param Request $request
     * @param VideoService $videoService
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
     * @param VideoService $videoService
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
    /**
     * @Route("/trick/delete/{id}", name="app_trick_delete", requirements={"id"="\d+"})
     * @param Trick $trick
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function deleteTrick(Trick $trick, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('delete', $trick);

        $form = $this->createForm(DeleteTrickType::class, $trick);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $trick = $form->getData();
            $entityManager->remove($trick);
            $entityManager->flush();
            $this->addFlash('success', "Le trick a bien été supprimer !");
            return $this->redirectToRoute('app_trick_view_all');
        }
        return $this->render('module/form_delete_trick.html.twig',['form' => $form->createView(), 'trick' => $trick] );
    }
    /**
     * @Route("/loadCategory/", name="app_trick_view_category")
     * @return Response
     */
    public function category(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository(Category::class)->findAll();
        return $this->render('module/category.html.twig', ['category' => $category]);
    }
}