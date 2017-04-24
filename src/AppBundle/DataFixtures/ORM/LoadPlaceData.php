<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Place;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadPlaceData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $places = [[
            'name' => 'Tour Eiffel',
            'address' => '5 Avenue Anatole France, 75007 Paris',
        ], [
            'name' => 'Mont-Saint-Michel',
            'address' => '50170 Le Mont-Saint-Michel',
        ], [
            'name' => 'Château de Versailles',
            'address' => 'Place d\'Armes, 78000 Versailles',
        ]];

        foreach ($places as $p) {
            $place = new Place();
            $place
                ->setName($p['name'])
                ->setAddress($p['address'])
            ;
            $manager->persist($place);
        }

        $manager->flush();
    }
}