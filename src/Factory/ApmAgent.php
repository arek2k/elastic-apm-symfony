<?php

namespace Arek2k\ElasticApmSymfony\Factory;

use PhilKra\Agent;

class ApmAgent
{
    public static function createAgent(array $config = [])
    {
        return new Agent(
            array_merge(
                [
                    'framework' => 'Symfony',
                    'frameworkVersion' => \Symfony\Component\HttpKernel\Kernel::VERSION,
                ],
                [
                    'active' => $config['active'],
                    'httpClient' => $config['httpClient'],
                ],
                $config['app'],
                $config['env'],
                $config['server'],
                [
                    'errors' => $config['errors'],
                    'transactions' => $config['transactions']
                ]
            )
        );
    }
}
