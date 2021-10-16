<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Faker\Factory;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResponsableController extends AbstractController
{
    /**
     * @Route("/admin/responsable", name="responsable")
     * @param UserRepository $repository
     * @return Response
     */
    public function show(UserRepository $repository): Response
    {

        $users = $repository->findAll();

        $resp = [];

        foreach ($users as $user) {
            if (!in_array("ROLE_GESTION", $user->getRoles())) {
                $resp[] = $user;
            }
        }

        dump($resp);

        return $this->render('admin/responsable/show.html.twig', [
            'controller_name' => 'ResponsableController',
            'responsables' => $resp
        ]);
    }

    /**
     * @Route("/admin/responsable/ajouter",name="resp_new")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param Swift_Mailer $mailer
     * @return Response
     * @throws Exception
     */
    public function new(Request      $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $passwordEncoder,
                        Swift_Mailer $mailer): Response
    {

        $user = new User();

        $userForm = $this->createForm(UserType::class, $user);

        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {

            $faker = Factory::create();

            $user->setRoles(['ROLE_ADMIN']);
            $random = md5(random_int(0, 1000));
            $user->setPassword($passwordEncoder->encodePassword($user, $random));

            // Emailing password to user's mail
            $message = (new Swift_Message("Bienvenue"))
                ->setFrom("freeinxd@gmail.com")
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'emails/admin.html.twig', [
                            'nom' => $user->getNom(),
                            'prenom' => $user->getPrenom(),
                            'password' => $random
                        ]
                    ),
                    'text/html'
                )->setContentType('text/html');
            $nSent = $mailer->send($message);
            if (!$nSent) {
                $this->addFlash('error', 'Une erreur est survenue: Email n\'a pas pu être envoyé !');
                return $this->render('admin/responsable/new.html.twig', [
                    'controller_name' => 'ResponsableController',
                    'userForm' => $userForm->createView()
                ]);
            }

            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', $user->getNom() . ' ' . $user->getPrenom() . ' ajouté !');

            return $this->redirectToRoute('responsable');

        }

        return $this->render('admin/responsable/new.html.twig', [
            'controller_name' => 'ResponsableController',
            'userForm' => $userForm->createView()
        ]);
    }
}
