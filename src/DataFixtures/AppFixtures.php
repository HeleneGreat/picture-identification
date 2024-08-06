<?php

namespace App\DataFixtures;

use App\Factory\PersonFactory;
use App\Factory\PictureFactory;
use App\Factory\TagFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // PersonFactory::createMany(25);
        // PictureFactory::createMany(10);
        TagFactory::createMany(30);

        $manager->flush();
    }
}

// php bin/console doctrine:fixtures:load --append
