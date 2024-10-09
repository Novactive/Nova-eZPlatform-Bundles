<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\CaptchEtat\Api;

use AlmaviaCX\Bundle\CaptchEtat\Exceptions\MissingConfigurationException;
use AlmaviaCX\Bundle\CaptchEtat\Logger\CaptchEtatLogger;
use Exception;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\Exception\ServerException;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OauthGateway
{
    protected HttpClientInterface $client;
    protected string $clientId;
    protected string $clientSecret;
    protected string $url;
    protected float $timeout;
    protected CaptchEtatLogger $logger;

    public function __construct(
        HttpClientInterface $client,
        CaptchEtatLogger $logger,
        string $clientId,
        string $clientSecret,
        string $url,
        float $timeout
    ) {
        $this->logger = $logger;
        $this->timeout = $timeout;
        $this->url = $url;
        $this->clientSecret = $clientSecret;
        $this->clientId = $clientId;
        $this->client = $client;
    }

    /**
     * Récupération du jeton Oauth 2.0.
     */
    public function getOauth20Token(): string
    {
        if (!$this->clientId || !$this->clientSecret) {
            $this->logger->error('MissingConfigurationException');
            throw new MissingConfigurationException();
        }

        $service = '/api/oauth/token';
        $body = [
            'grant_type' => 'client_credentials',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'scope' => ['resource.READ', 'piste.captchetat'],
        ];

        $url = $this->url.$service;

        $option = [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => $body,
            'timeout' => $this->timeout,
            'verify_host' => false,
            'verify_peer' => false,
        ];

        $method = 'POST';

        $requestLog = [
            'method' => $method,
            'url' => $url,
            'option' => $option,
        ];

        $requestLog['option']['body']['client_secret'] = '';

        $this->logger->notice('CAPTCHEtat.request', $requestLog);

        try {
            $response = $this->client->request($method, $url, $option);
            if (200 !== $response->getStatusCode()) {
                throw new Exception($response->getContent(false));
            }
            $jsonContent = $response->getContent();
            $content = json_decode($jsonContent, true, 512, JSON_THROW_ON_ERROR);
            $tokenType = $content['token_type'] ?? null;
            if ('Bearer' !== $tokenType) {
                throw new Exception('Not Bearer');
            }
            $accessToken = $content['access_token'] ?? null;
            if (!$accessToken) {
                throw new Exception('Not access_token');
            }

            return $accessToken;
        } catch (ClientException|ServerException $httpException) {
            $this->logger->logHttpException($httpException, $requestLog);
            throw $httpException;
        } catch (TransportException $transportException) {
            $this->logger->logTransportException($transportException, $requestLog);
            throw $transportException;
        }
    }
}
