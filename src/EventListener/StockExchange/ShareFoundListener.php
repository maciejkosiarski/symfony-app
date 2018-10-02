<?php

declare(strict_types=1);

namespace App\EventListener\StockExchange;

use App\Event\StockExchange\ShareFoundEvent;
use Doctrine\ORM\EntityManagerInterface;

class ShareFoundListener
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function onShareFound(ShareFoundEvent $event): void
    {
        $this->em->persist($event->getShare());
        $this->em->flush();
    }
}