<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="security_login")
     */
    public function login()
    {
        return $this->render('security/login.html.twig');
    }

    /**
     * @Route("/logout",name="security_logout")
     */
    public function logout(){}

    /**
     * @Route("/admin",name="dashboard")
     * @Route("/",name="index")
     */
    public function dashboard(){

        return $this->render('admin/index.html.twig');

    }
}
