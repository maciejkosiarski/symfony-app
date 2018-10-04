<?php

declare(strict_types=1);

namespace App\EventListener\StockExchange;

use App\Event\StockExchange\ShareFoundEvent;
use App\Exception\NotifyException;
use App\Service\StockExchange\ShareTracker;
use Doctrine\ORM\EntityManagerInterface;

class ShareFoundListener
{
    private $em;
    private $tracker;

    public function __construct(EntityManagerInterface $em, ShareTracker $tracker)
    {
        $this->em = $em;
        $this->tracker = $tracker;
    }

    /**
     * @throws NotifyException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function onShareFound(ShareFoundEvent $event): void
    {
        $share = $event->getShare();

        $this->tracker->trackExtremes($share);

        $this->em->persist($share);
        $this->em->flush();
    }
}