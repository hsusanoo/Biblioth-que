<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ResponsableController extends AbstractController
{
    /**
     * @Route("/admin/responsable", name="responsable")
     */
    public function show(Request $request, UserRepository $repository)
    {

        $users = $repository->findBy([
            'roles' => ['ROLE_USER'],
        ]);

        return $this->render('admin/responsable/show.html.twig', [
            'controller_name' => 'ResponsableController',
        ]);
    }

    /**
     * @Route("/admin/responsable/ajouter",name="resp_new")
     * @param Request $request
     * @param ObjectManager $manager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function new(Request $request, ObjectManager $manager){

        $user = new User();

        $userForm = $this->createForm(UserType::class,$user);

        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()){

            $user->setRoles(['ROLE_ADMIN']);

            $manager->persist($user);
            $manager->flush();



        }

        return $this->render('admin/responsable/new.html.twig', [
            'controller_name' => 'ResponsableController',
            'userForm' => $userForm->createView()
        ]);
    }
}
