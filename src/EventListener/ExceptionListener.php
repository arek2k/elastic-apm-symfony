<?php

namespace Arek2k\ElasticApmSymfony\EventListener;

use Arek2k\ElasticApmSymfony\Helper\StringHelper;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use PhilKra\Agent;

class ExceptionListener implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private $apm;

    public function __construct(Agent $apm)
    {
        $this->apm = $apm;
    }

    public function __invoke(ExceptionEvent $event)
    {
        $config = $this->apm->getConfig();

        $errors = $config->get('errors');

        if (!$config->get('active') || !$errors['enabled']) {
            return;
        }

        $exception = $event->getThrowable();

        if ($errors) {
            if ($excludedStatusCodes = $errors['exclude']['status_codes'] ?? []) {
                if (!$exception instanceof HttpExceptionInterface) {
                    return;
                }

                foreach ($excludedStatusCodes as $excludedStatusCode) {
                    if (StringHelper::match($excludedStatusCode, $exception->getStatusCode())) {
                        return;
                    }
                }
            }

            if ($excludedExceptions = $errors['exclude']['exceptions'] ?? []) {
                foreach ($excludedExceptions as $excludedException) {
                    if ($exception instanceof $excludedException) {
                        return;
                    }
                }
            }
        }

        $this->apm->captureThrowable($exception);

        if (null !== $this->logger) {
            $this->logger->info(sprintf('Errors captured for "%s"', $exception->getTraceAsString()));
        }

        try {
            $sent = $this->apm->send();
        } catch (\Exception $e) {
            $sent = false;
        }

        if (null !== $this->logger) {
            $this->logger->info(
                sprintf('Errors %s for "%s"', $sent ? 'sent' : 'not sent', $exception->getTraceAsString())
            );
        }
    }

}
