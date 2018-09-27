<?php

namespace App\Event\Share;

use App\Entity\Share;
use Symfony\Component\EventDispatcher\Event;

class ShareFoundEvent extends Event
{
    const NAME = 'share.found';

    /**
     * @var Share
     */
    protected $share;

    public function __construct(Share $share)
    {
        $this->share = $share;
    }

    public function getShare(): Share
    {
        return $this->share;
    }
}