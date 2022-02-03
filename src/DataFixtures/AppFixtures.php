<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Figure;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{
    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {

        $category = new Category();
        $category->setTitle("Backflip");
        $manager->persist($category);

        $category = new Category();
        $category->setTitle("Frontflip");
        $manager->persist($category);

         $figure = new Figure();
         $figure->setName('Le straight air');
         $figure->setDescription('Tout comme le 50-50 sur une box ou un rail, la réussite d’un saut dépend du respect de certains principes : bien vous positionner dans l’axe pour le décollage, maintenir votre base bien plate à l’approche du tremplin, et réaliser un ollie au moment où votre planche quitte le bord. Maintenir votre base à plat, vos genoux fléchis et votre torse bien droit constituent des éléments clés pour conserver son équilibre lors d’un saut.');
         $manager->persist($figure);

         $user = new User();
         $user->setUsername('ADMIN');
         $user->setEmail('admin@example.com');
         $user->setPassword('azerty');
            $hash = $this->encoder->hashPassword($user, $user->getPassword());
            $user->setPassword($hash);
         $user->setActive(true);
        $manager->persist($user);

        $manager->flush();
    }
}
