<?php

namespace App\Controller\Library;

use App\Controller\ApiResponsible;
use App\Entity\Library\Article;
use App\Repository\Library\ArticleRepository;
use App\Service\Library\LibraryService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api/v1/library/articles")
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
    public function index(Request $request, ArticleRepository $articles, SerializerInterface $serializer): JsonResponse
    {
        try {
            $page = $request->get('page') ? $request->get('page') : 1;
            $limit = $request->get('limit') ? $request->get('limit') : 10;

            $paginator = $articles->findAllPaginated($page, $limit);

            return $this->getJsonResponse($serializer->serialize([
                'articles' => $paginator->getIterator(),
                'all' => $paginator->count(),
            ], 'json', ['groups' => ['api']]));
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return $this->getJsonResponse('{"description":"Request failed"}', $e->getCode());
        }
    }

    /**
     * @Route("/", name="library_article_new", methods="POST")
     */
    public function new(Request $request, LibraryService $library): JsonResponse
    {
        try {
            $article = json_decode($request->getContent(), true);
            $library->addArticle($article);

            $message = '{"description":"New %s article was successfully added"}';
            return $this->getJsonResponse(sprintf($message, $article['url']));
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return $this->getJsonResponse(sprintf('{"description":"%s"}', $e->getMessage()), $e->getCode());
        }
    }

    /**
     * @Route("/tags/{ids}", name="library_article_show_by_tag", methods="GET")
     */
    public function showByTags(Request $request, ArticleRepository $articles, SerializerInterface $serializer)
    {
        try {
            $tags = array_filter(
                array_map('intval',explode(',', $request->get('ids'))), function ($id) {
                    return ($id > 0);
                }
            );

            $articles = $articles->findByTags($tags);

            return $this->getJsonResponse($serializer->serialize([
                'articles' => $articles->getValues(),
                'all' => 0,
            ], 'json', ['groups' => ['api']]));
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return $this->getJsonResponse('{"description":""Request failed""}', $e->getCode());
        }
    }

    /**
     * @Route("/{id}", name="library_article_show", methods="GET", requirements={
     *		"id" = "\d+"
     *  })
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

//    /**
//     * @Route("/{id}", name="library_article_edit", methods="PUT")
//     */
//    public function edit(Request $request, Article $article): Response
//    {
//        try {
//            // set article
//            //persist flush
//
//            return $this->getJsonResponse('{"description":"Request success"}');
//        } catch (\Exception $e) {
//            $this->logger->error($e->getMessage());
//            return $this->getJsonResponse('{"description":"Request failed"}', $e->getCode());
//        }
//    }

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

    /**
     * @Route("/{all}", defaults={"all" = null}, requirements={"all"=".+"},
     *     name="library_article_not_found",
     *     methods="GET|POST|PUT|DELETE")
     */
    public function notFound()
    {
        return $this->getJsonResponse('{"description":"Request failed, source not found"}', 404);
    }
}
