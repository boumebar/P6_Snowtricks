<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    protected $encoder;
    protected $slugger;

    public function __construct(UserPasswordHasherInterface $encoder, SluggerInterface $slugger)
    {
        $this->encoder = $encoder;
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create("fr_FR");


        /*******************************************************
         * 
         *                              USER
         * 
         *********************************************************/
        $admin = new User;
        $hash = $this->encoder->hashPassword($admin, "password");

        $admin->setUsername($faker->firstName())
            ->setEmail($faker->email())
            ->setPassword($hash)
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);

        for ($i = 0; $i < 5; $i++) {
            $user = new User;
            $hash = $this->encoder->hashPassword($user, "password");
            $user->setUsername("user$i")
                ->setEmail("user$i@gmail.com")
                ->setPassword($hash);
            $manager->persist($user);
        }

        /*******************************************************
         * 
         *                             CATEGORIES and TRICKS
         * 
         *********************************************************/

        $categoryNames = [
            'rotations',
            'grabs',
            'flips',
            'rotations désaxées',
            'slides',
            'Old school',
            'stall',
            'one foot tricks',
        ];
        foreach ($categoryNames as $name) {
            $category = new Category;
            $category->setName($name)
                ->setSlug(strtolower($this->slugger->slug($category->getName())));

            $manager->persist($category);

            for ($i = 0; $i < mt_rand(3, 6); $i++) {
                $trick = new Trick;
                $trick->setName($faker->words(2, true))
                    ->setSlug(strtolower($this->slugger->slug($trick->getName())))
                    ->setDescription($faker->paragraphs(5, true))
                    ->setCategory($category);
                $manager->persist($trick);

                for ($j = 1; $j < rand(1, 6); $j++) {
                    $comment = new Comment;
                    $comment->setContent($faker->paragraphs(1, true))
                        ->setTrick($trick);
                    $manager->persist($comment);
                }
            }
        }



        $manager->flush();
    }
}
