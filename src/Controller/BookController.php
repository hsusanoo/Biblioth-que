<?php

namespace App\Controller;

use App\Entity\Auteur;
use App\Entity\Exemplaire;
use App\Entity\Livre;
use App\Form\LivreType;
use App\Repository\CategorieRepository;
use App\Repository\DescripteurRepository;
use App\Repository\LivreRepository;
use DateTime;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class BookController extends AbstractController
{

    /**
     * @Route("/admin/books", name="books")
     */
    public function show()
    {
        return $this->render('admin/book/show.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }

    /**
     * @Route("admin/books/getcat",name="get_cat",methods={"GET"})
     * @param Request $request
     * @param CategorieRepository $catRepo
     * @return JsonResponse
     */
    public function getDomaine(Request $request, CategorieRepository $catRepo): JsonResponse
    {
        if ($request->isXmlHttpRequest()) {

            $repos = $catRepo->findAll();
            $cats = [];

            foreach ($repos as $repo) {
                $cat['id'] = $repo->getNom();
                $cat['text'] = $repo->getNom();
                $cats[] = $cat;
            }

            $results['results'] = $cats;
            $results['pagination'] = ['more' => false];

            $encoders = [
                new JsonEncoder(),
            ];

            $normalizers = [
                new ObjectNormalizer(),
            ];

            $seralizer = new Serializer($normalizers, $encoders);

            $data = $seralizer->serialize($results, 'json', [
                'circular_reference_handler' => static function ($object) {
                    return $object->getId();
                }
            ]);

            return new JsonResponse($data, 200, [], true);

        }
        return new JsonResponse([
            'type' => "error",
            'message' => "Not a XmlHttpRequest"
        ], 400, [], false);
    }

    /**
     * @Route("/admin/books/gettags",name="get_tags",methods={"GET"})
     * @param Request $request
     * @param DescripteurRepository $tagsRepo
     * @return JsonResponse
     */
    public function getTags(Request $request, DescripteurRepository $tagsRepo): JsonResponse
    {
        if ($request->isXmlHttpRequest()) {

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
                'circular_reference_handler' => static function ($object) {
                    return $object->getId();
                }
            ]);

            return new JsonResponse($data, 200, [], true);

        }
        return new JsonResponse([
            'type' => 'error',
            'message' => 'Not a XmlHttpRequest'
        ], 400, [], false);
    }

    /**
     * @Route("/admin/books/get",name="get_books",methods={"GET"},options={"expose"=true})
     * @param Request $request
     * @param LivreRepository $livreRepository
     * @return JsonResponse
     * @throws Exception
     */

    public function getBooks(Request $request, LivreRepository $livreRepository): JsonResponse
    {

        if ($request->isXmlHttpRequest()) {

            if (($start = $request->query->get('start')) && ($end = $request->query->get('end'))) {

                $livres = $livreRepository->findByDateRange($start, $end);

            } else {

                $livres = $livreRepository->findAll();

            }


            $books = [];

            foreach ($livres as $livre) {

                $book = [];
                $book['id'] = $livre->getId();
                $book['titrePrincipale'] = $livre->getTitrePrincipale();
                $book['titreSecondaire'] = $livre->getTitreSecondaire();
                $book['isbn'] = $livre->getIsbn() ? $livre->getIsbn() : "";
                $book['couverture'] = $livre->getCouverture() ? $livre->getCouverture() : "";
                $statut = 0;
                foreach ($livre->getExemplaires() as $exemplaire) {
                    if ($exemplaire->getStatut() == 1)
                        $statut = 1;
                }
                $book['statut'] = $statut;
                $book['edition'] = $livre->getDateEdition() ? $livre->getDateEdition() : "";
                $book['date_aquis'] = date_format($livre->getDateAquis(), "d/m/Y");
                $book['quantité'] = count($livre->getExemplaires());
                $book['observation'] = $livre->getObservation() ? $livre->getObservation() : "";
                $book['n_pages'] = $livre->getNPages() ? $livre->getNPages() : "";
                if ($livre->getCategorie()) {
                    $book['categorie'] = $livre->getCategorie()->getNom();
                } else
                    $book['categorie'] = "";
                $book['prix'] = $livre->getPrix() ? $livre->getPrix() : "";
                if (count($livre->getDescripteurs()) > 0) {
                    $tags = [];
                    foreach ($livre->getDescripteurs() as $tag) {
                        $tags[] = $tag->getNom();
                    }
                    $book['tags'] = implode(",", $tags);
                } else
                    $book['tags'] = "";
                if (count($livre->getAuteurs()) > 0) {
                    $authors = [];
                    foreach ($livre->getAuteurs() as $auteur) {
                        $authors[] = $auteur->getNom();
                    }
                    $book['authors'] = implode(",", $authors);
                } else
                    $book['authors'] = "";
                if ($qCat = $request->query->get('cat')) {
                    if ($book['categorie'] !== $qCat) {
                        continue;
                    }
                }
                if ($qStatut = $request->query->get('statut')) {
                    if ($book['statut'] !== (int)$qStatut) {
                        continue;
                    }
                }
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

        }

        return new JsonResponse([
            'type' => "error",
            'message' => "Not an XmlHttpRequest"
        ], 400, [], false);

    }

    /**
     * @Route("/admin/books/new",name="books_new")
     * @Route("/admin/books/{id}/edit",name="book_edit")
     * @param Request $request
     * @param ObjectManager $manager
     * @param Livre|null $livre
     * @return Response
     * @throws Exception
     */
    public function create(Request $request, ObjectManager $manager, Livre $livre = null): Response
    {

        // creating new book if create mode
        if (!$livre) {

            $livre = new Livre();

            //setting today as a default date
            $livre->setDateAquis(new DateTime('now'));

            // Creating a new book sample
            $exemplaire = new Exemplaire();
            $exemplaire->setLivre($livre);
            $livre->addExemplaire($exemplaire);

            // Creating a ne author
            $auteur = new Auteur();
            $auteur->addLivre($livre);
            $livre->addAuteur($auteur);
        }


        $livreForm = $this->createForm(LivreType::class, $livre);

        $livreForm->handleRequest($request);

        if ($livreForm->isSubmitted() && $livreForm->isValid()) {

            foreach ($livre->getAuteurs() as $auteur)
                $auteur->addLivre($livre);

            foreach ($livre->getExemplaires() as $exemplaire) {
                $exemplaire->setLivre($livre);
            }

            //setting added by
            $livre->setAddedBy($this->getUser());

            // setting updated by if it's updated
            if ($livre->getId())
                $livre->setUpdatedBy($this->getUser());

            // debugging tags
            $tags = [];
            foreach ($livre->getDescripteurs() as $descripteur) {
                $tags[] = $descripteur->getNom();
            }
            $tagsArray = json_decode(join(",", $tags), true);
            $i = 0;
            foreach ($livre->getDescripteurs() as $descripteur) {
                $descripteur->setNom($tagsArray[$i]['value']);
                $i++;
            }

            $manager->persist($livre);
            $manager->flush();

            $this->addFlash(
                'success',
                'Livre ' . ($livre->getId() ? ' modifié' : 'ajouté') . 'avec succès !');

            return $this->redirectToRoute("books");
        }

        if ($livre->getId())
            return $this->render('admin/book/new.html.twig', [
                'livreForm' => $livreForm->createView(),
//                'tags' => $tags ? $tags : null,
                'editMode' => true,
                'livre' => $livre
            ]);


        return $this->render('admin/book/new.html.twig', [
            'livreForm' => $livreForm->createView(),
            'editMode' => false
        ]);

    }

    /**
     * @Route("/admin/books/import",name="books_import")
     */
    public function import(): Response
    {
        return $this->render('admin/book/import.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }

    /**
     * @Route("/admin/books/{id}",name="admin_book_inf")
     * @param Livre $livre
     * @return Response
     */
    public function bookInformation(Livre $livre): Response
    {
        return $this->render('admin/book/book_card.html.twig', [
            'book' => $livre
        ]);
    }

}
