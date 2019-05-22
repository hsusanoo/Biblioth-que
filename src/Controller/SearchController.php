<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
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
     */
    public function getSearch(Request $request, LivreRepository $repo, CategorieRepository $categorieRepository)
    {

//        if ($request->isXmlHttpRequest()) {

            $livres = $repo->findAll();

            $results['results'] = [];

            $i = 1;
            foreach ($categorieRepository->findAll() as $categorie) {
                $results['results']['category' . $i]['name'] = $categorie->getNom();
                foreach ($livres as $livre) {
                    if (!($livre->getCategorie()===$categorie))
                        continue;
                    $book = [];
                    $book['image'] = $livre->getCouverture() ? $livre->getCouverture() : "";
                    $book['title'] = $livre->getTitrePrincipale();
                    $book['url'] = "#";
                    $book['description'] = $livre->getObservation() ? $livre->getObservation() : "";
                    $results['results']['category'.$i]['results'][] = $book;
//                    dd($livre->getCategorie(),$categorie,!($livre->getCategorie()===$categorie));
                    break;
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

//        }

        return new JsonResponse([
            'type' => "error",
            'message' => "Not an XmlHttpRequest"
        ], 400, [], false);

    }
}
