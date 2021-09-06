<?php


namespace App\Service;


use App\Entity\Trick;
use App\Util\Util;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class TrickService
{
    /**
     * @var string|\Stringable|UserInterface
     */
    private $user;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var Uploader
     */
    private $uploader;
    /**
     * @var Util
     */
    private $util;

    public function __construct(TokenStorageInterface $tokenStorage,
                                EntityManagerInterface $em,
                                Uploader $uploader,
                                Util $util)
    {
        $this->user = $tokenStorage->getToken()->getUser();
        $this->em = $em;
        $this->uploader = $uploader;
        $this->util = $util;

    }

    /**
     * Add new trick function
     * @param Form $data
     * @return mixed
     */
    public function create(FormInterface $data)
    {
        $trick = $data->getData();
        $slug = $this->util->getSlug($trick->getName());
        $trick->setSlug($slug);
        $trick->setUser($this->user);

            foreach ($data->get('images')->getData() as $image){
                $madia =  $this->uploader->saveImage($image);
                $trick->setImgDefault($madia);
                $trick->addImage($madia);
        }
        $this->em->persist($trick);
        $this->em->flush();

        return $trick;

    }

    /**
     * Edit trick function
     * @param FormInterface $data
     */
    public function edit(FormInterface $data){
        $trick = $data->getData();
        $this->em->persist($trick);
        $this->em->flush();
    }

    public function delete(FormInterface $form){
        $trick = $form->getData();
        $this->em->remove($trick);
        $this->em->flush();
    }
}