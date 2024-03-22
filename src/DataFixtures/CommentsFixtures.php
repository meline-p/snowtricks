<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class CommentsFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for($cmt = 1; $cmt <= 5; $cmt++){
            $comment = new Comment();
            $comment->setContent($faker->text(50));

            //on va chercher une reference de user
            $user = $this->getReference('usr-'.rand(1,5));
            $comment->setUser($user);

             //on va chercher une reference de trick
             $trick = $this->getReference('trk-'.rand(1,4));
             $comment->setTrick($trick);

            $manager->persist($comment);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UsersFixtures::class,
        ];
    }
}
