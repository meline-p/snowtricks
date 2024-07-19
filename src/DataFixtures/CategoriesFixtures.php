<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoriesFixtures extends Fixture
{
    private $counter = 1;

    public function __construct(private SluggerInterface $slugger)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $this->createCategory('Big Air', $manager);
        $this->createCategory('Flatland', $manager);
        $this->createCategory('Freeride', $manager);
        $this->createCategory('Freestyle', $manager);
        $this->createCategory('Jibbing', $manager);
        $this->createCategory('Pipe', $manager);

        $manager->flush();
    }

    public function createCategory(string $name, ObjectManager $manager): Category
    {
        $category = new Category();
        $category->setName($name);
        $category->setSlug($this->slugger->slug($name)->lower());
        $manager->persist($category);

        // ajouter une référence categorie
        $this->addReference('cat-'.$this->counter, $category);
        $this->counter++;

        return $category;
    }
}
