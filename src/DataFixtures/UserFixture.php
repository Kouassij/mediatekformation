<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUsername("admin");
        $plaintextPassword = "admin";
        $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $plaintextPassword
                );
        $user->setPassword($hashedPassword);
        $user->setPassword($hashedPassword);
        $user->setRoles(['Role_ADMIN']);
        $manager->persist($user);
        $manager->flush();
    }
}
