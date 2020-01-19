<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    /**
     * @Route("/",name="index")
     */
    public function index()
    {

        if ($this->isGranted('ROLE_GESTION'))
            return $this->redirectToRoute("dashboard");
        elseif ($this->isGranted('ROLE_ADMIN'))
            return $this->redirectToRoute("books");

        return $this->redirectToRoute("app_login");

    }

    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->isGranted("IS_AUTHENTICATED_FULLY"))
            return $this->redirectToRoute("index");

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }


    /**
     * @Route("/logout",name="app_logout")
     */
    public function logout()
    {
    }


    /**
     * @Route("/recover",name="recover")
     * @param Request $request
     * @param UserRepository $userRepository
     * @param ObjectManager $manager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param Swift_Mailer $mailer
     * @return RedirectResponse|Response
     */
    public function passwordRecover(Request $request, UserRepository $userRepository,
                                    ObjectManager $manager, UserPasswordEncoderInterface $passwordEncoder,
                                    Swift_Mailer $mailer)
    {
        if ($this->isGranted("IS_AUTHENTICATED_FULLY"))
            return $this->redirectToRoute("index");

        $email = null;
        $error = null;
        $success = null;

        if (($email = $request->request->get('email'))) {
            if (!($user = $userRepository->findOneBy(['email' => $email]))) {
                $error = 'Email non existant !';
            } else {
                if (!$this->newPassword($user, $manager, $passwordEncoder, $mailer))
                    $error = "Une erreur est survenue";
                else
                    $success = 'Un Nouveau mot de passe est envoyé !' .
                        ' Vérifiez votre boite mail';
            }

        }

        return $this->render('security/password_recovery.html.twig',
            [
                'email' => $email,
                'error' => $error,
                'success' => $success
            ]
        );
    }

    public function newPassword(User $user, ObjectManager $manager, UserPasswordEncoderInterface $passwordEncoder,
                                Swift_Mailer $mailer)
    {

        $faker = Factory::create();

        $random = md5(rand(0, 1000));
        $user->setPassword($passwordEncoder->encodePassword($user, $random));

        // Emailing password to user's mail box
        $message = (new Swift_Message("Bienvenue"))
            ->setFrom("freeinxd@gmail.com")
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'emails/admin_pass_rec.html.twig', [
                        'nom' => $user->getNom(),
                        'prenom' => $user->getPrenom(),
                        'password' => $random
                    ]
                ),
                'text/html'
            )->setContentType('text/html');
        $nSent = $mailer->send($message);
        if (!$nSent) {
            return false;
        }

        $manager->persist($user);
        $manager->flush();
        return true;
    }

    /**
     * @Route("/admin/settings",name="password_reset")
     */
    public function passwordReset(Request $request, UserPasswordEncoderInterface $passwordEncoder,
                                  ObjectManager $manager)
    {
        $passForm = $this->createForm(ResetPasswordType::class);
        $passForm->handleRequest($request);

        if ($passForm->isSubmitted() && $passForm->isValid()) {
            $oldPassword = $passForm->get('oldPassword')->getData();
            $newPassword = $passForm->get('newPassword')->getData();
            $user = $this->getUser();

            if (!($passwordEncoder->isPasswordValid($user, $oldPassword))) {
                $this->addFlash('error', 'Ancient mot de passe incorrect !');
            } else {
                $user->setPassword($passwordEncoder->encodePassword($user, $newPassword));
                $manager->persist($user);
                $manager->flush();
                $this->addFlash('success', 'Mot de passe changé avec succès !');
            }
        }

        return $this->render('security/password_reset.html.twig', [
            'passForm' => $passForm->createView()
        ]);
    }
}
