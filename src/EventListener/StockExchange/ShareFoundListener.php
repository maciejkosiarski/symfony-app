<?php

declare(strict_types=1);

namespace App\EventListener\StockExchange;

use App\Entity\CompanyWatcher;
use App\Event\StockExchange\ShareFoundEvent;
use App\Exception\NotifyException;
use App\Service\Mail;
use App\Service\StockExchange\ShareAnalyzer;
use Doctrine\ORM\EntityManagerInterface;

class ShareFoundListener
{
    private $em;
    private $analyzer;
    private $mail;

    public function __construct(EntityManagerInterface $em, ShareAnalyzer $analyzer, Mail $mail)
    {
        $this->em = $em;
        $this->analyzer = $analyzer;
        $this->mail = $mail;
    }

    /**
     * @throws NotifyException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function onShareFound(ShareFoundEvent $event): void
    {
        $share = $event->getShare();

        if ($body = $this->analyzer->checkExtremes($share)) {
            $repository = $this->em->getRepository(CompanyWatcher::class);
            /** @var CompanyWatcher $watcher */
            foreach ($repository->findByCompany($share->getCompany()) as $watcher) {
                $this->mail->send(
                    $watcher->getCompany()->getName(),
                    $watcher->getUser()->getEmail(),
                    $body
                );
            }
        }

        $this->em->persist($share);
        $this->em->flush();
    }
}