<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

abstract class BaseEntity
{
	/**
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"api"})
	 */
	protected $id;

	/**
	 * @ORM\Column(name="created_at", type="datetime", nullable=false)
     * @Groups({"api"})
	 */
	protected $createdAt;

	/**
	 * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     * @Groups({"api"})
	 */
	protected $updatedAt;

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getCreatedAt(): \DateTime
	{
		return $this->createdAt;
	}

	public function setCreatedAt(\DateTime $createdAt): void
	{
		$this->createdAt = $createdAt;
	}

	public function getUpdatedAt(): \DateTime
	{
		return $this->updatedAt;
	}

	public function setUpdatedAt(\DateTime $updatedAt): void
	{
		$this->updatedAt = $updatedAt;
	}

	/**
	 * @ORM\PrePersist()
     * @throws \Exception
     */
	public function prePersist(): void
	{
		$this->createdAt = $this->updatedAt = new \DateTime();
	}

	/**
	 * @ORM\PreUpdate()
     * @throws \Exception
     */
	public function preUpdate(): void
	{
		$this->updatedAt = new \DateTime();
	}
}