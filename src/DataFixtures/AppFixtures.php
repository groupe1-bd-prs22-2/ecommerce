<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Category;

/**
 * Gestion des données par défaut de test de l'application.
 */
class AppFixtures extends Fixture
{
    /**
     * @var array Liste des catégories par défaut
     */
    private const DEFAULT_CATEGORIES = [
        ['name' => 'Vêtements'],
        ['name' => 'Figurines'],
        ['name' => 'Manga'],
        ['name' => 'One Piece'],
        ['name' => 'Dragon Ball'],
        ['name' => 'Naruto'],
    ];

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        // Création des catégories
        foreach (self::DEFAULT_CATEGORIES as $category) {
            $manager->persist(
                (new Category())->setName($category['name'])
            );
        }

        // Enregistrement des modifications
        $manager->flush();
    }
}
