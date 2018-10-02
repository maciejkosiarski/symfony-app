<?php

declare(strict_types=1);

namespace App\Exception\StockExchange;

use App\Entity\Company;

class AllSourceFailedException extends \Exception
{
    public function __construct(Company $company)
    {
        parent::__construct(sprintf('All source %s (id: %s) failed, cant get current share price.', $company->getName(), $company->getId()));
    }
}