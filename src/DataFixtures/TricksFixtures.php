<?php

namespace App\DataFixtures;

use App\Entity\Trick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\String\Slugger\SluggerInterface;

class TricksFixtures extends Fixture
{
    private $counter = 1;

    public function __construct(private SluggerInterface $slugger)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($trk = 1; $trk <= 5; $trk++) {
            $trick = new Trick();
            $trick->setName($faker->text(10));
            $trick->setDescription($faker->text(50));
            $trick->setSlug($this->slugger->slug($trick->getName())->lower());

            // on va chercher une reference de catégorie
            $category = $this->getReference('cat-'.rand(1, 4));
            $trick->setCategory($category);

            // ajouter une référence trick
            $this->addReference('trk-'.$this->counter, $trick);
            $this->counter++;

            $manager->persist($trick);
        }

        $manager->flush();
    }
}
