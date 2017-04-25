<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\AuthToken;
use AppBundle\Entity\Preference;
use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData implements FixtureInterface, ContainerAwareInterface
{
    /** @var ContainerInterface */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $users = [[
            'firstname' => 'Ab',
            'lastname' => 'Cde',
            'email' => 'ab.cde@test.local',
            'plainPassword' => 'test',
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
            'authToken' => 'iGpKTcvbNA2eRvOtvN0EIVWYD4a6TE+uzR56rLNO2GujFysdLbD7hNp/QiVlClXmXEA=',
        ], [
            'firstname' => 'Ef',
            'lastname' => 'Ghi',
            'email' => 'ef.ghi@test.local',
            'plainPassword' => 'test',
            'preferences' => [],
            'authToken' => '',
        ]];

        foreach ($users as $u) {
            $user = new User();
            $user
                ->setFirstname($u['firstname'])
                ->setLastname($u['lastname'])
                ->setEmail($u['email'])
                ->setPlainPassword($u['plainPassword'])
            ;

            $encoder = $this->container->get('security.password_encoder');
            $password = $encoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

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

            if ($u['authToken']) {
                $authToken = new AuthToken();
                $authToken
                    ->setUser($user)
                    ->setValue($u['authToken'])
                    ->setCreatedAt(new \DateTime())
                ;
                $manager->persist($authToken);
            }
        }

        $manager->flush();
    }
}