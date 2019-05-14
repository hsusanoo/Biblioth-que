<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ProfileController extends AbstractController
{
    /**
     * @Route("/admin/responsable/{id}", name="admin_profile",requirements={"id" = "\d+"})
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(User $user)
    {
        return $this->render('admin/responsable/profile.html.twig', [
            'controller_name' => 'ProfileController',
            'resp' => $user
        ]);
    }

    /**
     * @Route("/admin/responsable/{id}/get",name="admin_get",requirements={"id" = "\d+"})
     * @param User $user
     * @param Request $request
     * @return JsonResponse
     */
    public function getLog(User $user, Request $request)
    {
        if ($request->isXmlHttpRequest()) {

            $books = $user->getLivres();

            $livres = [];

            foreach ($books as $book) {
                $livres[] = [
                    "id" => $book->getId(),
                    "titre" => $book->getTitrePrincipale(),
                    "date" => date_format($book->getDateAquis(), 'd/m/Y H:i:s')
                ];
            }


            if ($livres) {

                $encoders = [
                    new JsonEncoder(),
                ];

                $normalizers = [
                    new ObjectNormalizer(),
                ];

                $seralizer = new Serializer($normalizers, $encoders);

                $data = $seralizer->serialize($livres, 'json', [
                    'circular_reference_handler' => function ($object) {
                        return $object->getId();
                    }
                ]);

                return new JsonResponse($data, 200, [], true);

            }

        }
        return new JsonResponse([
            'type' => "error",
            'message' => "Not a XmlHttpRequest"
        ], 400, [], false);
    }

    /**
     * @Route("/admin/responsable/{id}/overview")
     * @param User $user
     * @param Request $request
     * @param CategorieRepository $repository
     * @return JsonResponse
     */
    public function getOverview(User $user, Request $request,
                                CategorieRepository $repository)
    {

        $data = [];

        foreach ($repository->findAll() as $categorie) {
            $counter = 0;
            foreach ($categorie->getLivres() as $livre) {
                if ($livre->getAddedBy() === $user)
                    $counter++;
            }
            $data['category'][] = $categorie->getNom();
            $data['data'][] = $counter;
        }

        if ($data) {

            $encoders = [
                new JsonEncoder(),
            ];

            $normalizers = [
                new ObjectNormalizer(),
            ];

            $seralizer = new Serializer($normalizers, $encoders);

            $jsonData = $seralizer->serialize($data, 'json', [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                }
            ]);

            return new JsonResponse($jsonData, 200, [], true);

        }
        return new JsonResponse([
            'type' => "error",
            'message' => "Not a XmlHttpRequest"
        ], 400, [], false);
    }
}
