<?php

namespace App\Controller\Library;

use App\Controller\ApiResponsible;
use App\Entity\Library\Article;
use App\Repository\Library\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/library/api/v1/articles")
 */
class ArticleController extends Controller
{
    use ApiResponsible;

    private $logger;

    public function __construct(LoggerInterface $l)
    {
        $this->logger = $l;
    }

    /**
     * @Route("/", name="library_article_index", methods="GET")
     */
    public function index(ArticleRepository $articles, SerializerInterface $serializer): JsonResponse
    {
        try {
            return $this->getJsonResponse($serializer->serialize([
                'articles' => $articles->findAll()
            ], 'json', ['groups' => ['api']]));
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return $this->getJsonResponse('{"description":"Request failed"}', $e->getCode());
        }
    }

    /**
     * @Route("/", name="library_article_new", methods="POST")
     */
    public function new(Request $request): JsonResponse
    {
        try {
            $article = new Article();
            // set article
            //persist flush

            return $this->getJsonResponse('{"description":"Request success"}');
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return $this->getJsonResponse('{"description":"Request failed"}', $e->getCode());
        }
    }

    /**
     * @Route("/{id}", name="library_article_show", methods="GET")
     */
    public function show(Article $article, SerializerInterface $serializer): JsonResponse
    {
        try {
            return $this->getJsonResponse($serializer->serialize([
                'article' => $article
            ], 'json', ['groups' => ['api']]));
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return $this->getJsonResponse('{"description":"Request failed"}', $e->getCode());
        }
    }

    /**
     * @Route("/{id}", name="library_article_edit", methods="PUT")
     */
    public function edit(Request $request, Article $article): Response
    {
        try {
            // set article
            //persist flush

            return $this->getJsonResponse('{"description":"Request success"}');
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return $this->getJsonResponse('{"description":"Request failed"}', $e->getCode());
        }
    }

    /**
     * @Route("/{id}", name="library_article_delete", methods="DELETE")
     */
    public function delete(Article $article, EntityManagerInterface $em): JsonResponse
    {
        try {
            $em->remove($article);
            $em->flush();

            return $this->getJsonResponse('{"description":"Request success"}');
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return $this->getJsonResponse('{"description":"Request failed"}', $e->getCode());
        }
    }
}
