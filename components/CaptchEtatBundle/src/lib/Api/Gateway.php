<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\CaptchEtat\Api;

use AlmaviaCX\Bundle\CaptchEtat\Logger\CaptchEtatLogger;
use RuntimeException;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\Exception\ServerException;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Gateway
{
    protected HttpClientInterface $client;
    protected string $url;
    protected float $timeout;
    protected CaptchEtatLogger $logger;
    protected OauthGateway $oauthGateway;

    public function __construct(
        HttpClientInterface $client,
        CaptchEtatLogger $logger,
        OauthGateway $oauthGateway,
        string $url,
        float $timeout
    ) {
        $this->oauthGateway = $oauthGateway;
        $this->logger = $logger;
        $this->timeout = $timeout;
        $this->url = $url;
        $this->client = $client;
    }

    public function getSimpleCaptchaEndpoint(
        string $captchaType,
        ?string $tech = null,
        string $type = 'numerique6_7CaptchaFR'
    ): string {
        $token = $this->oauthGateway->getOauth20Token();
        $availableTypes = [
            'image',
            'sound',
        ];

        if (!in_array($captchaType, $availableTypes)) {
            throw new RuntimeException(
                sprintf(
                    'c value "%s" not alloweb. One of %s waiting',
                    $captchaType,
                    implode(', ', $availableTypes)
                )
            );
        }

        $service = '/piste/captchetat/v2/simple-captcha-endpoint';
        $method = 'GET';

        $queryParams = [
            'get' => $captchaType,
            'c' => $type,
        ];
        if ($tech) {
            $queryParams['t'] = $tech;
        }

        $url = $this->url.$service.'?'.http_build_query($queryParams);

        $option = [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
            ],
            'timeout' => $this->timeout,
            'verify_host' => false,
            'verify_peer' => false,
        ];

        $requestLog = [
            '$method' => $method,
            '$url' => $url,
            '$option' => $option,
        ];

        $this->logger->notice('CAPTCHEtat.request', $requestLog);
        try {
            $response = $this->client->request($method, $url, $option);

            return $response->getContent();
        } catch (ClientException|ServerException $httpException) {
            $this->logger->logHttpException($httpException, $requestLog);
            throw $httpException;
        } catch (TransportException $transportException) {
            $this->logger->logTransportException($transportException, $requestLog);
            throw $transportException;
        }
    }

    public function validateChallenge(
        string $captchaId,
        string $answer
    ): bool {
        $token = $this->oauthGateway->getOauth20Token();
        $service = '/piste/captchetat/v2/valider-captcha';
        $method = 'POST';

        $url = $this->url.$service;

        $option = [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
                'Content-Type' => 'application/json',
            ],
            'timeout' => $this->timeout,
            'body' => json_encode([
                                      'uuid' => $captchaId,
                                      'code' => $answer,
                                  ]),
        ];

        $requestLog = [
            '$method' => $method,
            '$url' => $url,
            '$option' => $option,
        ];

        $this->logger->notice('CAPTCHEtat.request', $requestLog);

        try {
            $response = $this->client->request($method, $url, $option);
            $content = $response->getContent();

            return 'true' === $content;
        } catch (ClientException|ServerException $httpException) {
            $this->logger->logHttpException($httpException, $requestLog);
            throw $httpException;
        } catch (TransportException $transportException) {
            $this->logger->logTransportException($transportException, $requestLog);
            throw $transportException;
        }
    }
}
