<?php

declare(strict_types=1);

namespace App\Entity\FabricaCacti;

use App\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="fc_questions")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("question")
 */
class Question extends BaseEntity
{
    /**
     * @ORM\Column(name="question", type="string", nullable=false, unique=true)
     * @Assert\Type("string")
     */
    private $question;

    /**
     * @ORM\Column(name="answer", type="string", nullable=false)
     * @Assert\Type("string")
     */
    private $answer;

    /**
     * @ORM\Column(name="active", type="boolean", nullable=false)
     * @Assert\Type("bool")
     */
    private $active = true;

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): void
    {
        $this->question = $question;
    }

    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setAnswer(string $answer): void
    {
        $this->answer = $answer;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }
}