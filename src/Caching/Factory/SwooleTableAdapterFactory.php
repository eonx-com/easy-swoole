<?php
declare(strict_types=1);

namespace EonX\EasySwoole\Caching\Factory;

use EonX\EasySwoole\Caching\Adapter\SwooleTableAdapter;
use EonX\EasySwoole\Caching\Helper\CacheTableHelper;
use EonX\EasySwoole\Logging\Helper\OutputHelper;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Marshaller\DefaultMarshaller;
use Symfony\Component\Cache\Marshaller\MarshallerInterface;
use Symfony\Contracts\Cache\CacheInterface;

final class SwooleTableAdapterFactory
{
    public function __invoke(
        string $tableName,
        ?int $defaultLifetime = null,
        MarshallerInterface $marshaller = new DefaultMarshaller(),
    ): CacheInterface {
        if (CacheTableHelper::exists($tableName) === false) {
            OutputHelper::writeln(\sprintf(
                'SwooleTable "%s" does not exist, make sure you have set it in your easy_swoole config. ' .
                'The ArrayAdapter will be used instead.',
                $tableName
            ));

            return new ArrayAdapter($defaultLifetime ?? 0);
        }

        return new SwooleTableAdapter($tableName, $defaultLifetime, $marshaller);
    }
}
