<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="app_companies")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Company extends BaseEntity
{
    /**
     * @ORM\Column(name="name", type="string", nullable=false)
     * @Assert\Type("string")
     */
    private $name;

    /**
     * @ORM\Column(name="active", type="boolean", nullable=false)
     * @Assert\Type("boolean")
     */
    private $active = true;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CompanySource", mappedBy="company", cascade={"persist"})
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $sources;

    public function __construct()
    {
        $this->sources = new ArrayCollection();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function activeToggle(): void
    {
        $this->active = !$this->active;
    }

    public function getSources(): Collection
    {
        return $this->sources;
    }

    public function addToSources(CompanySource $source): void
    {
        $this->sources->add($source);
    }
}