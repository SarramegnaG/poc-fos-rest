<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Place;
use AppBundle\Form\Type\PlaceType;
use Doctrine\ORM\QueryBuilder;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PlaceController extends Controller
{
    /**
     * @ApiDoc(
     *     section="Places",
     *     description="Récupère la liste des lieux de l'application",
     *     output={"class"=Place::class, "collection"=true, "groups"={"place"}}
     * )
     *
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
     * @ApiDoc(
     *     section="Places",
     *     description="Récupère un lieu de l'application",
     *     output={"class"=Place::class, "groups"={"place"}},
     *     statusCodes={
     *         200="Succès",
     *         404="Lieu inexistant"
     *     },
     *     responseMap={
     *         200={"class"=Place::class, "groups"={"place"}},
     *         404={"class"=NotFoundHttpException::class, "fos_rest_exception"=true, "name"=""}
     *     }
     * )
     *
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
     * @ApiDoc(
     *     section="Places",
     *     description="Crée un lieu dans l'application",
     *     input={"class"=PlaceType::class, "name"=""},
     *     statusCodes={
     *         201="Création avec succès",
     *         400="Formulaire invalide"
     *     },
     *     responseMap={
     *         201={"class"=Place::class, "groups"={"place"}},
     *         400={"class"=PlaceType::class, "fos_rest_form_errors"=true, "name"=""}
     *     }
     * )
     *
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
     * @ApiDoc(
     *     section="Places",
     *     description="Supprime un lieu de l'application",
     *     output={"class"=Place::class, "groups"={"place"}},
     *     statusCodes={
     *         204="Supprimé avec succès"
     *     }
     * )
     *
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
     * @ApiDoc(
     *     section="Places",
     *     description="Modifie entièrement un lieu de l'application",
     *     input={"class"=PlaceType::class, "name"=""},
     *     output={"class"=Place::class, "groups"={"place"}},
     *     statusCodes={
     *         200="Succès",
     *         400="Formulaire invalide",
     *         404="Lieu inexistant"
     *     },
     *     responseMap={
     *         200={"class"=Place::class, "groups"={"place"}},
     *         400={"class"=PlaceType::class, "fos_rest_form_errors"=true, "name"=""},
     *         404={"class"=NotFoundHttpException::class, "fos_rest_exception"=true, "name"=""}
     *     }
     * )
     *
     * @Rest\Put("/places/{id}")
     * @Rest\View(serializerGroups={"place"})
     */
    public function putPlaceAction(Request $request)
    {
        return $this->updatePlace($request, true);
    }

    /**
     * @ApiDoc(
     *     section="Places",
     *     description="Modifie partiellement un lieu de l'application",
     *     input={"class"=PlaceType::class, "name"=""},
     *     output={"class"=Place::class, "groups"={"place"}},
     *     statusCodes={
     *         200="Succès",
     *         400="Formulaire invalide",
     *         404="Lieu inexistant"
     *     },
     *     responseMap={
     *         200={"class"=Place::class, "groups"={"place"}},
     *         400={"class"=PlaceType::class, "fos_rest_form_errors"=true, "name"=""},
     *         404={"class"=NotFoundHttpException::class, "fos_rest_exception"=true, "name"=""}
     *     }
     * )
     *
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