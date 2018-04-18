<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class BaseEntity
 *
 * @package App\Entity
 * @author  Maciej Kosiarski <maciek.kosiarski@gmail.com>
 */
abstract class BaseEntity
{
	/**
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @var \DateTime
	 * @ORM\Column(name="created_at", type="datetime", nullable=false)
	 */
	protected $createdAt;

	/**
	 * @var \DateTime
	 * @ORM\Column(name="updated_at", type="datetime", nullable=false)
	 */
	protected $updatedAt;

	/**
	 * @return integer|null
	 */
	public function getId(): ?int
	{
		return $this->id;
	}

	/**
	 * @return \DateTime
	 */
	public function getCreatedAt(): \DateTime
	{
		return $this->createdAt;
	}

	/**
	 * @param \DateTime $createdAt
	 */
	public function setCreatedAt(\DateTime $createdAt): void
	{
		$this->createdAt = $createdAt;
	}

	/**
	 * @return \DateTime
	 */
	public function getUpdatedAt(): \DateTime
	{
		return $this->updatedAt;
	}

	/**
	 * @param \DateTime $updatedAt
	 */
	public function setUpdatedAt(\DateTime $updatedAt): void
	{
		$this->updatedAt = $updatedAt;
	}

	/**
	 * @ORM\PrePersist()
	 */
	public function prePersist(): void
	{
		$this->createdAt = $this->updatedAt = new \DateTime();
	}

	/**
	 * @ORM\PreUpdate()
	 */
	public function preUpdate(): void
	{
		$this->updatedAt = new \DateTime();
	}
}