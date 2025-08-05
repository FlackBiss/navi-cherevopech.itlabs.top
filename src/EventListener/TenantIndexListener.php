<?php

namespace App\EventListener;

use App\Entity\Tenant;
use App\Service\MeilisearchService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::postPersist)]
#[AsDoctrineListener(event: Events::postUpdate)]
#[AsDoctrineListener(event: Events::preRemove)]
readonly class TenantIndexListener
{
    public function __construct(private MeilisearchService $meilisearchService) {}

    public function postPersist(PostPersistEventArgs $args): void
    {
        $entity = $args->getObject();
        if ($entity instanceof Tenant) {
            $this->meilisearchService->indexTenant($entity);
        }
    }

    public function postUpdate(PostUpdateEventArgs $args): void
    {
        $entity = $args->getObject();
        if ($entity instanceof Tenant) {
            $this->meilisearchService->indexTenant($entity);
        }
    }

    public function preRemove($args): void
    {
        $entity = $args->getObject();
        if ($entity instanceof Tenant) {
            $this->meilisearchService->deleteTenantFromIndex($entity);
        }
    }
}
