<?php

declare(strict_types=1);

namespace App\Service\StockExchange;

use App\Entity\Company;
use App\Entity\CompanyShare;
use App\Repository\CompanyRepository;
use App\Repository\CompanyWatcherRepository;
use App\Repository\CompanyShareRepository;

class ShareAnalyzer
{
    private $companyRepository;
    private $shareRepository;
    private $watcherRepository;

    public function __construct(CompanyShareRepository $shareRepository, CompanyWatcherRepository $watcherRepository, CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
        $this->shareRepository = $shareRepository;
        $this->watcherRepository = $watcherRepository;
    }

    public function analyze()
    {
        //$testCompany = $this->companyRepository->find(1);

        //$this->analyzeWeek($testCompany);

        //$this->analyzeMonth($testCompany);

        //todo throw exception if 0 results
    }

    private function analyzeWeek(Company $company)
    {
        $shares = $this->shareRepository->findLastSixDays($company);
        /** @var CompanyShare $previous */
        $previous = null;
        $pricesDifference = [];
        /** @var CompanyShare $share */
        foreach ($shares as $share) {
            if ($previous) {
                $pricesDifference[] = $this->comparePrices($previous->getPrice(), $share->getPrice());;
                $previous = $share;
            } else {
                $previous = $share;
            }
        }


        if ($this->allPositive($pricesDifference)) {
            dump('all positive');
        }

        if ($this->allNegative($pricesDifference)) {
            dump('all negative');
        }
    }

    private function analyzeMonth(Company $company)
    {

    }

    private function comparePrices(float $previous, float $current): float
    {
        if ($previous > $current) {
            return  round($current * 100 / $previous - 100, 2);
        }

        if ($previous < $current) {
            return round($current * 100 / $previous - 100, 2);
        }

        return 0;
    }

    private function allPositive(array $pricesDifference): bool
    {
        array_walk($pricesDifference, function ($price) {
            if (is_float($price)) return true;
            if (is_int($price)) return true;
            throw new \Exception('Invalid prices diff');
        });

        return min($pricesDifference) > 0;
    }

    private function allNegative(array $pricesDifference): bool
    {
        array_walk($pricesDifference, function ($price) {
            if (is_float($price)) return true;
            if (is_int($price)) return true;
            throw new \Exception('Invalid prices diff');
        });

        return max($pricesDifference) < 0;
    }

    /**
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function checkExtremes(CompanyShare $share): string
    {
        $extremes = $this->findExtremes($share->getCompany());

        if ($share->getPrice() > $extremes['max']) {
            return 'Last maximal price: ' .
                $extremes['max'] .
                ' zł New one: ' .
                $share->getPrice() .
                ' zł Period from ' .
                $share->getCompany()->getCreatedAt()->format('Y-m-d') .
                ' to today';
        }

        if ($share->getPrice() < $extremes['min']) {
            return 'Last minimal price: ' .
                $extremes['min'] .
                ' zł New one: ' .
                $share->getPrice() .
                ' zł Period from ' .
                $share->getCompany()->getCreatedAt()->format('Y-m-d') .
                ' to today';
        }

        return '';
    }

    /**
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function checkDifference(CompanyShare $share): string
    {
        $previous = $this->shareRepository->findLastPreviousDay($share->getCompany());

        $difference = $this->comparePrices($previous->getPrice(), $share->getPrice());

        if ($difference > 5) {
            return $share->getCompany()->getName() .
                ' get ' . $difference . '% growth (' .
                $previous->getPrice() . ' zł ---> ' .
                $share->getPrice() . ' zł)';
        }

        if ($difference < -5) {
            return $share->getCompany()->getName() .
                ' get ' . $difference . '% decrease (' .
                $previous->getPrice() . ' zł ---> ' .
                $share->getPrice() . ' zł)';
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