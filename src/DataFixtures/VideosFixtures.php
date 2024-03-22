<?php

namespace App\DataFixtures;

use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;
use Faker;

class VideosFixtures extends Fixture
{
    public function __construct(private SluggerInterface $slugger){}

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for($vdo = 1; $vdo <= 5; $vdo++){
            $video = new Video();
            $video->setUrl($faker->url());
            
            //on va chercher une reference de trick
            $trick = $this->getReference('trk-'.rand(1,5));
            $video->setTrick($trick);

            $manager->persist($video);
        }

        $manager->flush();
    }
}
