<?php

namespace App\EventSubscriber;

use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;
use Psr\Log\LoggerInterface;

// Ez a mágikus sor hiányzott! Ez regisztrálja be a háttérben a Symfony eseménykezelőjébe
#[AsDoctrineListener(event: Events::postPersist)]
class BadReviewSubscriber
{
    public function __construct(private LoggerInterface $logger)
    {
    }

// A modern Symfony 7 / Doctrine 3 alatt már PostPersistEventArgs-ot használunk LifecycleEventArgs helyett
public function postPersist(PostPersistEventArgs $args): void
{
    $entity = $args->getObject();

    if (!$entity instanceof Review) {
        return;
    }

    // Ha kritikus (1 vagy 2 csillagos) értékelés érkezik
    if ($entity->getRating() <= 2) {
        $this->logger->warning(sprintf(
            'ALERT: Negatív értékelés érkezett a(z) "%s" cégre! Értékelés: %d. Szerző: %s',
            $entity->getCompanyName(),
            $entity->getRating(),
            $entity->getAuthorEmail()
        ));
    }
}
}