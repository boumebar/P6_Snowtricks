<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $admin = new User;

        $admin->setUsername("Admin")
            ->setEmail("admin@gmail.com")
            ->setPassword("password")
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);

        for ($i = 0; $i < 5; $i++) {
            $user = new User;
            $user->setUsername("user$i")
                ->setEmail("user$i@gmail.com")
                ->setPassword("password");
            $manager->persist($user);
        }

        $manager->flush();
    }
}
