<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
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
    private $active;

    /**
     * @var PersistentCollection
     * @ORM\OneToMany(targetEntity="App\Entity\CompanySource", mappedBy="company", cascade={"persist"})
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $sources;

    public function getName(): string
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

    public function getSources(): PersistentCollection
    {
        return $this->sources;
    }

    public function setSources(PersistentCollection $sources): void
    {
        $this->sources = $sources;
    }
}