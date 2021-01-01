<?php

namespace PhpCaddy;

use DI\Container;
use DI\ContainerBuilder;
use Exception;
use InvalidArgumentException;

class Core
{
    /**
     * The prefix used to segregate modules from other container entries.
     *
     * @var string
     */
    private const MODULE_PREFIX = "module.";

    /**
     * Singleton instance of the DI container.
     *
     * @var Container
     */
    protected static Container $instance;

    /**
     * Return a singleton instance of the DI container.
     *
     * @return Container
     * @throws Exception
     */
    public static function container() : Container
    {
        if (isset(self::$instance)) {
            return self::$instance;
        }

        $builder = new ContainerBuilder();

        // TODO: Enable compilation in prod for performance.
        //       Note that for this to work, we need to wipe the cache
        //       folder every time the service definitions are updated
        //       on the prod environment, so some extra logic is needed
        //       to do this safely. See http://php-di.org/doc/performances.html
        // $builder->enableCompilation(__DIR__ . '/../cache');

        // Load DI configs
        $builder->addDefinitions(...array_merge(
            glob(__DIR__ . '/**/config.php'),
            glob(__DIR__ . '/**/**/config.php'),
            glob(__DIR__ . '/**/**/**/config.php'),
            glob(__DIR__ . '/**/**/**/**/config.php'),
        ));

        /** @noinspection PhpUnhandledExceptionInspection */
        $container = $builder->build();

        self::$instance = $container;

        // Register standard modules
        require_once __DIR__ . '/Modules/modules.php';

        return self::$instance;
    }

    /**
     * Registers a module to the container
     *
     * @param string $module The class name of the module
     * @throws Exception
     */
    public static function registerModule(string $module)
    {
        $container = self::container();

        /** @var Module $module */
        $id = $module::module()->id();
        if (empty($id)) {
            throw new InvalidArgumentException("Module ID is missing");
        }

        if ($container->has(self::MODULE_PREFIX . $id)) {
            throw new InvalidArgumentException("Module is already registered: $id");
        }

        $container->set(self::MODULE_PREFIX . $id, $module);
    }

    /**
     * Get a module class name from the container by its ID.
     *
     * @param string $id
     * @return string
     * @throws
     */
    public static function getModuleClass(string $id) : string
    {
        return self::container()->get(self::MODULE_PREFIX . $id);
    }

    /**
     * Get a module from the container by its ID.
     *
     * @param string $id
     * @return string
     * @throws
     */
    public static function getModule(string $id) : Module
    {
        return self::container()->make(self::getModuleClass($id));
    }

    /**
     * Get all modules under a given scope.
     *
     * For example, a scope of "foo" returns modules named "foo.bar",
     * "foo.loo", but not "bar", "foo.bar.loo", etc. An empty scope
     * returns top-level modules, for example "foo" or "bar". Partial
     * scopes are not matched (i.e. scope "foo.ba" does not match
     * name "foo.bar").
     *
     * @param string $scope
     * @return string[]
     * @throws
     */
    public static function getModules(string $scope) : array
    {
        $container = self::container();

        $scopeParts = explode(".", $scope);
        if ($scope === "") {
            $scopeParts = [];
        }

        /** @var string[] $modules */
        $modules = [];

        // Note: This might be slightly inefficient because it will look
        // at every entry in the container, including non-module entries.
        // This is a limitation of using a single container instance.
        foreach ($container->getKnownEntryNames() as $entry) {
            // If the entry is not a module, skip
            if (! str_starts_with($entry, self::MODULE_PREFIX)) {
                continue;
            }

            // Grab just the module ID from the entry
            $id = substr($entry, strlen(self::MODULE_PREFIX));
            $idParts = explode(".", $id);

            // Only match the next level of nesting
            if (count($idParts) !== count($scopeParts) + 1) {
                continue;
            }

            // The specified parts must be exact matches
            foreach ($scopeParts as $i => $part) {
                if ($idParts[$i] !== $part) {
                    continue 2;
                }
            }

            $modules[] = $container->get($entry);
        }

        return $modules;
    }

    /**
     * Get the list of all registered modules.
     *
     * @return string[]
     * @throws
     */
    public static function getAllModules() : array
    {
        $container = self::container();

        /** @var string[] $modules */
        $modules = [];

        foreach ($container->getKnownEntryNames() as $entry) {
            // If the entry is not a module, skip
            if (!str_starts_with($entry, self::MODULE_PREFIX)) {
                continue;
            }

            $modules[] = $container->get($entry);
        }

        return $modules;
    }
}
