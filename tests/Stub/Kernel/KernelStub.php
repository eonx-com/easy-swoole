<?php
declare(strict_types=1);

namespace EonX\EasySwoole\Tests\Stub\Kernel;

use EonX\EasySwoole\Bundle\EasySwooleBundle;
use EonX\EasySwoole\Tests\Stub\Resetter\ServicesResetterStub;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Kernel;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

final class KernelStub extends Kernel implements CompilerPassInterface
{
    /**
     * @var string[]
     */
    private readonly array $configs;

    /**
     * @param string[]|null $configs
     */
    public function __construct(?array $configs = null)
    {
        $this->configs = $configs ?? [];

        parent::__construct('test', true);
    }

    public function process(ContainerBuilder $container): void
    {
        $container->setDefinition('services_resetter', new Definition(ServicesResetterStub::class));
        $container->setDefinition(Environment::class, new Definition(Environment::class, [
            '$loader' => new Definition(ArrayLoader::class),
        ]));
        $container->setDefinition(RequestStack::class, new Definition(RequestStack::class));

        foreach ($container->getAliases() as $alias) {
            $alias->setPublic(true);
        }

        foreach ($container->getDefinitions() as $definition) {
            $definition->setPublic(true);
        }
    }

    /**
     * @return iterable<\Symfony\Component\HttpKernel\Bundle\BundleInterface>
     */
    public function registerBundles(): iterable
    {
        yield new EasySwooleBundle();
    }

    /**
     * @throws \Exception
     */
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        foreach ($this->configs as $config) {
            $loader->load($config);
        }
    }
}
