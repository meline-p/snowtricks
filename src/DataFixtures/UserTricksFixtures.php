<?php

namespace App\DataFixtures;

use App\Entity\UserTrick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class UserTricksFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        $operations = ['create', 'update', 'delete'];

        for($ut = 1; $ut <= 5; $ut++){
            $user_trick = new UserTrick();
            $date = $faker->dateTimeBetween('-2 years', 'now');
            $user_trick->setDate($date);

            $randomOperation = $operations[array_rand($operations)];
            $user_trick->setOperation($randomOperation);

            //on va chercher une reference de user
            $user = $this->getReference('usr-'.rand(1,5));
            $user_trick->setUser($user);

             //on va chercher une reference de trick
             $trick = $this->getReference('trk-'.rand(1,4));
             $user_trick->setTrick($trick);

            $manager->persist($user_trick);
        }

        $manager->flush();
    }
}
