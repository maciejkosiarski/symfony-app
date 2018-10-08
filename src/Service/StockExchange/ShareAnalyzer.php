<?php

declare(strict_types=1);

namespace App\Service\StockExchange;

use App\Entity\Company;
use App\Entity\CompanyShare;
use App\Repository\CompanyProbeRepository;
use App\Repository\CompanyShareRepository;

class ShareAnalyzer
{
    private $shareRepository;
    private $probeRepository;

    public function __construct(CompanyShareRepository $shareRepository, CompanyProbeRepository $probeRepository)
    {
        $this->shareRepository = $shareRepository;
        $this->probeRepository = $probeRepository;
    }

    /**
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function checkExtremes(CompanyShare $share): string
    {
        $extremes = $this->findExtremes($share->getCompany());

        if ($share->getPrice() > $extremes['max']) {
            return 'Last maximal price: ' . $extremes['max'] . ' New one: ' . $share->getPrice();
        }

        if ($share->getPrice() < $extremes['min']) {
            return 'Last minimal price: ' . $extremes['max'] . ' New one: ' . $share->getPrice();
        }

        return '';
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