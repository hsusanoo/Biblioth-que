<?php

namespace App\Controller;

use App\Entity\Auteur;
use App\Entity\Descripteur;
use App\Entity\Exemplaire;
use App\Entity\Livre;
use App\Form\LivreType;
use Doctrine\Common\Collections\ArrayCollection;
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
        return $this->render('book/show.html.twig', [
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
        // creating new book
        $livre = new Livre();

        //setting today as a default date
        $livre->setDateAquis(new \DateTime('now'));

        // Creating a new book sample
        $exemplaire = new Exemplaire();
        $exemplaire->setLivre($livre);
        $livre->addExemplaire($exemplaire);

        // Creating a ne author
        $auteur = new Auteur();
        $auteur->addLivre($livre);
        $livre->addAuteur($auteur);


        $livreForm = $this->createForm(LivreType::class, $livre);

        $livreForm->handleRequest($request);

        if ($livreForm->isSubmitted() && $livreForm->isValid()) {

            // Converting tags strings to Objects Array
            $descripteurs = [];

            foreach ($livre->getDescripteurs() as $desc) {

                $descripteur = new Descripteur();
                $descripteur->setNom($desc)
                    ->addLivre($livre);
                $descripteurs[] = $descripteur;
            }

            $livre->setDescripteurs($descripteurs);

            foreach ($livre->getAuteurs() as $auteur)
                $auteur->addLivre($livre);

            foreach ($livre->getExemplaires() as $exemplaire) {
                $exemplaire->setLivre($livre);
            }

            $manager->persist($livre);
            $manager->flush();

            dump($livre);
        }

        return $this->render('book/new.html.twig', [
            'livreForm' => $livreForm->createView()
        ]);
    }
}
