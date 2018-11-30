<?php

declare(strict_types=1);

namespace App\Service\StockExchange;

use App\Entity\CompanyWatcher;
use App\Repository\CompanyWatcherRepository;
use App\Repository\CompanyShareRepository;

class ShareCalculator
{
    private $shares;
    private $watchers;

    public function __construct(CompanyShareRepository $shares, CompanyWatcherRepository $watchers)
    {
        $this->shares = $shares;
        $this->watchers = $watchers;
    }

    public function calculate()
    {
        /** @var CompanyWatcher $watcher */
        foreach ($this->watchers->findAll() as $watcher) {
            $this->shares->findByCompany($watcher->getCompany());
        }
    }
}