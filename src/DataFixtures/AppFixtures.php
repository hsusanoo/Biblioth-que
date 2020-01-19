<?php

namespace App\DataFixtures;

use App\Entity\Auteur;
use App\Entity\Categorie;
use App\Entity\Descripteur;
use App\Entity\Exemplaire;
use App\Entity\Livre;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }


    public function load(ObjectManager $manager): void
    {

        // Creating random admin user

        $users = [];
        $user = new User();

        $user->setEmail('admin@gmail.com');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'admin'));
        $user->setNom('ADMIN');
        $user->setPrenom('Admin');
        $user->setPhone('0548178727');
        $user->setRoles(['ROLE_ADMIN', 'ROLE_GESTION']);

        $users[] = $user;
        $manager->persist($user);


        $faker = Factory::create();

        for ($i = 0; $i < 3; $i++) {
            $user = new User();

            $user->setEmail($faker->email);
            $user->setPassword($this->passwordEncoder->encodePassword($user, 'admin'));
            $user->setNom($faker->lastName);
            $user->setPrenom($faker->firstName);
            $user->setPhone('06' . $faker->randomNumber(8));
            $user->setRoles(['ROLE_ADMIN']);

            $users[] = $user;
            $manager->persist($user);
        }


        // Ctreating Categories
        $categories_array = [
            'Informatique',
            'Infographie',
            'Culture générale',
            'Techniques de droit',
            'Statistique',
            'Chimie',
            'Génie civil',
            'Mathématiques',
            'Management/gestion',
            'Sciences de l\'environnement',
            'Techniques Financières/techniques Fiscales',
            'Management et organisation sports',
            'Electronique',
            'Economie',
            'Physique',
            'Génie électronique',
            'Métrologie/mesures physiques',
            'Marketing',
            'Electronique'
        ];

        $categories = [];
        foreach ($categories_array as $item) {
            $cat = new Categorie();
            $cat->setNom($item);
            $manager->persist($cat);
            $categories[] = $cat;
        }

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
        for ($i = 0; $i < 300; $i++) {

            // Book infos
            $book = new Livre();
            $book->setAddedBy($faker->randomElement($users));
//            $book->setCouverture($faker->imageUrl(350, 500, 'abstract'));     // lorempixel is down
            $book->setCouverture('https://loremflickr.com/350/500/book');
            $book->setIsbn($faker->isbn10);
            $book->setEditeur($faker->name);
            $book->setTitrePrincipale($faker->sentence($faker->numberBetween(3, 7)));
            $book->setTitreSecondaire($faker->sentence($faker->numberBetween(3, 7)));
            $book->setDateAquis($faker->dateTimeBetween('-10 years', 'now'));
            $book->setUpdatedAt(new DateTime());
            $book->setDateEdition($faker->year());
            $book->setPrix($faker->numberBetween(100, 500));
            $book->setNPages($faker->numberBetween(50, 400));
            $book->setObservation($faker->text());
            $book->setAuteurs($faker->randomElements($authors, $faker->numberBetween(1, 4)));
            $book->setCategorie($faker->randomElement($categories));
            $book->setDescripteurs($faker->randomElements($tags, $faker->numberBetween(1, 4)));
            // Samples
            try {
                for ($j = 1, $jMax = random_int(2, 10); $j < $jMax; $j++) {
                    $sample = new Exemplaire();
                    $sample->setNInventaire($faker->randomFloat(7, 11111.10, 55555.99));
                    $sample->setCote(strtoupper($faker->word) . '-' . $faker->numberBetween(10, 30));
                    $manager->persist($sample);
                    $book->addExemplaire($sample);
                }
            } catch (Exception $e) {
            }

            $manager->persist($book);

        }

        $manager->flush();
    }
}
