<?php

declare(strict_types=1);

namespace App\Exception\StockExchange;

use App\Entity\CompanySource;

class SourceFailedException extends \Exception
{
    public function __construct(CompanySource $source)
    {
        parent::__construct(
            sprintf(
                'Source %s (id: %s) failed, cant get current share price. (%s)',
                $source->getCompany()->getName(),
                $source->getId(),
                $source->getPath()
            )
        );
    }
}