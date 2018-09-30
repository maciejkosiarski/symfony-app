<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="app_company_shares")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class CompanyShare extends BaseEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     * @Assert\Type("App\Entity\Company")
     */
    private $company;

    /**
     * @ORM\Column(name="price", type="float", nullable=false)
     */
    private $price;

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function setCompany(Company $company): void
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