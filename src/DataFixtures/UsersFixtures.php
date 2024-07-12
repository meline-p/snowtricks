<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class UsersFixtures extends Fixture
{
    private $counter = 1;

    public function __construct(
        private UserPasswordHasherInterface $passwordEncoder,
        private SluggerInterface $slugger
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($usr = 1; $usr <= 5; $usr++) {
            $user = new User();
            $user->setEmail($faker->email);

            $lastName = $faker->lastName;
            $user->setLastName(strtoupper($lastName));

            $firstName = $faker->firstName;
            $user->setFirstName(ucfirst($firstName));
            $user->setUsername($this->slugger->slug($faker->userName)->lower());

            $user->setPassword(
                $this->passwordEncoder->hashPassword($user, 'secret')
            );

            $manager->persist($user);

            // ajouter une référence user
            $this->addReference('usr-'.$usr, $user);
            $this->counter++;
        }

        $manager->flush();
    }
}
