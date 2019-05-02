<?php

namespace App\Controller;

use App\Entity\Auteur;
use App\Entity\Categorie;
use App\Entity\Exemplaire;
use App\Entity\Livre;
use App\Repository\AuteurRepository;
use App\Repository\CategorieRepository;
use App\Repository\LivreRepository;
use Doctrine\Common\Persistence\ObjectManager;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IOController extends AbstractController
{
    /**
     * @Route("/admin/export", name="export")
     * @param Request $request
     * @param CategorieRepository $repository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, CategorieRepository $repository)
    {
        if ($request->query->get('submit'))
            $this->export($repository, $request->query->get('date'));

        return $this->render('admin/book/reports.html.twig', [
            'controller_name' => 'IOController',
        ]);
    }


    /**
     * @Route("/admin/invent",name="inventory")
     */
    public function inventory(Request $request, CategorieRepository $repository, LivreRepository $livreRepo)
    {
        if ($request->query->get('submit'))
            $this->export($repository, $request->query->get('date'), true, $livreRepo);


        return $this->render('admin/book/inventaire.html.twig', [
            'controller_name' => 'IOController',
        ]);

    }

    public function export(CategorieRepository $repository, String $date = null, bool $inv = false, LivreRepository $livrRepo = null)
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
        $sheet = $spreadsheet->getActiveSheet();

        // Changing cells width
        $sheet->getColumnDimension('A')->setWidth(72.43);
        $sheet->getColumnDimension('B')->setWidth(22.14);
        $sheet->getColumnDimension('C')->setWidth(16.14);
        $sheet->getColumnDimension('D')->setWidth(16.14);

        // Merging cells
        $sheet->mergeCells('A4:D5');
        $sheet->mergeCells('A6:D6');

        // Styling
        $headerStyles = [
            'font' => [
                'bold' => true,
                'size' => 13,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ]
        ];
        $titleStyles = [
            'font' => [
                'bold' => true,
                'size' => 14,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ]
        ];
        $domaineStyle = [
            'font' => [
                'bold' => true,
                'size' => 14
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => [
                    'argb' => 'FFE6E6FA'
                ]
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
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
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => [
                    'argb' => 'FF7F55B9'
                ]
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ]
        ];
        $tableHeaderStyle = [
            'font' => [
                'bold' => true,
                'size' => 11,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                ]
            ]
        ];
        $tableStyle = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                ]
            ]
        ];
        $centerStyle = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ]
        ];

        $sheet->getStyle('A')->getAlignment()->setWrapText(true);
        $sheet->getStyle('B')->getAlignment()->setWrapText(true);

        // Applying styles to header
        $sheet->getStyle('A1:A3')->applyFromArray($headerStyles);
        $sheet->getStyle('A4:A6')->applyFromArray($titleStyles);

        // Columns alignment
        $sheet->getStyle('B:D')->applyFromArray($centerStyle);

        // Set Header values
        $sheet->setCellValue('A1', 'Ecole Supérieure de Technologie de Salé');
        $sheet->setCellValue('D1', 'Le ' . date_format(new \DateTime(), 'd/m/Y'));
        $sheet->setCellValue('A2', 'Direction des Etudes');
        $sheet->setCellValue('A3', 'Bibliothèque');
        if (!$inv) {

            $sheet->setCellValue('A4', 'NOUVELLES ACQUISITIONS DOCUMENTAIRES / BIBLIOTHÈQUE EST-SALÉ ' .
                $this->getMonthName("fr", $month) . ' ' . $year);
            $sheet->setCellValue('A6', $year . ' ' . ' قائمة الكتب المقتناة ' . $this->getMonthName("ar", $month));

            // current row value
            $row = 6;

        } else {

            $sheet->setCellValue('A4', 'LISTE DES LIVRES / BIBLIOTHÈQUE EST-SALÉ ' .
                $this->getMonthName("fr", $month) . ' ' . $year);
            $sheet->setCellValue('A6', $year . ' ' . ' قائمة الكتب ' . $this->getMonthName("ar", $month));

            $row = 9;

            // OVERVIEW
            //Header
            $sheet->mergeCells('A' . $row . ':D' . $row);
            $sheet->getStyle('A' . $row)->applyFromArray($overviewStyle);
            $sheet->setCellValue('A' . $row, "VUE D'ENSEMBLE");

            $row += 2;
            // Table
            $sheet->setCellValue('A' . $row, 'Catégorie');
            $sheet->setCellValue('B' . $row, 'Livres');
            $sheet->setCellValue('C' . $row, 'Exemplaires');
            $sheet->setCellValue('D' . $row, 'Prix Totale');
            $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray($tableHeaderStyle);

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
                    foreach ($livrRepo->findByYear($year) as $livreByYear) {
                        if ($livreByYear->getCategorie() === $category)
                            $nbLivres++;
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
                    foreach ($livrRepo->findByYear($year) as $livreByYear) {
                        if ($livreByYear->getCategorie() === $category) {
                            $samples += count($livreByYear->getExemplaires());
                            $prix += $livreByYear->getPrix() * $samples;
                        }
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
            $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray($tableHeaderStyle);
            $sheet->getStyle('A12:D' . $row)->applyFromArray($tableStyle);

        }


        foreach ($categories as $category) {

            if (count($category->getLivres()) < 1)
                continue;

            // Skipping 2 rows
            $row += 3;

            // Domaine title
            $sheet->mergeCells('A' . $row . ':D' . $row);
            $sheet->getStyle('A' . $row)->applyFromArray($domaineStyle);
            $sheet->setCellValue('A' . $row, $category->getNom());

            // Skipping 1 row
            $row += 2;

            // Table header
            $sheet->mergeCells('A' . $row . ':A' . ($row + 1));
            $sheet->mergeCells('B' . $row . ':B' . ($row + 1));
            $sheet->mergeCells('C' . $row . ':D' . $row);

            $sheet->setCellValue('A' . $row, 'Titre/Auteurs');
            $sheet->setCellValue('B' . $row, 'Editeurs/Année');
            $sheet->setCellValue('C' . $row, 'Exemplaires');
            $sheet->setCellValue('C' . ($row + 1), 'N° Inventaire');
            $sheet->setCellValue('D' . ($row + 1), 'Cote');
            $sheet->getStyle('A' . $row . ':D' . ($row + 1))->applyFromArray($tableHeaderStyle);

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
                    $sheet->getStyle('A' . $row)->getFont()->setBold(true);
                    // Auteurs
                    $authors = [];
                    foreach ($book->getAuteurs() as $auteur) {
                        $authors[] = ucfirst($auteur->getNom());
                    }
                    $sheet->setCellValue('A' . ($row + 1), implode(',', $authors));

                    // Editeur
                    $sheet->setCellValue('B' . $row, '*Editeur*');
                    // Année
                    $sheet->setCellValue('B' . ($row + 1), $book->getDateEdition());


                    // Inventaire
                    $sheet->setCellValue('C' . $row, $exemplaire->getNInventaire());
                    // Cote
                    $sheet->setCellValue('D' . $row, $exemplaire->getCote());

                    $row += 3;

                }

                $row++;

            }

            // Setting overall outline
            $sheet->getStyle('A' . $tableStart . ':D' . ($row - 1))->applyFromArray($tableStyle);

        }


        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Acquisitions Documentaires ' . date_format(new \DateTime(), "d/m/y H:i:s") . '.xlsx"');
        header('Cache-Control: max-age=0');


        // Creating IOFactory object to download the file on the client side
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        // Save into php output
        $writer->save('php://output');
        exit();
    }

    /**
     * @Route("/admin/import",name="import")
     * @param ObjectManager $manager
     * @param LivreRepository $livreRepository
     * @param CategorieRepository $categorieRepository
     * @param AuteurRepository $auteurRepository
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function import(ObjectManager $manager, LivreRepository $livreRepository,
                           CategorieRepository $categorieRepository, AuteurRepository $auteurRepository)
    {

        // Excel file name
        $inputFileName = __DIR__ . '/CultureGen.xlsx';
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
                $livre->setDateAquis(new \DateTime());

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
                $livres[] = $livre;
            }

            $byDomain[] = $livres;

        }
        dump($byDomain);
        die();
    }

    // Return String value of the given month in the given language
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
}