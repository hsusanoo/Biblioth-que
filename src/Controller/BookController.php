<?php

namespace App\Controller;

use App\Entity\Auteur;
use App\Entity\Descripteur;
use App\Entity\Exemplaire;
use App\Entity\Livre;
use App\Form\LivreType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    /**
     * @Route("/books", name="books")
     */
    public function index()
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }

    /**
     * @Route("/books/new",name="books_new")
     * @param Request $request
     * @param ObjectManager $manager
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function create(Request $request, ObjectManager $manager)
    {

        $livre = new Livre();

        $livre->setDateAquis(new \DateTime('now'));

        $exemplaire = new Exemplaire();
        $exemplaire->setLivre($livre);

        $auteur = new Auteur();
        $auteur->addLivre($livre);

//        $desc = new Descripteur();
//        $desc->addLivre($livre);

        $livre->addExemplaire($exemplaire);
        $livre->addAuteur($auteur);
//        $livre->addDescripteur($desc);

        $livreForm = $this->createForm(LivreType::class, $livre);

        $livreForm->handleRequest($request);

        if ($livreForm->isSubmitted()) {
            dump($livre);
        }

        return $this->render('book/new.html.twig', [
            'livreForm' => $livreForm->createView()
        ]);
    }
}
