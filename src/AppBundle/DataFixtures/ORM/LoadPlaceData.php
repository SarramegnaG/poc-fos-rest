<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Place;
use AppBundle\Entity\Price;
use AppBundle\Entity\Theme;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadPlaceData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $places = [[
            'name' => 'Tour Eiffel',
            'address' => '5 Avenue Anatole France, 75007 Paris',
            'prices' => [[
                'type' => 'less_than_12',
                'value' => 5.75,
            ]],
            'themes' => [[
                'name' => 'architecture',
                'value' => 7,
            ], [
                'name' => 'history',
                'value' => 6,
            ]],
        ], [
            'name' => 'Mont-Saint-Michel',
            'address' => '50170 Le Mont-Saint-Michel',
            'prices' => [],
            'themes' => [[
                'name' => 'history',
                'value' => 3,
            ], [
                'name' => 'art',
                'value' => 7,
            ]],
        ], [
            'name' => 'Château de Versailles',
            'address' => 'Place d\'Armes, 78000 Versailles',
            'prices' => [],
            'themes' => [],
        ]];

        foreach ($places as $p) {
            $place = new Place();
            $place
                ->setName($p['name'])
                ->setAddress($p['address'])
            ;
            $manager->persist($place);

            foreach ($p['prices'] as $pr) {
                $price = new Price();
                $price
                    ->setPlace($place)
                    ->setType($pr['type'])
                    ->setValue($pr['value'])
                ;
                $manager->persist($price);
            }

            foreach ($p['themes'] as $t) {
                $theme = new Theme();
                $theme
                    ->setPlace($place)
                    ->setName($t['name'])
                    ->setValue($t['value'])
                ;
                $manager->persist($theme);
            }
        }

        $manager->flush();
    }
}