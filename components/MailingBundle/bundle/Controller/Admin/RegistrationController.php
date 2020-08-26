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

use Novactive\Bundle\eZMailingBundle\Core\AjaxGuard;
use Novactive\Bundle\eZMailingBundle\Entity\Registration;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/registration")
 */
class RegistrationController
{
    /**
     * @Route("/accept/{registration}", name="novaezmailing_registration_accept", methods={"POST"})
     */
    public function acceptAction(
        Request $request,
        AjaxGuard $ajaxGuard,
        Registration $registration
    ): JsonResponse {
        $token = $ajaxGuard->execute(
            $request,
            $registration,
            function (Registration $registration) {
                $registration->setApproved(true);

                return [];
            }
        );

        return new JsonResponse(['token' => $token]);
    }

    /**
     * @Route("/deny/{registration}", name="novaezmailing_registration_deny")
     */
    public function denyAction(
        Request $request,
        AjaxGuard $ajaxGuard,
        Registration $registration
    ): JsonResponse {
        $results = $ajaxGuard->execute(
            $request,
            $registration,
            function (Registration $registration) {
                $registration->setApproved(false);

                return [];
            }
        );

        return new JsonResponse($results);
    }
}
