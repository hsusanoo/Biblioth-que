<?php

namespace App\Controller;

use App\Entity\Auteur;
use App\Entity\Descripteur;
use App\Entity\Exemplaire;
use App\Entity\Livre;
use App\Form\LivreType;
use App\Repository\CategorieRepository;
use App\Repository\DescripteurRepository;
use App\Repository\LivreRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class BookController extends AbstractController
{
    /**
     * @Route("/books", name="books")
     */
    public function show()
    {
        return $this->render('book/show.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }

    /**
     * @param Request $request
     * @param DescripteurRepository $tagsRepo
     * @return JsonResponse
     * @Route("/books/gettags",name="get_tags",methods={"GET"})
     */
    public function getTags(Request $request, DescripteurRepository $tagsRepo)
    {
//        if ($request->isXmlHttpRequest()) {

            $repos = $tagsRepo->findAll();
            $tags = [];

            foreach ($repos as $repo) {
                $tag['id'] = $repo->getId();
                $tag['text'] = $repo->getNom();
                $tags[] = $tag;
            }

            $results['results'] = $tags;
            $results['pagination'] = ['more' => false];

            $encoders = [
                new JsonEncoder(),
            ];

            $normalizers = [
                new ObjectNormalizer(),
            ];

            $seralizer = new Serializer($normalizers, $encoders);

            $data = $seralizer->serialize($results, 'json', [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                }
            ]);

            return new JsonResponse($data, 200, [], true);

//        }
        return new JsonResponse([
            'type' => "error",
            'message' => "Not a XmlHttpRequest"
        ], 400, [], false);
    }

    /**
     * @Route("/books/get",name="get_books",methods={"GET"},options={"expose"=true})
     * @param Request $request
     * @param LivreRepository $livreRepository
     * @return JsonResponse
     */

    public function getBooks(Request $request, LivreRepository $livreRepository)
    {

//        if ($request->isXmlHttpRequest()) {

        $livres = $livreRepository->findAll();

        $books = [];

        foreach ($livres as $livre) {

            $book = [];
            $book['titrePrincipale'] = $livre->getTitrePrincipale();
            $book['titreSecondaire'] = $livre->getTitreSecondaire();
            $book['isbn'] = $livre->getIsbn();
            $book['couverture'] = $livre->getCouverture();
            $statut = 0;
            foreach ($livre->getExemplaires() as $exemplaire) {
                if ($exemplaire->getStatut() == 1)
                    $statut = 1;
            }
            $book['statut'] = $statut;
            $book['edition'] = $livre->getDateEdition();
            $book['date_aquis'] = date_format($livre->getDateAquis(), "d/m/Y");
            $book['quantité'] = count($livre->getExemplaires());
            $book['observation'] = $livre->getObservation();
            $book['n_pages'] = $livre->getNPages();
            $book['categorie'] = $livre->getCategorie()->getNom();
            $book['prix'] = $livre->getPrix();
            $tags = [];
            foreach ($livre->getDescripteurs() as $tag) {
                $tags[] = $tag->getNom();
            }
            $book['tags'] = implode(",", $tags);
            $authors = [];
            foreach ($livre->getAuteurs() as $auteur) {
                $authors[] = $auteur->getNom();
            }
            $book['authors'] = implode(",", $authors);
            $books[] = $book;

        }


        if ($books) {

            $encoders = [
                new JsonEncoder(),
            ];

            $normalizers = [
                new ObjectNormalizer(),
            ];

            $seralizer = new Serializer($normalizers, $encoders);

            $data = $seralizer->serialize($books, 'json', [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                }
            ]);

            return new JsonResponse($data, 200, [], true);

        }

//        }

        return new JsonResponse([
            'type' => "error",
            'message' => "Not a XmlHttpRequest"
        ], 400, [], false);

    }

    /**
     * @Route("/books/new",name="books_new")
     * @Route("/books/{id}/edit",name="book_edit")
     * @param Livre|null $livre
     * @param Request $request
     * @param ObjectManager $manager
     * @param CategorieRepository $categorieRepository
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function create(Request $request, ObjectManager $manager,
                           CategorieRepository $categorieRepository,
                           Livre $livre = null, LivreRepository $repository)
    {

        // creating new book if create mode
        if (!$livre) {

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

        }else{
            $tags = [];
            foreach ($livre->getDescripteurs() as $tag) {
                $tags[] = $tag->getNom();
            }
        }


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

            $livre->setUpdatedAt(new \DateTime());

            $manager->persist($livre);
            $manager->flush();

            return $this->redirectToRoute("books");
        }

        return $this->render('book/new.html.twig', [
            'livreForm' => $livreForm->createView(),
            'tags' => $tags ? $tags : null,
            'editMode' => $livre->getId() !== null
        ]);
    }
}
