<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AuthToken;
use AppBundle\Entity\Credentials;
use AppBundle\Form\Type\CredentialsType;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AuthTokenController extends Controller
{
    /**
     * @ApiDoc(
     *     section="AuthToken",
     *     description="Crée un token d'authentification",
     *     input={"class"=CredentialsType::class, "name"=""},
     *     statusCodes = {
     *         201="Création avec succès",
     *         400="Formulaire invalide"
     *     },
     *     responseMap={
     *         201={"class"=AuthToken::class, "groups"={"auth-token"}},
     *         400={"class"=CredentialsType::class, "fos_rest_form_errors"=true, "name"=""}
     *     }
     * )
     *
     * @Rest\Post("/auth-tokens")
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"auth-token"})
     */
    public function postAuthTokensAction(Request $request)
    {
        $credentials = new Credentials();
        $form = $this->createForm(CredentialsType::class, $credentials);

        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $form;
        }

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('AppBundle:User')->findOneByEmail($credentials->getLogin());

        if (!$user) {
            throw new BadRequestHttpException('Invalid credentials');
        }

        $encoder = $this->get('security.password_encoder');
        $isPasswordValid = $encoder->isPasswordValid($user, $credentials->getPassword());

        if (!$isPasswordValid) {
            throw new BadRequestHttpException('Invalid credentials');
        }

        $authToken = new AuthToken();
        $authToken->setValue(base64_encode(random_bytes(50)));
        $authToken->setCreatedAt(new \DateTime('now'));
        $authToken->setUser($user);

        $em->persist($authToken);
        $em->flush();

        return $authToken;
    }

    /**
     * @ApiDoc(
     *     section="AuthToken",
     *     description="Supprime un token d'authentification",
     *     statusCodes = {
     *         204="Supprimé avec succès",
     *         400="Suppression non autorisée"
     *     },
     *     responseMap={
     *         400={"class"=BadRequestHttpException::class, "fos_rest_exception"=true, "name"=""}
     *     }
     * )
     *
     * @Rest\Delete("/auth-tokens/{id}")
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     */
    public function removeAuthTokenAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $authToken = $em->getRepository('AppBundle:AuthToken')->find($id);
        /* @var $authToken AuthToken */

        if ($authToken && $authToken->getUser()->getId() === $this->getUser()->getId()) {
            $em->remove($authToken);
            $em->flush();
        } else {
            throw new BadRequestHttpException();
        }
    }
}