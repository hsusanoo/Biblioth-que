<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\LivreRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class DashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="dashboard")
     * @return Response
     */
    public function index()
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }

    /**
     * @Route("/admin/getCatStats/{year}",name="CategoriesStatistics")
     * @param int $year
     * @param CategorieRepository $categorieRepository
     * @param LivreRepository $livreRepository
     * @return JsonResponse
     * @throws Exception
     */
    public function getCategoriesStats(int             $year, CategorieRepository $categorieRepository,
                                       LivreRepository $livreRepository, Request $request)
    {
        if ($request->isXmlHttpRequest()) {

            // Initialize response data
            $data = [];
            // Get Number of books by year
            $totalLivres = count($livreRepository->findByYear($year));
            // Get number of samples by year
            $totalExemplaires = 0;
            foreach ($livreRepository->findByYear($year) as $livre) {
                $totalExemplaires += count($livre->getExemplaires());
            }

            // Loop for each category/domain
            foreach ($categorieRepository->findAll() as $categorie) {
                // Get category name
                $catNom = $categorie->getNom();
                // Get books by category and year
                $livres = $livreRepository->getByCategoryAndYear($categorie->getId(), $year);
                $nbrLivres = count($livres);
                $nbrExemplaires = 0;
                foreach ($livres as $livre) {
                    $nbrExemplaires += count($livre->getExemplaires());
                }
                $data['categories'][$catNom]['Livres']['nbr'] = $nbrLivres;
                $data['categories'][$catNom]['Livres']['prc'] = number_format($nbrLivres * 100 / $totalLivres, 2, '.', ' ');
                $data['categories'][$catNom]['Exemplaires']['nbr'] = $nbrExemplaires;
                $data['categories'][$catNom]['Exemplaires']['prc'] = number_format($nbrExemplaires * 100 / $totalExemplaires, 2, '.', ' ');
            }
            $data['totalLivres'] = $totalLivres;
            $data['totalExemplaires'] = $totalExemplaires;

            // Sort by Categories
            ksort($data);

            $encoders = [
                new JsonEncoder(),
            ];

            $normalizers = [
                new ObjectNormalizer(),
            ];

            $seralizer = new Serializer($normalizers, $encoders);

            $result = $seralizer->serialize($data, 'json', [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                }
            ]);

            return new JsonResponse($result, 200, [], true);

        }
        return new JsonResponse([
            'type' => "error",
            'message' => "Not an XmlHttpRequest"
        ], 400, [], false);
    }

    /**
     * @Route("/admin/getBooksStats/{year}",name="BooksStatistics")
     * @param int $year
     * @param LivreRepository $livreRepository
     * @return JsonResponse
     * @throws Exception
     */
    public function getBooksStats(int $year, LivreRepository $livreRepository)
    {

        $data = [];

        for ($i = 1; $i <= 12; $i++) {

            $books = $livreRepository->findByYearAndMonth($year, $i);

            $samplesCounter = 0;


            foreach ($books as $book) {
                $samplesCounter += count($book->getExemplaires());
            }

            $booksCounter = count($books);

            $data['books'][] = $booksCounter;
            $data['samples'][] = $samplesCounter;

        }

        $encoders = [
            new JsonEncoder(),
        ];

        $normalizers = [
            new ObjectNormalizer(),
        ];

        $seralizer = new Serializer($normalizers, $encoders);

        $result = $seralizer->serialize($data, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return new JsonResponse($result, 200, [], true);

    }

}
