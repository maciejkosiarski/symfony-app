<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Share
 *
 * @package App\Entity
 * @ORM\Table(name="app_shares")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 * @author  Maciej Kosiarski <maciek.kosiarski@gmail.com>
 */
class Share extends BaseEntity
{
    /**
     * @ORM\Column(name="company", type="string", nullable=false)
     */
    private $company;

    /**
     * @ORM\Column(name="price", type="float", nullable=false)
     */
    private $price;

    public function getCompany(): string
    {
        return $this->company;
    }

    public function setCompany(string $company): void
    {
        $this->company = $company;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }
}