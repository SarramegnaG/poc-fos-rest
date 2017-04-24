<?php

namespace AppBundle\DataFixtures\ORM;

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
        ], [
            'firstname' => 'Ef',
            'lastname' => 'Ghi',
            'email' => 'ef.ghi@test.local',
        ]];

        foreach ($users as $u) {
            $user = new User();
            $user
                ->setFirstname($u['firstname'])
                ->setLastname($u['lastname'])
                ->setEmail($u['email'])
            ;
            $manager->persist($user);
        }

        $manager->flush();
    }
}