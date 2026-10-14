<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\IbexaImportExport\Salesforce;

use RuntimeException;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SalesforceApiClient
{
    public function __construct(
        protected HttpClientInterface $httpClient,
        protected TagAwareAdapterInterface $cache,
    ) {
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws SalesforceApiException
     * @throws \JsonException
     *
     * @return array<mixed, mixed>
     */
    public function __invoke(
        string $domain,
        string $version,
        string $path,
        string $method,
        SalesforceApiCredentials $credentials,
    ): array {
        $url = sprintf(
            'https://%s/services/data/%s%s',
            $domain,
            $version,
            $path
        );

        $token = $this->getBearerToken($domain, $credentials);

        return $this->request(
            $method,
            $url,
            [
                'headers' => [
                    'Authorization' => sprintf('Bearer %s', $token),
                    'Content-Type' => 'application/json',
                ],
            ]
        );
    }

    protected function getBearerToken(
        string $domain,
        SalesforceApiCredentials $credentials,
    ): string {
        $cacheKey = sprintf(
            'salesforce_api_token_%s_%s',
            $domain,
            $credentials
        );
        $cachedToken = $this->cache->getItem($cacheKey);
        if ($cachedToken->isHit()) {
            return $cachedToken->get();
        }

        $authUrl = sprintf(
            'https://%s/services/oauth2/token',
            $domain
        );

        $response = $this->request(
            'POST',
            $authUrl,
            [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'body' => [
                    'grant_type' => 'password',
                    'client_id' => $credentials->clientId,
                    'client_secret' => $credentials->clientSecret,
                    'username' => $credentials->username,
                    'password' => $credentials->password,
                ],
            ]
        );
        $tokenType = $response['token_type'] ?? null;
        if ('Bearer' !== $tokenType) {
            throw new RuntimeException('Not Bearer');
        }

        $token = $response['access_token'] ?? null;
        if (!$token) {
            throw new RuntimeException('Access token not found');
        }

        $cachedToken->set($token);
        $cachedToken->expiresAfter($response['expires_in'] ?? 3600);
        $cachedToken->tag(['salesforce_api_token']);
        $this->cache->save($cachedToken);

        return $token;
    }

    /**
     * @param array<string, mixed> $options
     *
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws SalesforceApiException
     * @throws \JsonException
     *
     * @return array<mixed, mixed>
     */
    protected function request(string $method, string $url, array $options = []): array
    {
        $response = $this->httpClient->request($method, $url, $options);
        if (200 !== $response->getStatusCode()) {
            $errors = json_decode($response->getContent(false), true, 512, JSON_THROW_ON_ERROR);
            $error = reset($errors);
            throw new SalesforceApiException(
                $error['errorCode'],
                $error['extendedErrorDetails'] ?? [],
                $error['message'],
                $response->getStatusCode()
            );
        }
        $jsonContent = $response->getContent();

        return json_decode($jsonContent, true, 512, JSON_THROW_ON_ERROR);
    }
}
