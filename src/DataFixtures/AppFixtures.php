<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Category;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Gestion des données par défaut de test de l'application.
 */
class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

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

        // Création d'un super admin par défaut
        $sAdmin = (new User())
            ->setEmail('administrateur@mangamania.com')
            ->setRoles(['ROLE_ADMIN'])
            ->setFirstname('Administrateur')
            ->setLastname('SUPER')
        ;

        $sAdmin->setPassword(
            $this->hasher->hashPassword($sAdmin, 'Adm1nTest')
        );

        $manager->persist($sAdmin);

        // Enregistrement des modifications
        $manager->flush();
    }
}
