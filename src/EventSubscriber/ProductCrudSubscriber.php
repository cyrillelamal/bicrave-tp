<?php

namespace App\EventSubscriber;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Event\AbstractLifecycleEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

/**
 * This subscriber handles events related to the Product entity dispatched by the EasyAdmin.
 */
class ProductCrudSubscriber implements EventSubscriberInterface
{
    public const TAGS = [
        'products',
        'categories',
    ];

    private LoggerInterface $logger;
    private TagAwareCacheInterface $cache;

    public function __construct(
        LoggerInterface        $logger,
        TagAwareCacheInterface $cache,
    )
    {
        $this->logger = $logger;
        $this->cache = $cache;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            AfterEntityPersistedEvent::class => 'invalidateCache',
            AfterEntityUpdatedEvent::class => 'invalidateCache',
            AfterEntityDeletedEvent::class => 'invalidateCache',
        ];
    }

    public function invalidateCache(AbstractLifecycleEvent $event): void
    {
        $image = $event->getEntityInstance();

        if (!$image instanceof Product) {
            return;
        }

        try {
            $this->cache->invalidateTags(self::TAGS);
        } catch (InvalidArgumentException $e) {
            $this->logger->error('Cannot invalidate cache', ['exception' => $e, 'tags' => self::TAGS]);
        }
    }
}
