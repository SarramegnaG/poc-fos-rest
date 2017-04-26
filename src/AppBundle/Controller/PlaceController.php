<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Place;
use AppBundle\Form\Type\PlaceType;
use Doctrine\ORM\QueryBuilder;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PlaceController extends Controller
{
    /**
     * @Rest\Get("/places")
     * @Rest\QueryParam(name="offset", requirements="\d+", default="", description="Index de début de la pagination")
     * @Rest\QueryParam(name="limit", requirements="\d+", default="", description="Nombre d'éléments de la pagination")
     * @Rest\QueryParam(name="sort", requirements="(asc|desc)", nullable=true, description="Ordre de tri (basé sur le nom)")
     * @Rest\View(serializerGroups={"place"})
     */
    public function getPlacesAction(ParamFetcher $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $limit = $paramFetcher->get('limit');
        $sort = $paramFetcher->get('sort');

        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        /** @var QueryBuilder $qb */
        $qb
            ->select('p')
            ->from('AppBundle:Place', 'p')
        ;

        if ($offset !== "") {
            $qb->setFirstResult($offset);
        }

        if ($limit !== "") {
            $qb->setMaxResults($limit);
        }

        if (in_array($sort, ['asc', 'desc'])) {
            $qb->orderBy('p.name', $sort);
        }

        $places = $qb->getQuery()->getResult();
        /* @var $places Place[] */

        return $places;
    }

    /**
     * @Rest\Get("/places/{id}")
     * @Rest\View(serializerGroups={"place"})
     */
    public function getPlaceAction($id)
    {
        $place = $this->getDoctrine()->getRepository('AppBundle:Place')->find($id);
        /* @var $place Place */

        if (!$place) {
            throw new NotFoundHttpException('Place not found');
        }

        return $place;
    }

    /**
     * @Rest\Post("/places")
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"place"})
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

    /**
     * @Rest\Put("/places/{id}")
     * @Rest\View(serializerGroups={"place"})
     */
    public function putPlaceAction(Request $request)
    {
        return $this->updatePlace($request, true);
    }

    /**
     * @Rest\Patch("/places/{id}")
     * @Rest\View(serializerGroups={"place"})
     */
    public function patchPlaceAction(Request $request)
    {
        return $this->updatePlace($request, false);
    }

    private function updatePlace(Request $request, $clearMissing)
    {
        $place = $this->getDoctrine()->getRepository('AppBundle:Place')->find($request->get('id'));
        /* @var $place Place */

        if (!$place) {
            throw new NotFoundHttpException('Place not found');
        }

        $form = $this->createForm(PlaceType::class, $place);

        $form->submit($request->request->all(), $clearMissing);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $place;
        } else {
            return $form;
        }
    }
}