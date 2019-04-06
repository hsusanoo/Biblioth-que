<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Form\LivreType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Isbn;

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

        $livreForm = $this->createForm(LivreType::class,$livre);

        $livreForm->handleRequest($request);

        if ($livreForm->isSubmitted() && $livreForm->isValid()){
            dump($livre);
        }

        return $this->render('book/new.html.twig',[
            'livreForm' => $livreForm->createView()
        ]);
    }
}
