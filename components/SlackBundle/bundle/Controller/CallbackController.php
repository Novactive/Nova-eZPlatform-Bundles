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
use JMS\Serializer\SerializerInterface;
use Novactive\Bundle\eZSlackBundle\Core\Client\Slack;
use Novactive\Bundle\eZSlackBundle\Core\Dispatcher;
use Novactive\Bundle\eZSlackBundle\Core\Event\Shared;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Interaction\Generator;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Interaction\Provider;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Responder\FirstResponder;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * @Route("/notify")
 */
class CallbackController
{
    /**
     * @Route("", methods={"GET", "POST"}, name="novactive_ezslack_callback_notification")
     */
    public function indexAction(Request $request, Provider $provider, Generator $generator): Response
    {
        $interactiveMessage = $generator->createMessage(
            json_decode($request->request->get('payload'), true, 512, JSON_THROW_ON_ERROR)
        );

        $response = $provider->execute($interactiveMessage);

        $blocks = $interactiveMessage->getBlocks();
        $action = $interactiveMessage->getAction();

        if (isset($response['action'])) {
            $generator->replaceBlockAction($blocks, $response['action'], $action['block_id'], $action['action_id']);
        } elseif (isset($response['text'])) {
            $generator->insertTextSection($blocks, $response['text'], $action['block_id']);
        }

        HttpClient::create()->request(
            'POST',
            $interactiveMessage->getResponseURL(),
            [
                'json' => [
                    'blocks' => $blocks,
                    'replace_original' => 'true',
                ],
            ]
        );

        return new Response('OK');
    }

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
