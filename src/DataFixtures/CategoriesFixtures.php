<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoriesFixtures extends Fixture
{

    private $counter = 1;

    public function __construct(private SluggerInterface $slugger){}

    public function load(ObjectManager $manager): void
    {
        $this->createCategory('Sauts', $manager);
        $this->createCategory('Figures sur rail', $manager);
        $this->createCategory('Figures sur halfpipe', $manager);
        $this->createCategory('Figures de flatland', $manager);

        $manager->flush(); 
    }

    public function createCategory(string $name, ObjectManager $manager)
    {
        $category = new Category();
        $category->setName($name);
        $category->setSlug($this->slugger->slug($name)->lower());
        $manager->persist($category);

        //ajouter une référence categorie
        $this->addReference('cat-'.$this->counter, $category);
        $this->counter++;

        return $category;
    }
}
