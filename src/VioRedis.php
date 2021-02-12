<?php declare(strict_types=1);

namespace Vio\Redis;

use Exception;
use Shopware\Core\Framework\Plugin;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class VioRedis extends Plugin
{

    /**
     * @param ContainerBuilder $container
     *
     * @throws Exception
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        if(getenv('REDIS_CACHE_ENABLED')) {
            $container->setParameter(
                $this->getContainerPrefix() . '.cache.host',
                (string)getenv('REDIS_CACHE_HOST')
            );
            $container->setParameter(
                $this->getContainerPrefix() . '.cache.port',
                (int)getenv('REDIS_CACHE_PORT')
            );
            $container->setParameter(
                $this->getContainerPrefix() . '.cache.password',
                (string)getenv('REDIS_CACHE_PASSWORD')
            );
            $container->setParameter(
                $this->getContainerPrefix() . '.cache.db',
                (int)getenv('REDIS_CACHE_db')
            );

            $container->loadFromExtension('framework', [
                'cache' => [
                    'app' => 'cache.adapter.redis',
                    'system' => 'cache.adapter.redis',
                    'pools' => [
                        'serializer' => [
                            'adapter' => 'cache.adapter.redis'
                        ],
                        'annotations' => [
                            'adapter' => 'cache.adapter.redis'
                        ],
                        'property_info' => [
                            'adapter' => 'cache.adapter.redis'
                        ],
                        'messenger' => [
                            'adapter' => 'cache.adapter.redis'
                        ],
                        'property_access' => [
                            'adapter' => 'cache.adapter.redis'
                        ],
                    ],
                    'default_redis_provider' => "redis://%vio_redis.cache.password%@%vio_redis.cache.host%:%vio_redis.cache.port%/%vio_redis.cache.db%"
                ]
            ]);
        }

        if(getenv('REDIS_SESSION_ENABLED')) {
            $container->setParameter(
                $this->getContainerPrefix() . '.session.host',
                (string)getenv('REDIS_SESSION_HOST')
            );
            $container->setParameter(
                $this->getContainerPrefix() . '.session.port',
                (int)getenv('REDIS_SESSION_PORT')
            );
            $container->setParameter(
                $this->getContainerPrefix() . '.session.password',
                (string)getenv('REDIS_SESSION_PASSWORD')
            );
            $container->setParameter(
                $this->getContainerPrefix() . '.session.db',
                (int)getenv('REDIS_SESSION_DB')
            );


            $loader = new XmlFileLoader(
                $container,
                new FileLocator()
            );
            $loader->load($this->getPath() . '/Resources/config/services/session.xml');

            $container->loadFromExtension('framework', [
                'session' => [
                    'handler_id' => $this->getContainerPrefix() . '.session.redis_session_handler'
                ]
            ]);

        }
    }

}
