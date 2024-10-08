<?php
declare(strict_types=1);

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;

use function Symfony\Component\String\u;

return static function (DefinitionConfigurator $definition) {
    $definition->rootNode()
        ->children()
            ->arrayNode('access_log')
                ->canBeDisabled()
                ->children()
                    ->arrayNode('do_not_log_paths')
                        ->beforeNormalization()
                            ->always(static function ($value): array {
                                if ($value === null) {
                                    $value = [];
                                }

                                return \array_map(
                                    static fn ($mapValue): string => u((string)$mapValue)
                                        ->ensureStart('/')
                                        ->toString(),
                                    \is_array($value) ? $value : [$value]
                                );
                            })
                        ->end()
                        ->scalarPrototype()->end()
                    ->end()
                    ->scalarNode('timezone')->defaultValue('UTC')->end()
                ->end()
            ->end()
            ->arrayNode('doctrine')
                ->canBeDisabled()
                ->children()
                    ->booleanNode('reset_dbal_connections')->defaultTrue()->end()
                    ->arrayNode('coroutine_pdo')
                        ->canBeEnabled()
                        ->children()
                            ->booleanNode('default_heartbeat')->defaultTrue()->end()
                            ->floatNode('default_max_idle_time')->defaultValue(60.0)->end()
                            ->integerNode('default_pool_size')->defaultValue(10)->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->arrayNode('easy_admin')
                ->canBeDisabled()
            ->end()
            ->arrayNode('easy_batch')
                ->canBeDisabled()
                ->children()
                    ->booleanNode('reset_batch_processor')->defaultTrue()->end()
                ->end()
            ->end()
            ->arrayNode('easy_bugsnag')
                ->canBeDisabled()
            ->end()
            ->arrayNode('easy_logging')
                ->canBeDisabled()
            ->end()
            ->arrayNode('request_limits')
                ->canBeDisabled()
                ->children()
                    ->integerNode('min')->defaultValue(5000)->end()
                    ->integerNode('max')->defaultValue(10000)->end()
                ->end()
            ->end()
            ->arrayNode('reset_services')
                ->canBeDisabled()
            ->end()
            ->arrayNode('static_php_files')
                ->canBeEnabled()
                ->children()
                    ->arrayNode('allowed_dirs')
                        ->defaultValue(['%kernel.project_dir%/public'])
                        ->beforeNormalization()
                            ->always(static function ($value): array {
                                if ($value === null) {
                                    $value = [];
                                }

                                // Remove trailing slashes
                                $dirs = \array_map(static function ($mapValue): string {
                                    $dir = u((string)$mapValue);

                                    while ($dir->endsWith('/')) {
                                        $dir = $dir->trimSuffix('/');
                                    }

                                    return $dir->toString();
                                }, \is_array($value) ? $value : [$value]);

                                // Filter empty strings
                                $dirs = \array_filter(
                                    $dirs,
                                    static fn (
                                        $filterValue,
                                    ): bool => \is_string($filterValue) && $filterValue !== ''
                                );

                                // Default to public dir if not set
                                return \count($dirs) > 0 ? $dirs : ['%kernel.project_dir%/public'];
                            })
                        ->end()
                        ->scalarPrototype()->end()
                    ->end()
                    ->arrayNode('allowed_filenames')
                        ->beforeNormalization()
                            ->always(static function ($value): array {
                                if ($value === null) {
                                    $value = [];
                                }

                                return \array_map(static function ($mapValue): string {
                                    $phpFile = u((string)$mapValue)->ensureStart('/');

                                    if ($phpFile->endsWith('.php') === false) {
                                        throw new InvalidArgumentException(
                                            \sprintf(
                                                'Only PHP files allowed, %s given',
                                                $phpFile->toString()
                                            )
                                        );
                                    }

                                    return $phpFile->toString();
                                }, \is_array($value) ? $value : [$value]);
                            })
                        ->end()
                        ->scalarPrototype()->end()
                    ->end()
                ->end()
            ->end()
        ->end();
};
