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
        $img_names = [
            1 => "6687fa8ab1945.avif",
            2 => "6687fa9b2925e.avif",
            3 => "6687faaa0a348.avif",
            4 => "6687fab882066.avif",
            5 => "6687fac45fa9c.avif",
            6 => "6687fad057f25.avif",
            7 => "6687fadc4375e.avif",
            8 => "6687faf8bdc64.avif",
            9 => "6687fb9829702.avif",
            10 => "6687fbadcac52.avif",
            11 => "6687fbbc192d2.avif",
            12 => "6687fbccaa3e9.avif"
        ];

        for ($i = 1; $i <= 12; $i++) {

            $image = new Image();

            $image->setName($img_names[$i]);
            $image->setExtension("avif");

            // on va chercher une reference de trick
            $trick = $this->getReference('trk-'.$i);
            $image->setTrick($trick);

            $manager->persist($image);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            TricksFixtures::class,
        ];
    }
}
