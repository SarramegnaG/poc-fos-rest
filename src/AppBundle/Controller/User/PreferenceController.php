<?php

namespace AppBundle\Controller\User;

use AppBundle\Form\Type\PreferenceType;
use AppBundle\Entity\Preference;
use AppBundle\Entity\User;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PreferenceController extends Controller
{
    /**
     * @Rest\Get("/users/{id}/preferences")
     * @Rest\View(serializerGroups={"preference"})
     */
    public function getPreferencesAction($id)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        /* @var $user User */

        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        return $user->getPreferences();
    }

    /**
     * @Rest\Post("/users/{id}/preferences")
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"preference"})
     */
    public function postPreferencesAction(Request $request)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($request->get('id'));
        /* @var $user User */

        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        $preference = new Preference();
        $preference->setUser($user);
        $form = $this->createForm(PreferenceType::class, $preference);

        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($preference);
            $em->flush();
            return $preference;
        } else {
            return $form;
        }
    }
}