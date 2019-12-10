<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    const CATEGORIES = [
        'Action',
        'Aventure',
        'Animation',
        'Comedie',
        'Drame',
        'Famille',
        'Fantastique',
        'Historique',
        'Horreur',
        'Policier',
        'Romance',
        'Science Fiction',
        'Thriller'

    ];

    public function load(ObjectManager $manager)
    {

        foreach (self::CATEGORIES as $categorie => $name) {
            $category = new Category();
            $category->setName($name);
            $manager->persist($category);
            $this->addReference('categorie_' . $categorie, $category);
        }

        $manager->flush();

    }
}
