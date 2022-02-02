<?php

namespace App\DataFixtures;

use App\Entity\Race;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class RaceFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $race = new Race();
        $race->setNom('Abyssin');
        $manager->persist($race);
        $race = new Race();
        $race->setNom('Bengal');
        $manager->persist($race);
        $race = new Race();
        $race->setNom('Chartreux');
        $manager->persist($race);
        $race = new Race();
        $race->setNom('Européen');
        $manager->persist($race);
        $race = new Race();
        $race->setNom('Maine Coon');
        $manager->persist($race);
        $race = new Race();
        $race->setNom('Persan');
        $manager->persist($race);
        $race = new Race();
        $race->setNom('Sacré de Birmanie');
        $manager->persist($race);
        $race = new Race();
        $race->setNom('Siamois');
        $manager->persist($race);
        
        $manager->flush();
    }
}
