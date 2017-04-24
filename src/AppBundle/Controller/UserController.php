<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * @Rest\Get("/users")
     * @Rest\View()
     */
    public function getUsersAction()
    {
        $users = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:User')
            ->findAll();
        /* @var $users User[] */

        $view = View::create($users);
        $view->setFormat('json');

        return $view;
    }

    /**
     * @Rest\Get("/users/{id}")
     * @Rest\View()
     */
    public function getUserAction($id)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        /* @var $user User */

        if (empty($user)) {
            $view = View::create(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        } else {
            $view = View::create($user);
        }

        $view->setFormat('json');

        return $view;
    }
}