<?php

/**
 * NovaeZSlackBundle Bundle.
 *
 * @package   Novactive\Bundle\eZSlackBundle
 *
 * @author    Novactive <s.morel@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZSlackBundle/blob/master/LICENSE MIT Licence
 */

declare(strict_types=1);

namespace Novactive\Bundle\eZSlackBundle\Controller;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\Core\MVC\Symfony\Routing\ChainRouter;
use Symfony\Component\Routing\RouterInterface;
use JMS\Serializer\SerializerInterface;
use Novactive\Bundle\eZSlackBundle\Core\Client\Slack;
use Novactive\Bundle\eZSlackBundle\Core\Dispatcher;
use Novactive\Bundle\eZSlackBundle\Core\Event\Shared;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Interaction\Provider;
use Novactive\Bundle\eZSlackBundle\Core\Slack\InteractiveMessage;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Message;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Responder\FirstResponder;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CommandController.
 *
 * @Route("/")
 */
class CallbackController
{
    /**
     * @Route("/command", methods={"POST"}, name="novactive_ezslack_callback_command")
     */
    public function commandAction(
        Request $request,
        FirstResponder $firstResponder,
        SerializerInterface $jmsSerializer
    ): JsonResponse {
        $message = $firstResponder($request->request->get('text'));

        return new JsonResponse($jmsSerializer->serialize($message, 'json'), 200, [], true);
    }

    /**
     * @Route("/message", methods={"POST"}, name="novactive_ezslack_callback_message")
     */
    public function messageAction(
        Request $request,
        SerializerInterface $jmsSerializer,
        Provider $provider
    ): JsonResponse {
        // has been decoded and checked in the RequestListener already
        /** @var InteractiveMessage $interactiveMessage */
        $interactiveMessage = $request->attributes->get('interactiveMessage');
        $attachment = $provider->execute($interactiveMessage);
        $originalMessage = $interactiveMessage->getOriginalMessage();
        if (null === $originalMessage) {
            // we are coming from an ephemeral (prob search)
            $originalMessage = new Message();
        } else {
            $originalMessage->removeAttachmentAtIndex((int) $interactiveMessage->getAttachmentIndex() - 1);
        }
        $originalMessage->addAttachment($attachment);
        $newPayload = $jmsSerializer->serialize($originalMessage, 'json');

        return new JsonResponse($newPayload, 200, [], true);
    }

    /**
     * @Route("/share/{locationId}", methods={"GET"}, name="novactive_ezslack_callback_shareonslack")
     */
    public function shareOnSlackAction(
        Request $request,
        int $locationId,
        RouterInterface $router,
        Dispatcher $dispatcher,
        Repository $repository
    ) {
        $location = $repository->getLocationService()->loadLocation($locationId);
        $contentId = (int) $location->contentInfo->id;
        $event = new Shared($contentId);
        $dispatcher->receive($event);
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse('ok');
        }

        return new RedirectResponse(
            $router->generate('_ez_content_view', ['locationId' => $locationId, 'contentId' => $location->contentId])
        );
    }

    /**
     * @Route("/kcode", methods={"GET"})
     */
    public function kcodeAction(Slack $client, Request $request): JsonResponse
    {
        //$client->sendNotification(new Message(base64_decode(base64_decode($request->query->get('m')))));

        return new JsonResponse();
    }
}
