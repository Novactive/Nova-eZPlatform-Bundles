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

namespace Novactive\Bundle\eZSlackBundle\Listener;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Novactive\Bundle\eZSlackBundle\Core\Slack\Interaction\Generator;
use Novactive\Bundle\eZSlackBundle\Repository\User as UserRepository;
use RuntimeException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class Request
{
    private ConfigResolverInterface $configResolver;

    private UserRepository $userRepository;

    private Repository $repository;

    private Generator $generator;

    public function __construct(
        ConfigResolverInterface $configResolver,
        UserRepository $userRepository,
        Repository $repository,
        Generator $generator
    ) {
        $this->configResolver = $configResolver;
        $this->userRepository = $userRepository;
        $this->repository = $repository;
        $this->generator = $generator;
    }

    private function sudoUser(string $slackId, string $slackTeamId): void
    {
        $user = $this->userRepository->findBySlackIds($slackId, $slackTeamId);
        if (null === $user) {
            throw new RuntimeException('You need to Slack Connect First before to use interactions.');
        }
        $apiUser = $this->repository->getUserService()->loadUser($user->id);
        $this->repository->getPermissionResolver()->setCurrentUserReference($apiUser);
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            // don't do anything if it's not the master request
            return;
        }

        $route = $event->getRequest()->get('_route');
        if (!\in_array($route, ['novactive_ezslack_callback_command', 'novactive_ezslack_callback_notification'])) {
            // don't do anything if it's not a compliant route
            return;
        }

        $response = [
            'response_type' => 'ephemeral',
            'replace_original' => 'false',
            'text' => "Sorry, that didn't work. Please try again.",
        ];

        try {
            $validToken = $this->configResolver->getParameter('slack_verification_token', 'nova_ezslack');
            if ('novactive_ezslack_callback_notification' === $route) {
                $payload = json_decode($event->getRequest()->get('payload'), true, 512, JSON_THROW_ON_ERROR);
                if ($validToken === $payload['token']) {
                    $this->sudoUser($payload['user']['id'], $payload['team']['id']);

                    // we are good, return and proceed to controller
                    return;
                }
            }
            if ('novactive_ezslack_callback_command' === $route) {
                // token is in POST
                // @todo: this should be checked and modified according to the new api specification if needed
                $token = $event->getRequest()->request->get('token');
                if ($validToken === $token) {
                    $this->sudoUser(
                        $event->getRequest()->request->get('user_id'),
                        $event->getRequest()->request->get('team_id')
                    );

                    // we are good, return and proceed to controller
                    return;
                }
            }
        } catch (\Exception $e) {
            $response['text'] = $e->getMessage();
        }

        // @todo: this should be checked and modified according to the new api specification if needed
        if (isset($payload)) {
            $blocks = $payload['message']['blocks'];
            $this->generator->insertTextSection($blocks, $response['text'], $payload['actions'][0]['block_id']);
            $response['blocks'] = $blocks;
            $response['replace_original'] = 'true';
            HttpClient::create()->request('POST', $payload['response_url'], ['json' => $response]);
        }
        $event->setResponse(new JsonResponse($response));
    }
}
