<?php

declare(strict_types=1);

namespace App\EventListener\StockExchange;

use App\Entity\CompanyProbe;
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
            dump($body);
            /** @var CompanyProbe $probe */
            foreach ($this->em->getRepository(CompanyProbe::class)->findByCompany($share->getCompany()) as $probe) {
                $this->mail->send(
                    $probe->getCompany()->getName(),
                    $probe->getUser()->getEmail(),
                    $body
                );
            }
        }

        $this->em->persist($share);
        $this->em->flush();
    }
}