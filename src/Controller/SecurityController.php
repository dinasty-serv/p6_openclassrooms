<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\EditAccountType;
use App\Form\ForgotPasswordType;
use App\Form\ImgType;
use App\Form\RegisterType;
use App\Form\ResetPasswordType;
use App\Service\Uploader;
use LogicException;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @param Swift_Mailer $mailer
     * @return Response
     */
    public function register(Request  $request, UserPasswordEncoderInterface $encoder, Swift_Mailer $mailer): Response
    {

        $form = $this->createForm(RegisterType::class);
        $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()){
                $user = $form->getData();

                $encoded = $encoder->encodePassword($user, $user->getPassword());
                $token = $this->generateToken();
                $user->setResetToken($token);
                $user->setPassword($encoded);

                $message = (new Swift_Message('Activé votre compte ! | snowtricks'))
                    ->setFrom('postmaster@snowtricks.fr', 'snowtricks')
                    ->setTo($user->getEmail())
                    ->setBody(
                        $this->renderView(
                            'email/activeAccount.html.twig',
                            [
                                'user' => $user,
                                'token' =>$token
                            ]
                        ),
                        'text/html'
                    );

                $mailer->send($message);
                $this->addFlash('success', 'Votre compte a bien été créer, vous allez recevoire un email pour activé votre compte.');
                $this->getDoctrine()->getManager()->persist($user);
                $this->getDoctrine()->getManager()->flush();
                return $this->redirectToRoute('app_home_page');

            }


        return $this->render('security/register.html.twig',['form' => $form->createView()]);

    }

    /**
     * @Route("/forgot-password", name="app_forgot_password")
     * @param Request $request
     * @param Swift_Mailer $mailer
     * @return Response
     */
    public function forgotPassword(Request $request, Swift_Mailer $mailer): Response
    {
        $error = '';
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(ForgotPasswordtype::class);
        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $user = $em->getRepository(User::class)->findOneBy(['email' => $data['email']]);
                if ($user != null) {
                    $token = $this->generateToken();

                    $user->setResetToken($token);
                    $em->persist($user);

                    $message = (new Swift_Message('Mots de passe oublié | snowtricks'))
                        ->setFrom('postmaster@snowtricks.fr', 'snowtricks')
                        ->setTo($user->getEmail())
                        ->setBody(
                            $this->renderView(
                                'email/resetPassword.html.twig',
                                [
                                    'user' => $user,
                                    'token' => $token
                                ]
                            ),
                            'text/html'
                        );

                    if ($mailer->send($message)) {
                        $em->flush();
                        return $this->redirectToRoute('app_login');
                    }

                }
                $this->addFlash(
                    'success',
                    'Si votre compte existe vous allez recevoir un email de réinitialisation. '
                );
            }
        }
        return $this->render('security/forgot_password.html.twig', ['error' => $error, 'form' => $form->createView()]);
    }


    /**
     * @Route("/reset-password/{resetToken}", name="app_reset_password")
     * @param User $user
     * @param UserPasswordEncoderInterface $encoder
     * @param Request $request
     * @return Response
     */
    public function resetPassword(User $user, UserPasswordEncoderInterface $encoder, Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(ResetPasswordType::class);
        $error = '';
        if (!empty($user)) {
            $form->handleRequest($request);
            if ($request->isMethod('POST')) {
                if ($form->isSubmitted() && $form->isValid()) {
                    $data = $form->getData();
                    $encoded = $encoder->encodePassword($user, $data['password']);
                    $user->setPassword($encoded);
                    $user->setResetToken(null);
                    $em->persist($user);

                    $em->flush();
                    $this->addFlash(
                        'success',
                        'Votre mots de passe à bien été modifié !'
                    );

                    return $this->redirectToRoute('app_login');
                }
            }

            return $this->render('security/reset_password.html.twig',
                ['error' => $error,
                    'form' => $form->createView(),
                    'token' => $user->getResetToken()
                ]);
        } else {
            $this->addFlash(
                'danger',
                'Token invalide'
            );

            return $this->redirectToRoute('app_login');
        }
    }

    /**
     * @Route("/account", name="app_account")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function account(Request $request, UserPasswordEncoderInterface $encoder, Uploader $uploader): Response
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $formChangePassword = $this->createForm(ChangePasswordType::class);
        $formEditAccount = $this->createForm(EditAccountType::class, $user);
        $formEditProfilImg = $this->createForm(ImgType::class);

        $formChangePassword->handleRequest($request);
        $formEditAccount->handleRequest($request);
        $formEditProfilImg->handleRequest($request);
        if ($request->isMethod('POST')) {

            //Form change password
            if ($formChangePassword->isSubmitted() && $formChangePassword->isValid()) {
                $data = $formChangePassword->getData();
                $encoded = $encoder->encodePassword($user, $data['plainPassword']);
                $user->setPassword($encoded);
                $em->persist($user);
                $em->flush();
                $this->addFlash(
                    'success',
                    'Votre mots de passe à bien été modifié !'
                );
            }
            //Form edit account
            if ($formEditAccount->isSubmitted() && $formEditAccount->isValid()) {
                $user = $formEditAccount->getData();
                $em->persist($user);
                $em->flush();
                $this->addFlash(
                    'success',
                    'Votre compte à bien été modifié'
                );
            }

            if ($formEditProfilImg->isSubmitted() && $formEditAccount->isValid()) {
                $img = $uploader->saveImage($formEditProfilImg->getData());
                $user = $this->getUser();
                $user->setImgProfile($img->getPath());
                $em->persist($user);
                $em->flush();
                $this->addFlash(
                    'success',
                    'Votre compte à bien été modifié'
                );
            }
        }

        return $this->render(
            'security/account.html.twig',
            [
                'formChangePassword' => $formChangePassword->createView(),
                'formEditAccount' => $formEditAccount->createView()
            ]
        );
    }
     /**
      * @Route("/account/validate/{resetToken}", name="app_account_validate")
      */
    public function validateAccount(User $user):Response
    {
        $em = $this->getDoctrine()->getManager();
        if($user->getStatus() == false){
            $user->setResetToken(null);
            $user->setStatus(true);
            $em->persist($user);
            $em->flush();
            $this->addFlash('success','Votre compte a bien été activé, vous pouvez vous connecter');
        }else{
            $this->addFlash('danger','Token invalide !');

        }
        return $this->redirectToRoute('app_login');
    }
    private function generateToken(): string
    {
        //Generate a random string.
        $token = openssl_random_pseudo_bytes(16);
        //Convert the binary data into hexadecimal representation.
        $token = bin2hex($token);
        //Print it out for example purposes.
        return $token;
    }
}
