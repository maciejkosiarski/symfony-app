<?php

declare(strict_types=1);

namespace App\Service\StockExchange;

use App\Entity\Company;
use App\Entity\CompanyShare;
use App\Repository\CompanyWatcherRepository;
use App\Repository\CompanyShareRepository;

class ShareAnalyzer
{
    private $shareRepository;
    private $watcherRepository;

    public function __construct(CompanyShareRepository $shareRepository, CompanyWatcherRepository $watcherRepository)
    {
        $this->shareRepository = $shareRepository;
        $this->watcherRepository = $watcherRepository;
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