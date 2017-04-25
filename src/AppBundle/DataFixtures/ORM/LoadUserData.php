<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Preference;
use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadUserData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $users = [[
            'firstname' => 'Ab',
            'lastname' => 'Cde',
            'email' => 'ab.cde@test.local',
            'preferences' => [[
                'name' => 'history',
                'value' => 4,
            ], [
                'name' => 'art',
                'value' => 4,
            ], [
                'name' => 'sport',
                'value' => 3,
            ]],
        ], [
            'firstname' => 'Ef',
            'lastname' => 'Ghi',
            'email' => 'ef.ghi@test.local',
            'preferences' => [],
        ]];

        foreach ($users as $u) {
            $user = new User();
            $user
                ->setFirstname($u['firstname'])
                ->setLastname($u['lastname'])
                ->setEmail($u['email'])
            ;
            $manager->persist($user);

            foreach ($u['preferences'] as $p) {
                $preference = new Preference();
                $preference
                    ->setUser($user)
                    ->setName($p['name'])
                    ->setValue($p['value'])
                ;
                $manager->persist($preference);
            }
        }

        $manager->flush();
    }
}