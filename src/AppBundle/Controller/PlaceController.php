<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Place;
use AppBundle\Form\Type\PlaceType;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PlaceController extends Controller
{
    /**
     * @Rest\Get("/places")
     * @Rest\View()
     */
    public function getPlacesAction()
    {
        $places = $this->getDoctrine()->getRepository('AppBundle:Place')->findAll();
        /* @var $places Place[] */

        return $places;
    }

    /**
     * @Rest\Get("/places/{id}")
     * @Rest\View()
     */
    public function getPlaceAction($id)
    {
        $place = $this->getDoctrine()->getRepository('AppBundle:Place')->find($id);
        /* @var $place Place */

        if (empty($place)) {
            return View::create(['message' => 'Place not found'], Response::HTTP_NOT_FOUND);
        }

        return $place;
    }

    /**
     * @Rest\Post("/places")
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     */
    public function postPlacesAction(Request $request)
    {
        $place = new Place();
        $form = $this->createForm(PlaceType::class, $place);

        $form->submit($request->request->all()); // Validation des données

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($place);
            $em->flush();
            return $place;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\Delete("/places/{id}")
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     */
    public function removePlaceAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $place = $em->getRepository('AppBundle:Place')->find($id);
        /* @var $place Place */

        if ($place) {
            $em->remove($place);
            $em->flush();
        }
    }
}