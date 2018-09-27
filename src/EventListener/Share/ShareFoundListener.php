<?php

namespace App\EventListener\Share;

use App\Event\Share\ShareFoundEvent;
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