<?php

namespace App\EventSubscriber;

use App\Entity\Category;
use App\UseCase\Category\MoveUpChildren;
use EasyCorp\Bundle\EasyAdminBundle\Event\AbstractLifecycleEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityDeletedEvent;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

/**
 * This subscriber handles events related to the Category entity dispatched by the EasyAdmin.
 */
class CategoryCrudSubscriber implements EventSubscriberInterface
{
    public const TAGS = [
        'categories',
    ];

    private LoggerInterface $logger;
    private TagAwareCacheInterface $cache;
    private MoveUpChildren $moveUpChildren;

    public function __construct(
        LoggerInterface        $logger,
        TagAwareCacheInterface $cache,
        MoveUpChildren         $moveUpChildren,
    )
    {
        $this->logger = $logger;
        $this->cache = $cache;
        $this->moveUpChildren = $moveUpChildren;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            AfterEntityPersistedEvent::class => 'invalidateCache',
            AfterEntityUpdatedEvent::class => 'invalidateCache',
            BeforeEntityDeletedEvent::class => 'handleDelete',
            AfterEntityDeletedEvent::class => 'invalidateCache',
        ];
    }

    public function invalidateCache(AbstractLifecycleEvent $event): void
    {
        $category = $event->getEntityInstance();

        if (!$category instanceof Category) {
            return;
        }

        try {
            $this->cache->invalidateTags(self::TAGS);
        } catch (InvalidArgumentException $e) {
            $this->logger->error('Cannot invalidate cache', ['exception' => $e, 'tags' => self::TAGS]);
        }
    }

    public function handleDelete(BeforeEntityDeletedEvent $event): void
    {
        $category = $event->getEntityInstance();

        if ($category instanceof Category) {
            return;
        }

        ($this->moveUpChildren)($category);
    }
}
