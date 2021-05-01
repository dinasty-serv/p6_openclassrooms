<?php


namespace App\Controller;


use App\Form\CommentType;
use App\Form\ImgType;
use App\Form\TrickType;
use App\Entity\Category;
use App\Form\VideoType;
use App\Util\Util;
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
            dump($comment);
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
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()){
                $trick = $form->getData();
                $em->persist($trick);
                $em->flush();
                $this->addFlash('success','Le trick a bien été modifié');
                return $this->redirectToRoute('app_trick_view',['slug' => $trick->getSlug(), 'id' =>$trick->getId()]);
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
        $em = $this->getDoctrine()->getManager();
        //TODO add Voter
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
     * @Route("/trick/{id}/add/video/", name="app_trick_add_video", requirements={"id"="\d+"})
     * @param Trick $trick
     * @param Request $request
     * @return Response
     */
    public function addVideoTrick(Trick $trick, Request $request): Response
    {
        //TODO add Voter
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(VideoType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $video = $form->getData();
            $video->setTrick($trick);
            $em->persist($video);
            $em->flush();
            $this->addFlash('success', "Votre vidéo a bien été ajouté.");
            return $this->redirectToRoute('app_trick_view', ['slug' => $trick->getSlug()]);

        }
        return $this->render('module/form_video.html.twig', ['form' => $form->createView(), 'id' => $trick->getId(),'route' => 'app_trick_add_video']);
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
     * @return Response
     */
    public function deleteTrick(Trick $trick, Request $request): Response
    {
        //TODO add voter

        return $this->render('trick/delete.html.twig');

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