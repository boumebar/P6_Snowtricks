<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    protected $encoder;

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User;
        $hash = $this->encoder->hashPassword($admin, "password");

        $admin->setUsername("Admin")
            ->setEmail("admin@gmail.com")
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

        $manager->flush();
    }
}
