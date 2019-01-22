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
 * @ORM\Table(name="library_articles")
 * @ORM\Entity(repositoryClass="App\Repository\Library\ArticleRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("url")
 */
class Article extends BaseEntity
{
    use DataLoader;

    /**
     * @ORM\Column(name="title", type="string", nullable=false, length=150)
     * @Assert\Type("string")
     * @Assert\NotBlank()
     * @Groups({"api"})
     */
    private $title;

    /**
     * @ORM\Column(name="description", type="string", nullable=true, length=255)
     * @Assert\Type("string")
     * @Groups({"api"})
     */
    private $description;

    /**
     * @ORM\Column(name="url", type="string", nullable=false, unique=true, length=255)
     * @Assert\Type("string")
     * @Assert\NotBlank()
     * @Groups({"api"})
     */
    private $url;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Library\Tag", inversedBy="articles")
     * @Groups({"api"})
     */
    protected $tags;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->title;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): void
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }
    }

    public function removeTag(Tag $tag): void
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }
    }
}