<?php

namespace App\DataFixtures;

use App\Entity\Commune;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class CommuneFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $commune = new Commune();
        $commune->setNom('Ambillou');
        $manager->persist($commune);
        $commune = new Commune();
        $commune->setNom('Amboise');
        $manager->persist($commune);
        $commune = new Commune();
        $commune->setNom('Bléré');
        $manager->persist($commune);
        $commune = new Commune();
        $commune->setNom('Chambray-les-Tours');
        $manager->persist($commune);
        $commune = new Commune();
        $commune->setNom('Fondettes');
        $manager->persist($commune);
        $commune = new Commune();
        $commune->setNom('Hommes');
        $manager->persist($commune);
        $commune = new Commune();
        $commune->setNom('La Riche');
        $manager->persist($commune);
        $commune = new Commune();
        $commune->setNom('Luynes');
        $manager->persist($commune);

        $manager->flush();
    }
}
