<?php
declare( strict_types=1 );

namespace AlmaviaCX\Bundle\IbexaSaml\Security\Saml;

use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class SamlExceptionLogger
{
    /** @var LoggerInterface */
    protected LoggerInterface $logger;

    /**
     * ExceptionLogger constructor.
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Logs an exception.
     *
     * @param Exception $exception The \Exception instance
     */
    public function logException( Throwable $exception): void
    {
        if (null !== $this->logger) {
            $message = sprintf(
                'Uncaught PHP Exception %s: "%s" at %s line %s',
                get_class($exception),
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine()
            );
            if ( $exception instanceof SamlException) {
                $this->logger->warning($message, ['exception' => $exception]);
            } elseif (!$exception instanceof HttpExceptionInterface || $exception->getStatusCode() >= 500) {
                $this->logger->critical($message, ['exception' => $exception]);
            } else {
                $this->logger->error($message, ['exception' => $exception]);
            }
        }
    }

    public function logInfo(string $message): void
    {
        $this->logger->info($message);
    }
    public function logError(string $message): void
    {
        $this->logger->error($message);
    }
}
