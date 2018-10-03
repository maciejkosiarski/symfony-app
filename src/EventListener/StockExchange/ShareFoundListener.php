<?php

declare(strict_types=1);

namespace App\EventListener\StockExchange;

use App\Entity\Company;
use App\Entity\CompanyProbe;
use App\Event\StockExchange\ShareFoundEvent;
use App\Exception\NotifyException;
use App\Repository\CompanyProbeRepository;
use App\Repository\CompanyShareRepository;
use Doctrine\ORM\EntityManagerInterface;

class ShareFoundListener
{
    private $em;
    private $mailer;
    private $companyProbeRepository;
    private $companyShareRepository;

    public function __construct(
        EntityManagerInterface $em,
        \Swift_Mailer $mailer,
        CompanyProbeRepository $companyProbeRepository,
        CompanyShareRepository $companyShareRepository
    ){
        $this->em = $em;
        $this->mailer = $mailer;
        $this->companyProbeRepository = $companyProbeRepository;
        $this->companyShareRepository = $companyShareRepository;
    }

    /**
     * @throws NotifyException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function onShareFound(ShareFoundEvent $event): void
    {
        $share = $event->getShare();

        $extremes = $this->findExtremes($share->getCompany());

        if ($share->getPrice() > $extremes['max']) {
            /** @var CompanyProbe $probe */
            foreach ($this->findProbes($share->getCompany()) as $probe) {
                $message = (new \Swift_Message($probe->getCompany()->getName() . ' goes up!'))
                    ->setFrom(getenv('MAILER_FROM'))
                    ->setTo($probe->getUser()->getEmail())
                    ->setBody('Last maximal price: ' . $extremes['max'] . ' New one: ' . $share->getPrice());

                if (0 === $this->mailer->send($message)){
                    throw new NotifyException(current($message->getTo()));
                };
            }
        }

        if ($share->getPrice() < $extremes['min']) {
            foreach ($this->findProbes($share->getCompany()) as $probe) {
                $message = (new \Swift_Message($probe->getCompany()->getName() . ' goes down!'))
                    ->setFrom(getenv('MAILER_FROM'))
                    ->setTo($probe->getUser()->getEmail())
                    ->setBody('Last minimal price: ' . $extremes['min'] . ' New one: ' . $share->getPrice());

                if (0 === $this->mailer->send($message)){
                    throw new NotifyException(current($message->getTo()));
                };
            }
        }

        $this->em->persist($share);
        $this->em->flush();
    }

    private function findProbes(Company $company): array
    {
        return $this->companyProbeRepository->findByCompany($company);
    }

    /**
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function findExtremes(Company $company): array
    {
        return $this->companyShareRepository->findExtremesByCompany($company);
    }
}