<?php

namespace AppBundle\Controller\Place;

use AppBundle\Entity\Place;
use AppBundle\Entity\Theme;
use AppBundle\Form\Type\ThemeType;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ThemeController extends Controller
{
    /**
     * @Rest\Get("/places/{id}/themes")
     * @Rest\View(serializerGroups={"theme"})
     */
    public function getThemesAction($id)
    {
        $place = $this->getDoctrine()->getRepository('AppBundle:Place')->find($id);
        /* @var $place Place */

        if (!$place) {
            return $this->placeNotFound();
        }

        return $place->getThemes();
    }

    /**
     * @Rest\Post("/places/{id}/themes")
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"theme"})
     */
    public function postThemesAction(Request $request)
    {
        $place = $this->getDoctrine()->getRepository('AppBundle:Place')->find($request->get('id'));
        /* @var $place Place */

        if (!$place) {
            return $this->placeNotFound();
        }

        $theme = new Theme();
        $theme->setPlace($place);
        $form = $this->createForm(ThemeType::class, $theme);

        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($theme);
            $em->flush();
            return $theme;
        } else {
            return $form;
        }
    }

    private function placeNotFound()
    {
        return View::create(['message' => 'Place not found'], Response::HTTP_NOT_FOUND);
    }
}