<?php
declare(strict_types = 1);
/**
 * /src/Kernel.php
 */

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use function dirname;
use function is_file;

/**
 * Class Kernel
 *
 * @package App
 */
class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    /**
     * {@inheritdoc}
     */
    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import('../config/{packages}/*.yaml');
        $container->import('../config/{packages}/' . $this->environment . '/*.yaml');

        if (is_file(dirname(__DIR__) . '/config/services.yaml')) {
            $container->import('../config/services.yaml');
            $container->import('../config/{services}_' . $this->environment . '.yaml');
        } elseif (is_file($path = dirname(__DIR__) . '/config/services.php')) {
            /**
             * @noinspection PhpIncludeInspection
             * @noinspection UsingInclusionReturnValueInspection
             * @psalm-suppress UnresolvableInclude
             */
            (require $path)($container->withPath($path), $this);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import('../config/{routes}/' . $this->environment . '/*.yaml');
        $routes->import('../config/{routes}/*.yaml');

        if (is_file(dirname(__DIR__) . '/config/routes.yaml')) {
            $routes->import('../config/routes.yaml');
        } elseif (is_file($path = dirname(__DIR__) . '/config/routes.php')) {
            /**
             * @noinspection PhpIncludeInspection
             * @noinspection UsingInclusionReturnValueInspection
             * @psalm-suppress UnresolvableInclude
             */
            (require $path)($routes->withPath($path), $this);
        }
    }
}
