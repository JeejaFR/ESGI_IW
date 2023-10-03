<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher,
    ){

    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $user = (new User())
            ->setFirstname('admin')
            ->setLastname('admin')
            ->setEmail('admin@test.com')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword(
                $this->hasher->hashPassword(new User(),'test1234')
            );

        $manager->persist($user); // on prepare l'envoie de l'objet $user dans la base de donnée

        $manager->flush(); // un peu comme le commit de git pour que ca envoie à la base de donnée
    }
}
