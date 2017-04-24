<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Place;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
}