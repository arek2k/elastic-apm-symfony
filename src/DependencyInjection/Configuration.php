<?php
namespace Arek2k\ElasticApmSymfony\DependencyInjection;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('elastic_apm_symfony');

        $rootNode
            ->children()
                ->booleanNode('active')
                    ->info('Sets whether the apm reporting should be active or not')
                    ->defaultFalse()
                ->end()
                ->arrayNode('app')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('appName')
                            ->info('The app name that will identify your app in Kibana / Elastic APM')
                            ->defaultValue('Symfony')
                        ->end()
                        ->scalarNode('appVersion')
                            ->info('The version of your app')
                            ->defaultValue('')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('env')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('env')
                            ->info('whitelist environment variables OR send everything')
                            ->scalarPrototype()->end()
                        ->end()
                        ->scalarNode('enviroment')
                            ->info('Application environment')
                            ->defaultValue('dev')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('httpClient')
                    ->scalarPrototype()->end()
                    ->info('GuzzleHttp\Client options (http://docs.guzzlephp.org/en/stable/request-options.html#request-options)')
                ->end()
                ->arrayNode('server')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('serverUrl')
                            ->info('The apm-server to connect to')
                            ->defaultValue('http://127.0.0.1:8200')
                        ->end()
                        ->scalarNode('secretToken')
                            ->info('Token for x')
                            ->defaultValue('')
                        ->end()
                        ->scalarNode('apmVersion')
                            ->info('API version of the apm agent you connect to')
                            ->defaultValue('v2')
                        ->end()
                        ->scalarNode('hostname')
                            ->info('Hostname of the system the agent is running on')
                            ->defaultValue(gethostname())
                        ->end()
                    ->end()
                ->end()
//                ->arrayNode('spans')
//                    ->addDefaultsIfNotSet()
//                    ->children()
//                        ->integerNode('backtraceDepth')
//                            ->info('Depth of backtraces')
//                            ->defaultValue(25)
//                        ->end()
//                        ->booleanNode('renderSource')
//                            ->info('Add source code to span')
//                            ->defaultValue(true)
//                        ->end()
//                        ->arrayNode('querylog')
//                            ->children()
//                                ->booleanNode('enabled')
//                                    ->info('Set to false to completely disable query logging, or to \'auto\' if you would like to use the threshold feature.')
//                                    ->defaultValue(true)
//                                ->end()
//                                ->integerNode('threshold')
//                                    ->info('If a query takes longer then 200ms, we enable the query log. Make sure you set enabled = \'auto\'')
//                                    ->defaultValue(200)
//                                ->end()
//                            ->end()
//                        ->end()
//                    ->end()
//                ->end()

                ->arrayNode('errors')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                    ->end()
                    ->children()
                        ->arrayNode('exclude')
                            ->children()
                                ->arrayNode('status_codes')
                                    ->scalarPrototype()->end()
                                ->end()
                            ->end()
                            ->children()
                                ->arrayNode('exceptions')
                                    ->scalarPrototype()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('transactions')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')
                            ->defaultTrue()
                        ->end()
                    ->end()
                ->end()
        ;

        return $treeBuilder;
    }
}
