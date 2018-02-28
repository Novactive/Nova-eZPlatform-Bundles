<?php
/**
 * NovaeZMailingBundle Bundle.
 *
 * @package   Novactive\Bundle\eZMailingBundle
 *
 * @author    Novactive <s.morel@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZMailingBundle/blob/master/LICENSE MIT Licence
 */
declare(strict_types=1);

namespace Novactive\Bundle\eZMailingBundle\Controller\Admin;

use Doctrine\ORM\EntityManager;
use Novactive\Bundle\eZMailingBundle\Entity\Registration;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * Class RegistrationController.
 *
 * @Route("/registration")
 */
class RegistrationController
{
    /**
     * @Route("/accept/{registration}", name="novaezmailing_registration_accept")
     * @Method({"POST"})
     *
     * @return JsonResponse
     */
    public function acceptAction(
        Request $request,
        CsrfTokenManagerInterface $csrfTokenManager,
        EntityManager $entityManager,
        Registration $registration
    ): JsonResponse {
        $token = $request->request->get('token');
        if (!$request->isXmlHttpRequest() || null === $token ||
            !$csrfTokenManager->isTokenValid(new CsrfToken($registration->getId(), $token))) {
            throw new AccessDeniedHttpException("Not Allowed");
        }
        $registration->setApproved(true);
        $entityManager->persist($registration);
        $entityManager->flush();

        return new JsonResponse(['token' => $csrfTokenManager->getToken($registration->getId())->getValue()]);
    }

    /**
     * @Route("/deny/{registration}", name="novaezmailing_registration_deny")
     *
     * @return JsonResponse
     */
    public function debyAction(
        Request $request,
        CsrfTokenManagerInterface $csrfTokenManager,
        EntityManager $entityManager,
        Registration $registration
    ): JsonResponse {
        $token = $request->request->get('token');
        if (!$request->isXmlHttpRequest() || null === $token ||
            !$csrfTokenManager->isTokenValid(new CsrfToken($registration->getId(), $token))) {
            throw new AccessDeniedHttpException("Not Allowed");
        }

        $registration->setApproved(false);
        $entityManager->persist($registration);
        $entityManager->flush();

        return new JsonResponse(['token' => $csrfTokenManager->getToken($registration->getId())->getValue()]);

    }

}
