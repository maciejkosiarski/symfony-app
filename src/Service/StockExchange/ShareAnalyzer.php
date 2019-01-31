<?php

declare(strict_types=1);

namespace App\Service\StockExchange;

use App\Entity\Company;
use App\Entity\CompanyShare;
use App\Entity\CompanySource;
use App\Entity\CompanyWatcher;
use App\Entity\Notification;
use App\Repository\CompanyRepository;
use App\Repository\CompanyWatcherRepository;
use App\Repository\CompanyShareRepository;
use Doctrine\ORM\EntityManagerInterface;

class ShareAnalyzer
{
    private $em;
    private $companyRepository;
    private $shareRepository;
    private $watcherRepository;

    public function __construct(
        EntityManagerInterface $em,
        CompanyShareRepository $shareRepository,
        CompanyWatcherRepository $watcherRepository,
        CompanyRepository $companyRepository
    ){
        $this->em = $em;
        $this->companyRepository = $companyRepository;
        $this->shareRepository = $shareRepository;
        $this->watcherRepository = $watcherRepository;
    }

    /**
     * @throws \App\Exception\InvalidNotificationTypeException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \ReflectionException
     */
    public function analyze(): void
    {
        foreach ($this->companyRepository->findByActive(true) as $company) {
            $this->analyzeWeek($company);
        }
    }

    /**
     * @throws \App\Exception\InvalidNotificationTypeException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \ReflectionException
     */
    private function analyzeWeek(Company $company): void
    {
        $watchers = $this->watcherRepository->findByCompany($company);

        if (empty($watchers)) return;

        $shares = $this->shareRepository->findAvgPriceFromLastSevenDays($company);
        $week = '(' . $shares[0]['created'] . ' : ' . $shares[count($shares) - 1]['created'] . ')';
        $weekExtremes = $this->comparePrices((float) $shares[0]['avg'], (float) $shares[count($shares) - 1]['avg']);

        $sources = [];

        /** @var CompanySource $source */
        foreach ($company->getSources() as $source) {
            $sources[] = $source->getPath();
        }

        $source = '<br>' . implode('<br>', $sources);

        if ($weekExtremes > 5) {
            $message = $company->getName() . ' week extremes growth ' . $weekExtremes . '% ' . $week . ' ' .$source;
            $this->notifyWatchers($watchers, $message);
        }

        if ($weekExtremes < -5) {
            $message = $company->getName() . ' week extremes decrease ' . $weekExtremes . '% ' . $week . ' ' .$source;
            $this->notifyWatchers($watchers, $message);
        }

        $previous = null;
        $pricesDifference = [];

        foreach ($shares as $share) {
            if ($previous) {
                $pricesDifference[] = $this->comparePrices((float) $previous, (float) $share['avg']);
                $previous = $share['avg'];
            } else {
                $previous = $share['avg'];
            }
        }

        if ($this->allPositive($pricesDifference)) {
            $message = $company->getName() . ' all week growth ' . implode(' ,', $pricesDifference) . $week . ' ' .$source;
            $this->notifyWatchers($watchers, $message);
        }

        if ($this->allNegative($pricesDifference)) {
            $message = $company->getName() . ' all week decrease ' . implode(' ,', $pricesDifference) . $week . ' ' .$source;
            $this->notifyWatchers($watchers, $message);
        }

        $weekAvg = round(array_sum($pricesDifference) / count($pricesDifference), 2);

        if ($weekAvg > 5) {
            $message = $company->getName() . ' week average growth ' . $weekAvg . '% ' . $week . ' ' .$source;
            $this->notifyWatchers($watchers, $message);
        }

        if ($weekAvg < -5) {
            $message = $company->getName() . ' week average decrease ' . $weekAvg . '% ' . $week . ' ' .$source;
            $this->notifyWatchers($watchers, $message);
        }
    }

    /**
     * @throws \App\Exception\InvalidNotificationTypeException
     * @throws \ReflectionException
     */
    private function notifyWatchers(array $watchers, string $message)
    {
        foreach ($watchers as $watcher) {
            if ($watcher instanceof CompanyWatcher) {
                $notification = new Notification();
                $notification->setUser($watcher->getUser());
                $notification->setType(Notification::TYPE_EMAIL);
                $notification->setMessage($message);
                $notification->setRecurrent(false);
                $notification->setIntervalExpression('* * * * *');

                $this->em->persist($notification);
            }
        }

        $this->em->flush();
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
            $format = '%s Last maximal price: %s zł New one: %s zł Period from %s to today';
            return sprintf(
                $format,
                $share->getCompany()->getName(),
                $extremes['max'],
                $share->getPrice(),
                $share->getCompany()->getCreatedAt()->format('Y-m-d')
            );

        }

        if ($share->getPrice() < $extremes['min']) {
            $format = '%s Last maximal price: %s zł New one: %s zł Period from %s to today';
            return sprintf(
                $format,
                $share->getCompany()->getName(),
                $extremes['min'],
                $share->getPrice(),
                $share->getCompany()->getCreatedAt()->format('Y-m-d')
            );
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