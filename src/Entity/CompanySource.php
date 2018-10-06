<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="app_company_sources")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class CompanySource extends BaseEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     * @Assert\Type("Company")
     */
    private $company;

    /**
     * @ORM\Column(name="path", type="string", nullable=false)
     * @Assert\Type("string", groups={"form"})
     */
    private $path;

    /**
     * @ORM\Column(name="price_selector", type="string", nullable=false)
     * @Assert\Type("string", groups={"form"})
     */
    private $priceSelector;

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(Company $company): void
    {
        $this->company = $company;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath($path): void
    {
        $this->path = $path;
    }

    public function getPriceSelector(): ?string
    {
        return $this->priceSelector;
    }

    public function setPriceSelector($priceSelector): void
    {
        $this->priceSelector = $priceSelector;
    }
}