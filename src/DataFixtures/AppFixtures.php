<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Category;
use App\Entity\Account;
use App\Entity\Article;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher
    )   { }


    public function load(ObjectManager $manager): void
    {
        $categories = [];
        $users = [];

        //Instancier Faker
        $faker = Faker\Factory::create("fr_FR");

        //Boucle pour ajouter 100 catégories
        for ($i=0; $i < 100 ; $i++) {
            $category = new Category();
            //Setter le label avec un métier aléatoire
            $category
                ->setLabel($faker->unique()->word(1));
            //Mettre en cache la catégorie
            $manager->persist($category);
            $categories[] = $category;
        }

        for ($i=0; $i < 50; $i++) {
            $user = new Account();
            $user
                ->setFirstname($faker->firstName())
                ->setLastname($faker->lastName())
                ->setEmail(strtolower($user->getFirstname()) . strtolower($user->getLastname()) . "@" . $faker->freeEmailDomain())
                ->setPassword($this->hasher->hashPassword($user, $faker->word(3)))
                ->setRoles(["ROLE_USER"]);
            $manager->persist($user);
            $users[] = $user;
        }

        for ($i=0; $i < 200 ; $i++) {
            $article = new Article();
            $article
                ->setTitle($faker->word(3))
                ->setContent($faker->text(2000))
                ->setCreatedAt(new \DateTimeImmutable($faker->date('Y-m-d')))
                ->setUser($users[$faker->numberBetween(0, 49)])
                ->addCategory($categories[$faker->numberBetween(0, 32)])
                ->addCategory($categories[$faker->numberBetween(33, 66)])
                ->addCategory($categories[$faker->numberBetween(67, 99)]);
            $manager->persist($article);
        }
        //Synchroniser avec la BDD
        $manager->flush();
    }
}
