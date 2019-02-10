<?php

declare(strict_types=1);

namespace App\Service\Library;

use App\Entity\Library\Article;
use App\Entity\Library\Tag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LibraryService
{
    private $em;
    private $validator;

    public function __construct(EntityManagerInterface $em, ValidatorInterface $vi)
    {
        $this->em = $em;
        $this->validator = $vi;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function addArticle(array $data)
    {
        $article = new Article();
        $article->loadData($data);

        $errors = $this->validator->validate($article);

        if ($errors->count()) {
            /** @var ConstraintViolation $error */
            foreach ($errors as $error) {
                throw new \InvalidArgumentException($error->getPropertyPath() .': '. $error->getMessage(), 422);
            }
        }
        $this->em->persist($article);
        $this->em->flush();
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function addTag(array $data)
    {
        $tag = new Tag();
        $tag->loadData($data);

        $errors = $this->validator->validate($tag);

        if ($errors->count()) {
            /** @var ConstraintViolation $error */
            foreach ($errors as $error) {
                throw new \InvalidArgumentException($error->getPropertyPath() .': '. $error->getMessage(), 422);
            }
        }
        $this->em->persist($tag);
        $this->em->flush();
    }
}