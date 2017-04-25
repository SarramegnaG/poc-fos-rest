<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AuthToken;
use AppBundle\Entity\Credentials;
use AppBundle\Form\Type\CredentialsType;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AuthTokenController extends Controller
{
    /**
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