<?php

namespace App\Controller;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SecurityController
 * @package App\Controller
 */
class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(): Response
    {
        return $this->render('security/register.html.twig');

    }

    /**
     * @Route("/forgot-password", name="app_forgot_password")
     */
    public function forgotPassword(): Response
    {
        return $this->render('security/forgot_password.html.twig');

    }

    /**
     * @Route("/reset-password", name="app_reset_password")
     */
    public function resetPassword(): Response
    {
        return $this->render('security/reset_password.html.twig');

    }

    /**
     * @Route("/account", name="app_account")
     */
    public function editAccount(): Response
    {
        return $this->render('security/account.html.twig');

    }
}
