<?php

namespace App\Event\StockExchange;

use App\Entity\CompanyShare;
use Symfony\Component\EventDispatcher\Event;

class ShareFoundEvent extends Event
{
    const NAME = 'share.found';

    /**
     * @var CompanyShare
     */
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