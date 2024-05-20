<?php

namespace App\DataFixtures;

use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImagesFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private SluggerInterface $slugger)
    {
    }

    public function load(ObjectManager $manager): void
    {
        // $faker = Faker\Factory::create('fr_FR');

        // for ($img = 1; $img <= 5; $img++) {
        //     $image = new Image();
        //     $image->setName($faker->regexify('[A-Za-z0-9]{20}'));
        //     $image->setExtension($faker->fileExtension());

        //     // on va chercher une reference de trick
        //     $trick = $this->getReference('trk-'.rand(1, 5));
        //     $image->setTrick($trick);

        //     $manager->persist($image);
        // }

        // $manager->flush();
    }

    public function getDependencies()
    {
        return [
            TricksFixtures::class,
        ];
    }
}
