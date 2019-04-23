<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
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

    public function export(CategorieRepository $repository, String $date = null)
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
        $sheet->getColumnDimension('A')->setWidth(67.86);
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

        // Applying styles to header
        $sheet->getStyle('A1:A3')->applyFromArray($headerStyles);
        $sheet->getStyle('A4:A6')->applyFromArray($titleStyles);

        // Columns alignment
        $sheet->getStyle('B:D')->applyFromArray($centerStyle);

        // Set Header values
        $sheet->setCellValue('A1', 'Ecole Supérieure de Technologie de Salé');
        $sheet->setCellValue('A2', 'Direction des Etudes');
        $sheet->setCellValue('A3', 'Bibliothèque');
        $sheet->setCellValue('A4', 'NOUVELLES ACQUISITIONS DOCUMENTAIRES / BIBLIOTHÈQUE EST-SALÉ ' .
            $this->getMonthName("fr", $month) . ' ' . $year);
        $sheet->setCellValue('A6', $year . ' ' . ' قائمة الكتب المقتناة ' . $this->getMonthName("ar", $month));

        // current row value
        $row = 6;


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
                    } elseif (!(date_format($book->getDateAquis(), "Y") == $year)) {
                        continue;
                    }
                }

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

                // Exemplaires
                foreach ($book->getExemplaires() as $exemplaire) {

                    // Inventaire
                    $sheet->setCellValue('C' . $row, $exemplaire->getNInventaire());
                    // Cote
                    $sheet->setCellValue('D' . $row, $exemplaire->getCote());

                    $row++;

                }

                $row++;

            }

            // Setting overall outline
            $sheet->getStyle('A' . $tableStart . ':D' . ($row - 1))->applyFromArray($tableStyle);

        }


        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Acquisitions Documentaires ' . time() . '.xlsx"');
        header('Cache-Control: max-age=0');


        // Creating IOFactory object to download the file on the client side
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        // Save into php output
        $writer->save('php://output');
        exit();
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
