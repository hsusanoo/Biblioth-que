<?php

namespace App\DataFixtures;

use App\Entity\Auteur;
use App\Entity\Categorie;
use App\Entity\Descripteur;
use App\Entity\Exemplaire;
use App\Entity\Livre;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        // Ctreating Categories
        $categories_array = [
            "Informatique",
            "Infographie",
            "Culture générale",
            "Techniques de droit",
            "Statistique",
            "Chimie",
            "Génie civile",
            "Mathématiques",
            "Management/Gestion",
            "Sciences de l'environnement",
            "Techniques Financières/Techniques Fiscales",
            "Management et organisation sports",
            "Electronique",
            "Economie",
            "Physique",
            "Génie électronique",
            "Métrologie/Mesures physiques",
            "Marketing",
            "Electronique"
        ];

        $categories = [];
        foreach ($categories_array as $item) {
            $cat = new Categorie();
            $cat->setNom($item);
            $manager->persist($cat);
            $categories[] = $cat;
        }


        $faker = Factory::create();

        // Creating authors
        $authors = [];
        for ($i = 0; $i < 70; $i++) {
            $author = new Auteur();
            $author->setNom($faker->name);
            $manager->persist($author);
            $authors[] = $author;
        }

        // Creating tags
        $tags = [];
        for ($i = 0; $i < 200; $i++) {
            $tag = new Descripteur();
            $tag->setNom($faker->name);
            $manager->persist($tag);
            $tags[] = $tag;
        }

        // Creating multiple books
        for ($i = 0; $i < 100; $i++) {

            // Book infos
            $book = new Livre();
            $book->setCouverture($faker->imageUrl(350, 500, 'abstract'));
            $book->setIsbn($faker->isbn10);
            $book->setEditeur($faker->name);
            $book->setTitrePrincipale($faker->sentence($faker->numberBetween(3,7)));
            $book->setTitreSecondaire($faker->sentence($faker->numberBetween(3,7)));
            $book->setDateAquis($faker->dateTimeThisYear('now'));
            $book->setUpdatedAt(new \DateTime());
            $book->setDateEdition($faker->year());
            $book->setPrix($faker->numberBetween(100, 500));
            $book->setNPages($faker->numberBetween(50, 400));
            $book->setObservation($faker->text());
            $book->setAuteurs($faker->randomElements($authors, $faker->numberBetween(1, 4)));
            $book->setCategorie($faker->randomElement($categories));
            $book->setDescripteurs($faker->randomElements($tags, $faker->numberBetween(1, 4)));
            // Samples
            for ($j = 0; $j < mt_rand(3, 10); $j++) {
                $sample = new Exemplaire();
                $sample->setNInventaire($faker->randomFloat(7,11111.10,55555.99));
                $sample->setCote(strtoupper($faker->word) . "-" . $faker->numberBetween(10, 30));
                $manager->persist($sample);
                $book->addExemplaire($sample);
            }

        }

        $manager->persist($book);

        $manager->flush();
    }
}
