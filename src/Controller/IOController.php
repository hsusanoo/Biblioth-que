<?php

namespace App\Controller;

use App\Entity\Auteur;
use App\Entity\Exemplaire;
use App\Entity\Livre;
use App\Repository\AuteurRepository;
use App\Repository\CategorieRepository;
use App\Repository\LivreRepository;
use DateTime;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Dompdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class IOController extends AbstractController
{
    /**
     * @Route("/admin/export", name="export")
     * @param Request $request
     * @param CategorieRepository $repository
     * @return Response
     */
    public function index(Request $request, CategorieRepository $repository): Response
    {
        if ($request->query->get('submit'))
            $this->export($repository, $request->query->get('date'));

        return $this->render('admin/book/reports.html.twig', [
            'controller_name' => 'IOController',
        ]);
    }

    public function export(CategorieRepository $repository, String $date = null, bool $inv = false,
                           LivreRepository $livrRepo = null, $mode = 'excel'): void
    {
        // Getting date values
        $dateArray = null;
        $month = null;
        $year = null;
        if ($date) {
            $dateArray = explode('/', $date);
            if (count($dateArray) > 1) {
                $month = $dateArray[0];
                $year = $dateArray[1];
            } else {
                $year = $dateArray[0];
            }
        }

        $categories = $repository->findAll();

        // Creating new spreadsheet
        $spreadsheet = new Spreadsheet();

        // getting current active sheet

        $sheet = null;

        try {
            $sheet = $spreadsheet->getActiveSheet();
        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
        }

        // Changing cells width
        $sheet->getColumnDimension('A')->setWidth(72.43);
        $sheet->getColumnDimension('B')->setWidth(22.14);
        $sheet->getColumnDimension('C')->setWidth(16.14);
        $sheet->getColumnDimension('D')->setWidth(16.14);
        if ($inv) {
            $sheet->getColumnDimension('E')->setWidth(10);
        }

        // Merging cells
        try {
            $sheet->mergeCells('A4:D5');
        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
        }
        try {
            $sheet->mergeCells('A6:D6');
        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
        }

        // Styling
        $headerStyles = [
            'font' => [
                'bold' => true,
                'size' => 13,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ]
        ];
        $titleStyles = [
            'font' => [
                'bold' => true,
                'size' => 14,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ];
        $domaineStyle = [
            'font' => [
                'bold' => true,
                'size' => 14
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => [
                    'argb' => 'FFE6E6FA'
                ]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ];
        $overviewStyle = [
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => [
                    'argb' => 'FFFFFFFF'
                ]
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => [
                    'argb' => 'FF7F55B9'
                ]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ];
        $tableHeaderStyle = [
            'font' => [
                'bold' => true,
                'size' => 11,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                ]
            ]
        ];
        $tableStyle = [
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                ]
            ]
        ];
        $centerStyle = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ];

        try {
            $sheet->getStyle('A')->getAlignment()->setWrapText(true);
        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
        }
        try {
            $sheet->getStyle('B')->getAlignment()->setWrapText(true);
        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
        }

        // Applying styles to header
        try {
            $sheet->getStyle('A1:A3')->applyFromArray($headerStyles);
        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
        }
        try {
            $sheet->getStyle('A4:A6')->applyFromArray($titleStyles);
        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
        }

        // Columns alignment
        try {
            $sheet->getStyle('B:D')->applyFromArray($centerStyle);
        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
        }

        // Set Header values
        $sheet->setCellValue('A1', 'Ecole Supérieure de Technologie de Salé');
        $sheet->setCellValue('D1', 'Le: ' . date_format(new DateTime(), 'd/m/Y'));
        $sheet->setCellValue('A2', 'Direction des Etudes');
        $sheet->setCellValue('A3', 'Bibliothèque');
        if (!$inv) {

            $sheet->setCellValue('A4', 'NOUVELLES ACQUISITIONS DOCUMENTAIRES / BIBLIOTHÈQUE EST-SALÉ ' .
                $this->getMonthName("fr", $month) . ' ' . $year);
            $sheet->setCellValue('A6', $year . ' ' . ' قائمة الكتب المقتناة ' . $this->getMonthName("ar", $month));

            // current row value
            $row = 6;

        } else {

            $sheet->setCellValue('A4', 'INVENTAIRE DES LIVRES / BIBLIOTHÈQUE EST-SALÉ ' . ' ' . $year);
//            $sheet->setCellValue('A6', $year . ' ' . ' قائمة الكتب ' . $this->getMonthName("ar", $month));

            $row = 9;

            // OVERVIEW
            //Header
            try {
                $sheet->mergeCells('A' . $row . ':D' . $row);
            } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            }
            try {
                $sheet->getStyle('A' . $row)->applyFromArray($overviewStyle);
            } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            }
            $sheet->setCellValue('A' . $row, "VUE D'ENSEMBLE");

            $row += 2;
            // Table
            $sheet->setCellValue('A' . $row, 'Catégorie');
            $sheet->setCellValue('B' . $row, 'Livres');
            $sheet->setCellValue('C' . $row, 'Exemplaires');
            $sheet->setCellValue('D' . $row, 'Prix Totale');
            try {
                $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray($tableHeaderStyle);
            } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            }

            $row++;
            $smLivres = 0;
            $smSamples = 0;
            $smPrix = 0;
            foreach ($categories as $category) {

                $sheet->setCellValue('A' . $row, $category->getNom());
                $nbLivres = 0;
                if (!$year) {
                    $nbLivres = count($category->getLivres());
                } else {
                    try {
                        foreach ($livrRepo->findByYear($year) as $livreByYear) {
                            if ($livreByYear->getCategorie() === $category)
                                $nbLivres++;
                        }
                    } catch (Exception $e) {
                    }
                }
                $sheet->setCellValue('B' . $row, $nbLivres);

                $nbExemplaires = 0;
                $prixTotale = 0;
                if (!$year)
                    foreach ($category->getLivres() as $livre) {
                        $samples = count($livre->getExemplaires());
                        $nbExemplaires += $samples;
                        $prixTotale += $livre->getPrix() * $samples;
                    }
                else {
                    $samples = 0;
                    $prix = 0;
//                    //debug
//                    $debugSamples = [];
                    try {
                        foreach ($livrRepo->findByYear($year) as $livreByYear) {
                            if ($livreByYear->getCategorie() === $category) {
                                $samples += count($livreByYear->getExemplaires());
                                $prix += $livreByYear->getPrix() * $samples;
                            }
                        }
                    } catch (Exception $e) {
                    }
                    $nbExemplaires += $samples;
                    $prixTotale += $prix;
//
//                    dump($debugSamples);
//                    die();
                }
                $sheet->setCellValue('C' . $row, $nbExemplaires);
                $sheet->setCellValue('D' . $row, $prixTotale);
                $smLivres += $nbLivres;
                $smSamples += $nbExemplaires;
                $smPrix += $prixTotale;
                $row++;
            }
            $sheet->setCellValue('A' . $row, 'Totale');
            $sheet->setCellValue('B' . $row, $smLivres);
            $sheet->setCellValue('C' . $row, $smSamples);
            $sheet->setCellValue('D' . $row, $smPrix);
            try {
                $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray($tableHeaderStyle);
            } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            }
            try {
                $sheet->getStyle('A12:D' . $row)->applyFromArray($tableStyle);
            } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            }

        }


        foreach ($categories as $category) {

            if (count($category->getLivres()) < 1)
                continue;

            // Skipping 2 rows
            $row += 3;

            // Domaine title
            try {
                $sheet->mergeCells('A' . $row . ':' . ($inv ? 'E' : 'D') . $row);
            } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            }
            try {
                $sheet->getStyle('A' . $row)->applyFromArray($domaineStyle);
            } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            }
            $sheet->setCellValue('A' . $row, $category->getNom());

            // Skipping 1 row
            $row += 2;

            // Table header
            try {
                $sheet->mergeCells('A' . $row . ':A' . ($row + 1));
            } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            }
            try {
                $sheet->mergeCells('B' . $row . ':B' . ($row + 1));
            } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            }
            try {
                $sheet->mergeCells('C' . $row . ':D' . $row);
            } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            }
            if ($inv)
                try {
                    $sheet->mergeCells('E' . $row . ':E' . ($row + 1));
                } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
                }

            $sheet->setCellValue('A' . $row, 'Titre/Auteurs');
            $sheet->setCellValue('B' . $row, 'Editeurs/Année');
            $sheet->setCellValue('C' . $row, 'Exemplaires');
            $sheet->setCellValue('C' . ($row + 1), 'N° Inventaire');
            $sheet->setCellValue('D' . ($row + 1), 'Cote');
            if ($inv) {
                $sheet->setCellValue('E' . $row, 'Prix');
            }
            try {
                $sheet->getStyle('A' . $row . ':' . ($inv ? 'E' : 'D') . ($row + 1))->applyFromArray($tableHeaderStyle);
            } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            }

            $tableStart = $row + 2;
            $row += 3;

            foreach ($category->getLivres() as $book) {

                if ($year) {
                    if ($month) {
                        if (!(date_format($book->getDateAquis(), "m") == $month
                            && date_format($book->getDateAquis(), "Y") == $year)) {
                            continue;
                        }
                    } elseif
                    (!(date_format($book->getDateAquis(), "Y") == $year)) {
                        continue;
                    }
                }

                foreach ($book->getExemplaires() as $exemplaire) {
                    // Titre principale
                    $sheet->setCellValue('A' . $row, ucfirst($book->getTitrePrincipale()));
                    try {
                        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
                    } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
                    }
                    // Auteurs
                    $authors = [];
                    foreach ($book->getAuteurs() as $auteur) {
                        $authors[] = ucfirst($auteur->getNom());
                    }
                    $sheet->setCellValue('A' . ($row + 1), implode(',', $authors));

                    // Editeur
                    $sheet->setCellValue('B' . $row, $book->getEditeur());
                    // Année
                    $sheet->setCellValue('B' . ($row + 1), $book->getDateEdition());


                    // Inventaire
                    $sheet->setCellValue('C' . $row, $exemplaire->getNInventaire());
                    // Cote
                    $sheet->setCellValue('D' . $row, $exemplaire->getCote());

                    if ($inv)
                        $sheet->setCellValue('E' . $row, $book->getPrix());

                    $row += 3;

                }

                // Delimiter
                try {
                    $sheet->getStyle('A' . ($row - 1) . ':' . ($inv ? 'E' : 'D') . ($row - 1))
                        ->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
                } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
                }

                $row++;

            }

            // Setting overall outline
            try {
                $sheet->getStyle('A' . $tableStart . ':' . ($inv ? 'E' : 'D') . ($row - 1))->applyFromArray($tableStyle);
            } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            }

        }


        // Creating IOFactory object to download the file on the client side
        if ($mode == 'pdf') {
            $class = Dompdf::class;
            IOFactory::registerWriter('Pdf', $class);
            $writer = IOFactory::createWriter($spreadsheet, 'Pdf');

            $sheet->setShowGridlines(false)
                ->setPrintGridlines(false);
            $sheet->getPageSetup()
                ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
                ->setFitToPage(true)
                ->setHorizontalCentered(true)
                ->setPaperSize(PageSetup::PAPERSIZE_A3);

            $sheet->getHeaderFooter()->setFirstHeader('debugging test');

            $fileDate = date_format(new DateTime(), "d_m_y_H_i");
            $fileName = 'Inventaire_' . $fileDate . '.pdf';

            header('Content-Type: Content-type:application/pdf');
            header('Content-Disposition: attachment;filename="' . $fileName);
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            memory_get_usage();
        } else {

            // Redirect output to a client’s web browser (Xlsx)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . ($inv ? 'Inventaire des livres' : 'Acquisitions Documentaires')
                . date_format(new DateTime(), 'd/m/y H:i:s') . '.xlsx"');
            header('Cache-Control: max-age=0');

            $writer = null;

            try {
                $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            } catch (\PhpOffice\PhpSpreadsheet\Writer\Exception $e) {
            }

            // Save into php output
            try {
                $writer->save('php://output');
            } catch (\PhpOffice\PhpSpreadsheet\Writer\Exception $e) {
            }
        }
        exit();
    }

    public function getMonthName(String $lang, int $month = null)
    {

        $monthsArray = [
            'fr' => [
                1 => "Janvier",
                2 => "Février",
                3 => "Mars",
                4 => "Avril",
                5 => "Mai",
                6 => "Juin",
                7 => "Juillet",
                8 => "Aout",
                9 => "Septembre",
                10 => "Octobre",
                11 => "Novembre",
                12 => "Décembre",
            ],
            'ar' => [
                1 => "يناير",
                2 => "فبراير",
                3 => "مارس",
                4 => "أبريل",
                5 => "ماي",
                6 => "يونيو",
                7 => "يوليوز",
                8 => "غشت",
                9 => "شتنبر",
                10 => "أكتوبر",
                11 => "نونبر",
                12 => "دجنبر",
            ]
        ];

        if ($month)
            return $monthsArray[$lang][(int)$month];
        return '';
    }

    /**
     * @Route("/admin/invent",name="inventory")
     * @param Request $request
     * @param CategorieRepository $repository
     * @param LivreRepository $livreRepo
     * @return Response
     */
    public function inventory(Request $request, CategorieRepository $repository, LivreRepository $livreRepo)
    {
        if ($request->query->get('pdf_submit'))
            $this->export($repository, $request->query->get('date'), true, $livreRepo, 'pdf');
        elseif ($request->query->get('excel_submit'))
            $this->export($repository, $request->query->get('date'), true, $livreRepo);


        return $this->render('admin/book/inventaire.html.twig', [
            'controller_name' => 'IOController',
        ]);

    }

    /**
     * @Route("/admin/invent/get",name="get_invent")
     * @param Request $request
     * @param LivreRepository $livreRepository
     * @return JsonResponse
     * @throws Exception
     */
    public function getInvent(Request $request, LivreRepository $livreRepository)
    {

        if ($request->isXmlHttpRequest()) {

            if ($year = $request->query->get('year'))
                $livres = $livreRepository->findByYear($year);
            else
                $livres = $livreRepository->findAll();

            $books = [];

            foreach ($livres as $livre) {

                $book = [];
                $book['titrePrincipale'] = $livre->getTitrePrincipale();
                if (count($livre->getAuteurs()) > 0) {
                    $authors = [];
                    foreach ($livre->getAuteurs() as $auteur) {
                        $authors[] = $auteur->getNom();
                    }
                    $book['authors'] = implode(",", $authors);
                } else
                    $book['authors'] = "";
                $book['annee'] = $livre->getDateEdition() ? $livre->getDateEdition() : "";
                $book['edition'] = $livre->getEditeur() ? $livre->getEditeur() : "";
                $book['prix'] = $livre->getPrix() ? $livre->getPrix() : "";
                $book['quantité'] = count($livre->getExemplaires());
                if ($livre->getCategorie()) {
                    $book['categorie'] = $livre->getCategorie()->getNom();
                } else
                    $book['categorie'] = "";

                // Samples
                $book['exemplaires'] = [];
                foreach ($livre->getExemplaires() as $exemplaire) {
                    $sample = [];
                    $sample['inventaire'] = $exemplaire->getNInventaire();
                    $sample['cote'] = $exemplaire->getCote();
                    $book['exemplaires'][] = $sample;
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
            'message' => "Not a XmlHttpRequest"
        ], 400, [], false);


    }

    // Return String value of the given month in the given language

    /**
     * @Route("admin/import",name="import")
     * @param Request $request
     * @param ObjectManager $manager
     * @param LivreRepository $livreRepository
     * @param CategorieRepository $categorieRepository
     * @param AuteurRepository $auteurRepository
     * @return RedirectResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @throws Exception
     */
    public function import(Request $request, ObjectManager $manager, LivreRepository $livreRepository,
                           CategorieRepository $categorieRepository, AuteurRepository $auteurRepository)
    {
        $date = null;
        $file = null;

        if (!($date = $request->request->get('date')) && ($file = $request->files->get('excelFile'))) {
            $this->addFlash('error', "Une erreur est survenue lors de la requette");
            return $this->redirectToRoute('books');
        }

        $date = $request->request->get('date');
        $file = $request->files->get('excelFile');

        // Excel file name
        $inputFileName = $file->getPathName();
        // Loading spreadsheet
        $spreadsheet = IOFactory::load($inputFileName);
        // getting current active sheet
        $sheet = $spreadsheet->getActiveSheet();

        // Iterator starting at 7
        $i = 7;
        //Debuggin
        $byDomain = [];

        while ($sheet->getCell('A' . ($i + 1))->getValue() !== null) {

            // debugging
            $livres = [];
            // Getting domaine
            $domain = $sheet->getCell('A' . ($i + 1))->getValue();
            $category = $categorieRepository->findOneBy([
                'nom' => ucfirst(mb_strtolower($domain))
            ]);

            $i += 5;
            while ($sheet->getCell('A' . $i)->getValue() == !null) {

                $livre = new Livre();
                $title = null;

                // Categorie
                $livre->setCategorie($category);
                $livre->setAddedBy($this->getUser());

                // Titre
                $title = $sheet->getCell('A' . $i)->getValue();
                $livre->setTitrePrincipale($title);
                // Auteurs
                $authors = $sheet->getCell('A' . ($i + 1))->getValue();
                if ($authors !== null) {
                    $authorsList = [];
                    if (strpos($authors, '/')) {
                        // Get alll authors
                        $authorsList = explode('/', $authors);

                    } else {
                        // Get first author
                        $authorsList[] = explode(',', $authors)[0];
                    }

                    // search if author exists or not
                    foreach ($authorsList as $authorName) {

                        if ($exists = $auteurRepository->findOneBy(['nom' => $authorName])) {
                            $livre->addAuteur($exists);
                        } else {
                            $newAuth = new Auteur();
                            $newAuth->setNom($authorName);
                            $livre->addAuteur($newAuth);
                        }

                    }

                }

                // Editeur et date
                $edit_year_value = $sheet->getCell('B' . $i)->getValue();

                if ($edit_year_value !== null) {
                    if (strpos($edit_year_value, '/')) {

                        // if it contains delimiter
                        $edit_year = explode('/', $edit_year_value);
                        $editor = $edit_year[0];
                        $year = $edit_year[1];

                        $livre->setDateEdition((int)$year);
                        $livre->setEditeur($editor);

                    } elseif (preg_match('/\\d/', $edit_year_value)) {
                        // if it's a number => year
                        $livre->setDateEdition((int)$edit_year_value);
                    } else {
                        // Edition
                        $livre->setEditeur($edit_year_value);
                    }

                }

                // Exemplaires
                $Ninventaire = $sheet->getCell('C' . $i)->getValue();
                $cote = $sheet->getCell('D' . $i)->getValue();

                $exemplaire = new Exemplaire();
                $exemplaire->setNInventaire((float)$Ninventaire);
                $exemplaire->setCote($cote);

                $livre->addExemplaire($exemplaire);

                $i += 3;
                while ($title == $sheet->getCell('A' . $i)->getValue()) {
                    $Ninventaire = $sheet->getCell('C' . $i)->getValue();
                    $cote = $sheet->getCell('D' . $i)->getValue();

                    $exemplaire = new Exemplaire();
                    $exemplaire->setNInventaire((float)$Ninventaire);
                    $exemplaire->setCote($cote);

                    $livre->addExemplaire($exemplaire);
                    $i += 3;
                }

                $livre->setDateAquis(
                    new DateTime(
                        date('Y-m-d H:i:s',
                            strtotime(
                                str_replace('/', '-', '01/' . $date . date('H:i:s'))
                            )
                        )
                    )
                );
                $manager->persist($livre);
                $livres[] = $livre;

            }

            $byDomain[] = $livres;
        }

        $manager->flush();
        $this->addFlash("success", "Livres Importés !");
        return $this->redirectToRoute("books");
    }

    public function getCatRepo(CategorieRepository $repository)
    {

        $categories = $repository->findAll();
        $categories->
        $data = [];

        foreach ($categories as $category) {
            //TODO: build json data
        }

    }

}
