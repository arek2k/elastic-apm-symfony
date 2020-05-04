<?php

namespace Arek2k\ElasticApmSymfony\EventListener;

use Arek2k\ElasticApmSymfony\Security\TokenStorageTrait;
use Arek2k\ElasticApmSymfony\Helper\RequestProcessor;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use PhilKra\Exception\Transaction\UnknownTransactionException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use PhilKra\Agent;

class TransactionTerminateListener implements LoggerAwareInterface
{
    use LoggerAwareTrait, TokenStorageTrait;

    private $apm;

    public function __construct(Agent $apm)
    {
        $this->apm = $apm;
    }

    public function __invoke(TerminateEvent $event)
    {
        if (!$event->isMasterRequest() || !$this->apm->getConfig()->get('active')) {
            return;
        }

        $request = $event->getRequest();
        $response = $event->getResponse();

        try {
            $transaction = $this->apm->getTransaction(
                $name = RequestProcessor::getTransactionName($request)
            );
        } catch (UnknownTransactionException $e) {
            return;
        }

        $transaction->setResponse([
            'finished' => true,
            'headers_sent' => true,
            'status_code' => $response->getStatusCode(),
            'headers' => $this->formatHeaders($response->headers->all()),
        ]);

        $transaction->setMeta([
            'result' => $response->getStatusCode(),
            'type' => 'HTTP'
        ]);

        $userContext = $this->getUserContext();
        $transaction->setUserContext($userContext);

        $transaction->stop();

        if (null !== $this->logger) {
            $this->logger->info(sprintf('Transaction stopped for "%s"', $name));
        }

        try {
            $sent = $this->apm->send();
        } catch (\Exception $e) {
            $sent = false;
        }

        if (null !== $this->logger) {
            $this->logger->info(sprintf('Transaction %s for "%s"', $sent ? 'sent' : 'not sent', $name));
        }

    }

    /**
     * @param array $headers
     *
     * @return array
     */
    private function formatHeaders(array $headers): array
    {
        return collect($headers)->map(function ($values, $header) {
            return reset($values);
        })->toArray();
    }

    /**
     * @return array
     */
    private function getUserContext(): array
    {
        $userContext = [];
        /** @var User $user */
        if ($user = $this->getUser()) {
            $userContext['username'] = $user->getUsername();

            if (method_exists($user, 'getId')) {
                $userContext['id'] = $user->getId();
            }

            if (method_exists($user, 'getEmail')) {
                $userContext['email'] = $user->getEmail();
            }

            if (method_exists($user, 'getRoles')) {
                $userContext['roles'] = $user->getRoles();
            }
        }

        return $userContext;
    }
}
