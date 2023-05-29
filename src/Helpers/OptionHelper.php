<?php

declare(strict_types=1);

namespace EonX\EasySwoole\Helpers;

final class OptionHelper
{
    private const DEFAULT_OPTIONS = [
        'callbacks' => [],
        'host' => '0.0.0.0',
        'hot_reload_dirs' => [
            self::DEFAULT_PUBLIC_DIR . 'config',
            self::DEFAULT_PUBLIC_DIR . 'public',
            self::DEFAULT_PUBLIC_DIR . 'src',
            self::DEFAULT_PUBLIC_DIR . 'translations',
            self::DEFAULT_PUBLIC_DIR . 'vendor',
        ],
        'hot_reload_enabled' => false,
        'hot_reload_extensions' => [
            '.php',
            '.yaml',
            '.xml',
        ],
        'mode' => \SWOOLE_BASE,
        'port' => 8080,
        'response_chunk_size' => 1048576,
        'settings' => [],
        'sock_type' => \SWOOLE_SOCK_TCP,
        'use_default_callbacks' => true,
    ];

    private const DEFAULT_PUBLIC_DIR = __DIR__ . '/../../../../../';

    /**
     * @var mixed[]
     */
    private static array $options = [];

    /**
     * @return mixed[]
     */
    public static function getArray(string $option, string $env): array
    {
        $value = self::getOption($option, $env);

        if (\is_string($value)) {
            return \explode(',', $value);
        }

        return \array_filter(\is_array($value) ? $value : [$value]);
    }

    public static function getBoolean(string $option, string $env): bool
    {
        $value = self::getOption($option, $env);

        if (\is_string($value)) {
            return \in_array($value, ['false', '0'], true) === false;
        }

        return (bool)$value;
    }

    public static function getInteger(string $option, string $env): int
    {
        return (int)self::getOption($option, $env);
    }

    public static function getString(string $option, string $env): string
    {
        return (string)self::getOption($option, $env);
    }

    /**
     * @param mixed[] $options
     */
    public static function setOptions(array $options): void
    {
        self::$options = $options;
    }

    private static function getOption(string $option, string $env): mixed
    {
        return self::$options[$option] ?? $_SERVER[$env] ?? $_ENV[$env] ?? self::DEFAULT_OPTIONS[$option];
    }
}
