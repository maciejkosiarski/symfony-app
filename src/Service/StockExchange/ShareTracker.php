<?php

declare(strict_types=1);

namespace App\Service\StockExchange;

use App\Entity\Company;
use App\Entity\CompanyProbe;
use App\Entity\CompanyShare;
use App\Exception\NotifyException;
use App\Repository\CompanyProbeRepository;
use App\Repository\CompanyShareRepository;
use App\Service\Mail;

class ShareTracker
{
    private $shareRepository;
    private $probeRepository;
    private $mail;

    public function __construct(
        CompanyShareRepository $shareRepository,
        CompanyProbeRepository $probeRepository,
        Mail $mail
    ){
        $this->shareRepository = $shareRepository;
        $this->probeRepository = $probeRepository;
        $this->mail = $mail;
    }

    /**
     * @throws NotifyException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function trackExtremes(CompanyShare $share)
    {
        $extremes = $this->findExtremes($share->getCompany());

        if ($share->getPrice() > $extremes['max']) {
            /** @var CompanyProbe $probe */
            foreach ($this->findProbes($share->getCompany()) as $probe) {
                $this->mail->send(
                    $probe->getCompany()->getName() . ' goes up!',
                    $probe->getUser()->getEmail(),
                    'Last maximal price: ' . $extremes['max'] . ' New one: ' . $share->getPrice()
                );
            }
        }

        if ($share->getPrice() < $extremes['min']) {
            foreach ($this->findProbes($share->getCompany()) as $probe) {
                $this->mail->send(
                    $probe->getCompany()->getName() . ' goes down!',
                    $probe->getUser()->getEmail(),
                    'Last minimal price: ' . $extremes['max'] . ' New one: ' . $share->getPrice()
                );
            }
        }
    }

    private function findProbes(Company $company): array
    {
        return $this->probeRepository->findByCompany($company);
    }

    /**
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function findExtremes(Company $company): array
    {
        return $this->shareRepository->findExtremesByCompany($company);
    }
}