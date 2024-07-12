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
        for ($trick = 1; $trick <= 12; $trick++) {
            $this->createOrUpdateUserTrick('-3 weeks', '-1 week', 'create', $trick, $manager);
            $this->createOrUpdateUserTrick('-3 days', 'now', 'update', $trick, $manager);
        }

        $manager->flush();
    }

    public function createOrUpdateUserTrick($dateStart, $dateEnd, $operation, $trick, ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        $user_trick = new UserTrick();

        $date = $faker->dateTimeBetween($dateStart, $dateEnd);
        $user_trick->setDate($date);

        $user_trick->setOperation($operation);

        // on va chercher une reference de user
        $user = $this->getReference('usr-'.rand(1, 5));
        $user_trick->setUser($user);

        // on va chercher une reference de trick
        $trick = $this->getReference('trk-'.$trick);
        $user_trick->setTrick($trick);

        $manager->persist($user_trick);
    }
}
