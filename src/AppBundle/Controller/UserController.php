<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\Type\UserType;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController extends Controller
{
    /**
     * @ApiDoc(
     *     section="Users",
     *     description="Récupère la liste des utilisateurs de l'application",
     *     output={"class"=User::class, "collection"=true, "groups"={"user"}}
     * )
     *
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
     * @ApiDoc(
     *     section="Users",
     *     description="Récupère un utilisateur de l'application",
     *     output={"class"=User::class, "groups"={"user"}},
     *     statusCodes={
     *         200="Succès",
     *         404="Utilisateur inexistant"
     *     },
     *     responseMap={
     *         200={"class"=User::class, "groups"={"user"}},
     *         404={"class"=NotFoundHttpException::class, "fos_rest_exception"=true, "name"=""}
     *     }
     * )
     *
     * @Rest\Get("/users/{id}")
     * @Rest\View(serializerGroups={"user"})
     */
    public function getUserAction($id)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        /* @var $user User */

        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        return $user;
    }

    /**
     * @ApiDoc(
     *     section="Users",
     *     description="Crée un utilisateur dans l'application",
     *     input={"class"=UserType::class, "name"=""},
     *     statusCodes={
     *         201="Création avec succès",
     *         400="Formulaire invalide"
     *     },
     *     responseMap={
     *         201={"class"=User::class, "groups"={"user"}},
     *         400={"class"=UserType::class, "fos_rest_form_errors"=true, "name"=""}
     *     }
     * )
     *
     * @Rest\Post("/users")
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"user"})
     */
    public function postUsersAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user, ['validation_groups' => ['Default', 'New']]);

        $form->submit($request->request->all());

        if ($form->isValid()) {
            $encoder = $this->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($encoded);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $user;
        } else {
            return $form;
        }
    }

    /**
     * @ApiDoc(
     *     section="Users",
     *     description="Supprime un utilisateur de l'application",
     *     output={"class"=User::class, "groups"={"user"}},
     *     statusCodes={
     *         204="Supprimé avec succès"
     *     }
     * )
     *
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
     * @ApiDoc(
     *     section="Users",
     *     description="Modifie entièrement un utilisateur de l'application",
     *     input={"class"=UserType::class, "name"=""},
     *     output={"class"=User::class, "groups"={"user"}},
     *     statusCodes={
     *         200="Succès",
     *         400="Formulaire invalide",
     *         404="Lieu inexistant"
     *     },
     *     responseMap={
     *         200={"class"=User::class, "groups"={"user"}},
     *         400={"class"=UserType::class, "fos_rest_form_errors"=true, "name"=""},
     *         404={"class"=NotFoundHttpException::class, "fos_rest_exception"=true, "name"=""}
     *     }
     * )
     *
     * @Rest\Put("/users/{id}")
     * @Rest\View(serializerGroups={"user"})
     */
    public function putUserAction(Request $request)
    {
        return $this->updateUser($request, true);
    }

    /**
     * @ApiDoc(
     *     section="Users",
     *     description="Modifie partiellement un utilisateur de l'application",
     *     input={"class"=UserType::class, "name"=""},
     *     output={"class"=User::class, "groups"={"user"}},
     *     statusCodes={
     *         200="Succès",
     *         400="Formulaire invalide",
     *         404="Lieu inexistant"
     *     },
     *     responseMap={
     *         200={"class"=User::class, "groups"={"user"}},
     *         400={"class"=UserType::class, "fos_rest_form_errors"=true, "name"=""},
     *         404={"class"=NotFoundHttpException::class, "fos_rest_exception"=true, "name"=""}
     *     }
     * )
     *
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
            throw new NotFoundHttpException('User not found');
        }

        if ($clearMissing) {
            $options = ['validation_groups' => ['Default', 'FullUpdate']];
        } else {
            $options = [];
        }

        $form = $this->createForm(UserType::class, $user, $options);

        // Le paramètre false dit à Symfony de garder les valeurs dans notre
        // entité si l'utilisateur n'en fournit pas une dans sa requête
        $form->submit($request->request->all(), $clearMissing);

        if ($form->isValid()) {
            if ($user->getPlainPassword()) {
                $encoder = $this->get('security.password_encoder');
                $encoded = $encoder->encodePassword($user, $user->getPlainPassword());
                $user->setPassword($encoded);
            }

            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $user;
        } else {
            return $form;
        }
    }

    /**
     * @ApiDoc(
     *     section="Users",
     *     description="Récupère la liste des suggestions pour un utilisateur",
     *     output={"class"=Place::class, "collection"=true, "groups"={"place"}}
     * )
     *
     * @Rest\Get("/users/{id}/suggestions")
     * @Rest\View(serializerGroups={"place"})
     */
    public function getUserSuggestionsAction($id)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        /* @var $user User */

        if (!$user) {
            throw new NotFoundHttpException('User not found');
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
}