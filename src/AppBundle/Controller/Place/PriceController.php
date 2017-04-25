<?php

namespace AppBundle\Controller\Place;

use AppBundle\Entity\Place;
use AppBundle\Entity\Price;
use AppBundle\Form\Type\PriceType;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PriceController extends Controller
{
    /**
     * @Rest\Get("/places/{id}/prices")
     * @Rest\View(serializerGroups={"price"})
     */
    public function getPricesAction($id)
    {
        $place = $this->getDoctrine()->getRepository('AppBundle:Place')->find($id);
        /* @var $place Place */

        if (!$place) {
            throw new NotFoundHttpException('Place not found');
        }

        return $place->getPrices();
    }

    /**
     * @Rest\Post("/places/{id}/prices")
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"price"})
     */
    public function postPricesAction(Request $request)
    {
        $place = $this->getDoctrine()->getRepository('AppBundle:Place')->find($request->get('id'));
        /* @var $place Place */

        if (!$place) {
            throw new NotFoundHttpException('Place not found');
        }

        $price = new Price();
        $price->setPlace($place);
        $form = $this->createForm(PriceType::class, $price);

        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($price);
            $em->flush();
            return $price;
        } else {
            return $form;
        }
    }
}