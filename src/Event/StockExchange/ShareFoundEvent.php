<?php

declare(strict_types=1);

namespace App\Event\StockExchange;

use App\Entity\CompanyShare;
use Symfony\Component\EventDispatcher\Event;

class ShareFoundEvent extends Event
{
    const NAME = 'share.found';

    protected $share;

    public function __construct(CompanyShare $share)
    {
        $this->share = $share;
    }

    public function getShare(): CompanyShare
    {
        return $this->share;
    }
}