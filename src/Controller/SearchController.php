<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Repository\CategorieRepository;
use App\Repository\DescripteurRepository;
use App\Repository\LivreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class SearchController extends AbstractController
{
    /**
     * @Route("/search", name="search")
     */
    public function index()
    {
        return $this->render('search/index.html.twig', [
            'controller_name' => 'SearchController',
        ]);
    }

    /**
     * @Route("/search/get")
     * @param Request $request
     * @param LivreRepository $repo
     * @param CategorieRepository $categorieRepository
     * @return JsonResponse
     */
    public function getSearch(Request $request, LivreRepository $repo, CategorieRepository $categorieRepository)
    {

        if ($request->isXmlHttpRequest()) {

            $livres = $repo->findAll();

            $results['results'] = [];

            $i = 1;
            foreach ($categorieRepository->findAll() as $categorie) {
                $results['results']['category' . $i]['name'] = $categorie->getNom();
                foreach ($livres as $livre) {
                    if (!($livre->getCategorie() === $categorie))
                        continue;
                    $book = [];
                    $book['image'] = $livre->getCouverture() ? $livre->getCouverture() : "";
                    $book['title'] = $livre->getTitrePrincipale();
                    $book['url'] = "/search/book/".$livre->getId();
                    $book['description'] = $livre->getObservation() ? $livre->getObservation() : "";
                    $results['results']['category' . $i]['results'][] = $book;
                    $results['results']['category' . $i]['results'][] = $book;
                    $results['results']['category' . $i]['results'][] = $book;
                }
                $i++;
            }


            if ($results) {

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

            }

        }

        return new JsonResponse([
            'type' => "error",
            'message' => "Not an XmlHttpRequest"
        ], 400, [], false);

    }

    /**
     * @Route("/search/results",name="search_results", methods={"GET"})
     * @param Request $request
     * @param LivreRepository $livreRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function results(Request $request, LivreRepository $livreRepository)
    {

        $query = $request->query->get('q', '');

        if (!$query)
            return $this->render('search/index.html.twig');

        $foundBooks = $livreRepository->findBySearchQuery($query);

        $results = [];

        foreach ($foundBooks as $book) {
            $authors = [];
            foreach ($book->getAuteurs() as $auteur) {
                $authors[] = $auteur->getNom();
            }
            $results[] = [
                'id' => $book->getId(),
                'cover' => $book->getCouverture(),
                'title' => $book->getTitrePrincipale(),
                'date' => $book->getDateAquis()->format('M d,Y'),
                'authors' => join(", ", $authors),
                'desc' => $book->getObservation(),
                'tags' => $book->getDescripteurs()
            ];
        }

        $withTags = false;
        $filtered = [];
        if ($tagName = $request->query->get('tag')) {

            $withTags = true;

            foreach ($results as $result) {
                foreach ($result['tags'] as $tag) {
                    if ($tag->getNom()===$tagName){
                        $filtered[]=$result;
                        break;
                    }
                }
            }
        }

//        dd($results);

        return $this->render('search/results.html.twig', [
            'books' => ($withTags ? $filtered : $results)
        ]);
    }

    /**
     * @Route("/search/book/{id}",name="book_inf")
     * @param Livre $book
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function bookCard(Livre $book){
        return $this->render('search/book.html.twig',[
            'book' => $book
        ]);
    }
}
