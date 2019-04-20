<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use Doctrine\Common\Persistence\ObjectManager;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IOController extends AbstractController
{
    /**
     * @Route("/admin/export", name="export")
     * @param ObjectManager $manager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(CategorieRepository $repository)
    {

        $this->export($repository);

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'IOController',
        ]);
    }

    public function export(CategorieRepository $repository)
    {

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
            ]
        ];

        // Applying styles to header
        $sheet->getStyle('A1:A3')->applyFromArray($headerStyles);
        $sheet->getStyle('A4:A6')->applyFromArray($titleStyles);


        // Set Header values
        $sheet->setCellValue('A1', 'Ecole Supérieure de Technologie de Salé');
        $sheet->setCellValue('A2', 'Direction des Etudes');
        $sheet->setCellValue('A3', 'Bibliothèque');
        $sheet->setCellValue('A4', 'NOUVELLES ACQUISITIONS DOCUMENTAIRES / BIBLIOTHÈQUE EST-SALÉ MARS 2019');
        $sheet->setCellValue('A6', ' قائمة الكتب المقتناة مارس 2019');

        // current row value
        $row = 6;


        foreach ($categories as $category) {

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


            foreach ($category->getLivres() as $book) {

                $row+=2;

                // Titre principale
                $sheet->setCellValue('A' . $row, ucfirst($book->getTitrePrincipale()));
                // Auteurs
                $authors = [];
                foreach ($book->getAuteurs() as $auteur) {
                    $authors[] = ucfirst($auteur->getNom());
                }
                $sheet->setCellValue('A' . ($row + 1), implode(',', $authors));

                // Editeur
                $sheet->setCellValue('B'.$row,'*Editeur*');
                // Année
                $sheet->setCellValue('B'.($row+1),$book->getDateEdition());

                // Exemplaires
                foreach ($book->getExemplaires() as $exemplaire) {

                    // Inventaire
                    $sheet->setCellValue('C'.$row,$exemplaire->getNInventaire());
                    // Cote
                    $sheet->setCellValue('D'.$row,$exemplaire->getCote());

                    $row++;

                }

                $row++;

            }


        }


        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="simple-' . time() . '.xlsx"');
        header('Cache-Control: max-age=0');


        // Creating IOFactory object to download the file on the client side
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        // Save into php output
        $writer->save('php://output');
        exit();
    }
}
