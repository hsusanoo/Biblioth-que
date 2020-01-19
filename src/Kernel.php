<?php

namespace App;

use Exception;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Exception\LoaderLoadException;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    private const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    public function registerBundles(): iterable
    {
        $contents = require $this->getProjectDir() . '/config/bundles.php';
        foreach ($contents as $class => $envs) {
            if ($envs[$this->environment] ?? $envs['all'] ?? false) {
                yield new $class();
            }
        }
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->addResource(new FileResource($this->getProjectDir() . '/config/bundles.php'));
        $container->setParameter('container.dumper.inline_class_loader', true);
        $confDir = $this->getProjectDir() . '/config';

        try {
            $loader->load($confDir . '/{packages}/*' . self::CONFIG_EXTS, 'glob');
        } catch (Exception $e) {
            $e->getTrace();
        }

        try {
            $loader->load($confDir . '/{packages}/' . $this->environment . '/**/*' . self::CONFIG_EXTS, 'glob');
        } catch (Exception $e) {
            $e->getTrace();
        }

        try {
            $loader->load($confDir . '/{services}' . self::CONFIG_EXTS, 'glob');
        } catch (Exception $e) {
            $e->getTrace();
        }

        try {
            $loader->load($confDir . '/{services}_' . $this->environment . self::CONFIG_EXTS, 'glob');
        } catch (Exception $e) {
            $e->getTrace();
        }
    }

    protected function configureRoutes(RouteCollectionBuilder $routes): void
    {
        $confDir = $this->getProjectDir() . '/config';

        try {
            $routes->import($confDir . '/{routes}/' . $this->environment . '/**/*' . self::CONFIG_EXTS, '/', 'glob');
        } catch (LoaderLoadException $e) {
            $e->getTrace();
        }

        try {
            $routes->import($confDir . '/{routes}/*' . self::CONFIG_EXTS, '/', 'glob');
        } catch (LoaderLoadException $e) {
            $e->getTrace();
        }

        try {
            $routes->import($confDir . '/{routes}' . self::CONFIG_EXTS, '/', 'glob');
        } catch (LoaderLoadException $e) {
            $e->getTrace();
        }
    }
}
