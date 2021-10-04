<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Session\Storage\Handler;

use Doctrine\DBAL\DriverManager;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\Traits\RedisClusterProxy;
use Symfony\Component\Cache\Traits\RedisProxy;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class SessionHandlerFactory
{
    /**
     * @param \Redis|\RedisArray|\RedisCluster|\Predis\ClientInterface|RedisProxy|RedisClusterProxy|\Memcached|\PDO|string $connection Connection or DSN
     */
    public static function createHandler($connection): AbstractSessionHandler
    {
        if (!\is_string($connection) && !\is_object($connection)) {
            throw new \TypeError(sprintf('Argument 1 passed to "%s()" must be a string or a connection object, "%s" given.', __METHOD__, get_debug_type($connection)));
        }

        switch (true) {
            case $connection instanceof \Redis:
            case $connection instanceof \RedisArray:
            case $connection instanceof \RedisCluster:
            case $connection instanceof \Predis\ClientInterface:
            case $connection instanceof RedisProxy:
            case $connection instanceof RedisClusterProxy:
                return new RedisSessionHandler($connection);

            case $connection instanceof \Memcached:
                return new MemcachedSessionHandler($connection);

            case $connection instanceof \PDO:
                return new PdoSessionHandler($connection);

            case !\is_string($connection):
                throw new \InvalidArgumentException(sprintf('Unsupported Connection: "%s".', get_debug_type($connection)));
<<<<<<< HEAD
<<<<<<< HEAD
            case str_starts_with($connection, 'file://'):
=======
            case 0 === strpos($connection, 'file://'):
>>>>>>> 22c0e54 (table changes)
=======
            case str_starts_with($connection, 'file://'):
>>>>>>> f330c64 (optimization in progress)
                $savePath = substr($connection, 7);

                return new StrictSessionHandler(new NativeFileSessionHandler('' === $savePath ? null : $savePath));

<<<<<<< HEAD
<<<<<<< HEAD
            case str_starts_with($connection, 'redis:'):
            case str_starts_with($connection, 'rediss:'):
            case str_starts_with($connection, 'memcached:'):
                if (!class_exists(AbstractAdapter::class)) {
                    throw new \InvalidArgumentException(sprintf('Unsupported DSN "%s". Try running "composer require symfony/cache".', $connection));
                }
                $handlerClass = str_starts_with($connection, 'memcached:') ? MemcachedSessionHandler::class : RedisSessionHandler::class;
=======
            case 0 === strpos($connection, 'redis:'):
            case 0 === strpos($connection, 'rediss:'):
            case 0 === strpos($connection, 'memcached:'):
                if (!class_exists(AbstractAdapter::class)) {
                    throw new \InvalidArgumentException(sprintf('Unsupported DSN "%s". Try running "composer require symfony/cache".', $connection));
                }
                $handlerClass = 0 === strpos($connection, 'memcached:') ? MemcachedSessionHandler::class : RedisSessionHandler::class;
>>>>>>> 22c0e54 (table changes)
=======
            case str_starts_with($connection, 'redis:'):
            case str_starts_with($connection, 'rediss:'):
            case str_starts_with($connection, 'memcached:'):
                if (!class_exists(AbstractAdapter::class)) {
                    throw new \InvalidArgumentException(sprintf('Unsupported DSN "%s". Try running "composer require symfony/cache".', $connection));
                }
                $handlerClass = str_starts_with($connection, 'memcached:') ? MemcachedSessionHandler::class : RedisSessionHandler::class;
>>>>>>> f330c64 (optimization in progress)
                $connection = AbstractAdapter::createConnection($connection, ['lazy' => true]);

                return new $handlerClass($connection);

<<<<<<< HEAD
<<<<<<< HEAD
            case str_starts_with($connection, 'pdo_oci://'):
=======
            case 0 === strpos($connection, 'pdo_oci://'):
>>>>>>> 22c0e54 (table changes)
=======
            case str_starts_with($connection, 'pdo_oci://'):
>>>>>>> f330c64 (optimization in progress)
                if (!class_exists(DriverManager::class)) {
                    throw new \InvalidArgumentException(sprintf('Unsupported DSN "%s". Try running "composer require doctrine/dbal".', $connection));
                }
                $connection = DriverManager::getConnection(['url' => $connection])->getWrappedConnection();
                // no break;

<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
            case str_starts_with($connection, 'mssql://'):
            case str_starts_with($connection, 'mysql://'):
            case str_starts_with($connection, 'mysql2://'):
            case str_starts_with($connection, 'pgsql://'):
            case str_starts_with($connection, 'postgres://'):
            case str_starts_with($connection, 'postgresql://'):
            case str_starts_with($connection, 'sqlsrv://'):
            case str_starts_with($connection, 'sqlite://'):
            case str_starts_with($connection, 'sqlite3://'):
<<<<<<< HEAD
=======
            case 0 === strpos($connection, 'mssql://'):
            case 0 === strpos($connection, 'mysql://'):
            case 0 === strpos($connection, 'mysql2://'):
            case 0 === strpos($connection, 'pgsql://'):
            case 0 === strpos($connection, 'postgres://'):
            case 0 === strpos($connection, 'postgresql://'):
            case 0 === strpos($connection, 'sqlsrv://'):
            case 0 === strpos($connection, 'sqlite://'):
            case 0 === strpos($connection, 'sqlite3://'):
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
                return new PdoSessionHandler($connection);
        }

        throw new \InvalidArgumentException(sprintf('Unsupported Connection: "%s".', $connection));
    }
}
