<?php

namespace Arek2k\ElasticApmSymfony\EventListener;

use Arek2k\ElasticApmSymfony\Helper\RequestProcessor;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use PhilKra\Agent;

class TransactionRegisterListener implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private $apm;

    public function __construct(Agent $apm)
    {
        $this->apm = $apm;
    }

    public function __invoke(RequestEvent $event)
    {
        $config = $this->apm->getConfig();

        $transactions = $config->get('transactions');

        if (!$event->isMasterRequest() || !$config->get('active') || !$transactions['enabled']) {
            return;
        }

        try {
            $this->apm->startTransaction(
                $name = RequestProcessor::getTransactionName(
                    $event->getRequest()
                )
            );
        } catch (DuplicateTransactionNameException $e) {
            return;
        }

        if (null !== $this->logger) {
            $this->logger->info(sprintf('Transaction started for "%s"', $name));
        }

    }
}
