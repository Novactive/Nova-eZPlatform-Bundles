<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\CaptchEtat\Logger;

use Exception;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class CaptchEtatLogger implements LoggerInterface
{
    protected LoggerInterface $innerLogger;

    public function __construct(
        LoggerInterface $innerLogger
    ) {
        $this->innerLogger = $innerLogger;
    }

    public function logException(Exception $exception): void
    {
        $message = sprintf(
            'Uncaught PHP Exception %s: "%s" at %s line %s',
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        );
        if ($exception instanceof NotFoundException || $exception instanceof UnauthorizedException) {
            $this->innerLogger->warning($message, ['exception' => $exception]);
        } elseif (!$exception instanceof HttpExceptionInterface || $exception->getStatusCode() >= 500) {
            $this->innerLogger->critical($message, ['exception' => $exception]);
        } else {
            $this->innerLogger->error($message, ['exception' => $exception]);
        }
    }

    public function logHttpException(RuntimeException $httpException, array $requestLog): void
    {
        $response = $httpException->getResponse();
        $content = $response->getContent(false);
        if ('application/json' === $response->getInfo('content_type')) {
            $content = json_decode($content);
        }
        $this->innerLogger->error('CAPTCHEtat.error', [
            'message' => $httpException->getMessage(),
            'request' => $requestLog,
            'statusCode' => $response->getStatusCode(),
            'content' => $content,
            'headers' => $response->getHeaders(false),
        ]);
    }

    public function logTransportException(TransportException $transportException, array $requestLog): void
    {
        $this->innerLogger->error('CAPTCHEtat.error', [
            'message' => $transportException->getMessage(),
            'request' => $requestLog,
        ]);
    }

    public function emergency($message, array $context = [])
    {
        $this->innerLogger->emergency($message, $context);
    }

    public function alert($message, array $context = [])
    {
        $this->innerLogger->alert($message, $context);
    }

    public function critical($message, array $context = [])
    {
        $this->innerLogger->critical($message, $context);
    }

    public function error($message, array $context = [])
    {
        $this->innerLogger->error($message, $context);
    }

    public function warning($message, array $context = [])
    {
        $this->innerLogger->warning($message, $context);
    }

    public function notice($message, array $context = [])
    {
        $this->innerLogger->notice($message, $context);
    }

    public function info($message, array $context = [])
    {
        $this->innerLogger->info($message, $context);
    }

    public function debug($message, array $context = [])
    {
        $this->innerLogger->debug($message, $context);
    }

    public function log($level, $message, array $context = [])
    {
        $this->innerLogger->log($message, $context);
    }
}
