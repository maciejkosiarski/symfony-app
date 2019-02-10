<?php

declare(strict_types=1);

namespace App\Entity\Library;

use App\Entity\BaseEntity;
use App\Entity\DataLoader;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="library_tags")
 * @ORM\Entity(repositoryClass="App\Repository\Library\TagRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("name")
 */
class Tag extends BaseEntity
{
    use DataLoader;

    /**
     * @ORM\Column(name="name", type="string", nullable=false, unique=true, length=20)
     * @Assert\Type("string")
     * @Assert\NotBlank()
     * @Assert\Length(min="2", max="20")
     * @Groups({"api"})
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Library\Article", mappedBy="tags")
     */
    protected $articles;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): void
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->addTag($this);
        }
    }

    public function removeArticle(Article $article): void
    {
        if ($this->articles->contains($article)) {
            $this->articles->removeElement($article);
            $article->removeTag($this);
        }
    }
}