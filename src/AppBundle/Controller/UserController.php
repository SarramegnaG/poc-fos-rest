<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\Type\UserType;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * @Rest\Get("/users")
     * @Rest\View(serializerGroups={"user"})
     */
    public function getUsersAction()
    {
        $users = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:User')
            ->findAll();
        /* @var $users User[] */

        return $users;
    }

    /**
     * @Rest\Get("/users/{id}")
     * @Rest\View(serializerGroups={"user"})
     */
    public function getUserAction($id)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        /* @var $user User */

        if (!$user) {
            $this->userNotFound();
        }

        return $user;
    }

    /**
     * @Rest\Post("/users")
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"user"})
     */
    public function postUsersAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $user;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\Delete("/users/{id}")
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     */
    public function removeUserAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->find($id);
        /* @var $user User */

        if ($user) {
            $em->remove($user);
            $em->flush();
        }
    }

    /**
     * @Rest\Put("/users/{id}")
     * @Rest\View(serializerGroups={"user"})
     */
    public function putUserAction(Request $request)
    {
        return $this->updateUser($request, true);
    }

    /**
     * @Rest\Patch("/users/{id}")
     * @Rest\View(serializerGroups={"user"})
     */
    public function patchUserAction(Request $request)
    {
        return $this->updateUser($request, false);
    }

    private function updateUser(Request $request, $clearMissing)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($request->get('id'));
        /* @var $user User */

        if (!$user) {
            $this->userNotFound();
        }

        $form = $this->createForm(UserType::class, $user);

        // Le paramètre false dit à Symfony de garder les valeurs dans notre
        // entité si l'utilisateur n'en fournit pas une dans sa requête
        $form->submit($request->request->all(), $clearMissing);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $user;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\Get("/users/{id}/suggestions")
     * @Rest\View(serializerGroups={"place"})
     */
    public function getUserSuggestionsAction($id)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        /* @var $user User */

        if (!$user) {
            return $this->userNotFound();
        }

        $suggestions = [];

        $places = $this->getDoctrine()->getRepository('AppBundle:Place')->findAll();

        foreach ($places as $place) {
            if ($user->preferencesMatch($place->getThemes())) {
                $suggestions[] = $place;
            }
        }

        return $suggestions;
    }

    private function userNotFound()
    {
        return View::create(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
    }
}