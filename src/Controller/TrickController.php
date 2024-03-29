<?php


namespace App\Controller;

use App\Form\CommentType;
use App\Form\DeleteTrickType;
use App\Form\TrickType;
use App\Entity\Category;
use App\Service\CommentService;
use App\Service\TrickService;
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
        $tricks = $em->getRepository(Trick::class)->findBy([],['id' => 'DESC'],8);
        return $this->render('trick/home.html.twig', ['trickSlide' => $tricksSlide, 'tricks' => $tricks]);
    }

    /**
     * @Route("/loadTrick/{page}", name="app_load_tricks", defaults={"page"=null})
     * @param int $page
     * @return Response
     */
    public function loadTricks(int $page): Response
    {
        $max = 8;
        $em = $this->getDoctrine()->getManager();
        $tricks = $em->getRepository(Trick::class)->
        createQueryBuilder('a')
                ->setFirstResult(($page*$max)-$max)
                ->setMaxResults($max)
                ->orderBy('a.id', 'DESC');

        return $this->render('module/trick.html.twig', ['tricks' => $tricks->getQuery()->getResult()]);
    }

    /**
     * @Route("/trick/view/{slug}", name="app_trick_view", requirements={"slug"="[a-zA-Z0-9-_/]+"})
     * @param Trick $trick
     * @param Request $request
     * @param CommentService $commentService
     * @return Response
     */
    public function view(Trick $trick, Request $request, CommentService $commentService):Response
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(CommentType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $commentService->newComment($form, $trick);
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
     * @param TrickService $trickService
     * @return Response
     */
    public function create(Request $request, TrickService $trickService):Response
    {
        $this->denyAccessUnlessGranted('create');

        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(TrickType::class, null,['action' => 'create']);
        $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()){
              $trick = $trickService->create($form);
                $this->addFlash('success','Le trick a bien été ajouté');
                return $this->redirectToRoute('app_trick_view',['slug' => $trick->getSlug()]);
            }
        return $this->render('trick/create.html.twig',['form' =>$form->createView()]);
    }

    /**
     * @Route("/trick/edit/{id}/", name="app_trick_edit", requirements={"id"="\d+"})
     * @param Trick $trick
     * @param Request $request
     * @param TrickService $trickService
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function edit(Trick $trick, Request $request, TrickService $trickService, EntityManagerInterface $em):Response
    {
        $this->denyAccessUnlessGranted('edit', $trick);


        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()){
                $trickService->edit($form);
                $this->addFlash('success','Le trick a bien été modifié');
                return $this->redirectToRoute('app_trick_view',['slug' => $trick->getSlug()]);
            }
        return $this->render('trick/edit.html.twig',['trick' => $trick, 'form' =>$form->createView()]);
    }
    /**
     * @Route("/trick/delete/{id}", name="app_trick_delete", requirements={"id"="\d+"})
     * @param Trick $trick
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function deleteTrick(Trick $trick, Request $request, TrickService $trickService): Response
    {
        $this->denyAccessUnlessGranted('delete', $trick);

        $form = $this->createForm(DeleteTrickType::class, $trick);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $trickService->delete($form);
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