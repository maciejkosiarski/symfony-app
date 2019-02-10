<?php

namespace App\Controller\Library;

use App\Controller\ApiResponsible;
use App\Entity\Library\Tag;
use App\Repository\Library\TagRepository;
use App\Service\Library\LibraryService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api/v1/library/tags")
 */
class TagController extends Controller
{
    use ApiResponsible;

    private $logger;

    public function __construct(LoggerInterface $l)
    {
        $this->logger = $l;
    }

    /**
     * @Route("/", name="library_tag_index", methods="GET")
     */
    public function index(TagRepository $tags, SerializerInterface $serializer): JsonResponse
    {
        try {
            return $this->getJsonResponse($serializer->serialize([
                'tags' => $tags->findAll()
            ], 'json', ['groups' => ['api']]));
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return $this->getJsonResponse('Request failed', $e->getCode());
        }
    }

    /**
     * @Route("/", name="library_tag_new", methods="POST")
     */
    public function new(Request $request, LibraryService $library): JsonResponse
    {
        try {
            $tag = json_decode($request->getContent(), true);
            $library->addTag($tag);

            $message = '{"description":"New %s tag was successfully added"}';
            return $this->getJsonResponse(sprintf($message, $tag['name']));
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return $this->getJsonResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @Route("/{id}", name="library_tag_show", methods="GET")
     */
    public function show(Tag $tag, SerializerInterface $serializer): Response
    {
        try {
            return $this->getJsonResponse($serializer->serialize([
                'tag' => $tag
            ], 'json', ['groups' => ['api']]));
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return $this->getJsonResponse('Request failed', $e->getCode());
        }
    }

    /**
     * @Route("/{id}/edit", name="library_tag_edit", methods="GET|POST")
     */
    public function edit(Request $request, Tag $tag): JsonResponse
    {
        try {
            // set tag
            //persist flush

            return $this->getJsonResponse('{"description":"Request success"}');
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return $this->getJsonResponse('Request failed', $e->getCode());
        }
    }

    /**
     * @Route("/{id}", name="library_tag_delete", methods="DELETE")
     */
    public function delete(Tag $tag, EntityManagerInterface $em): JsonResponse
    {
        try {
            $em->remove($tag);
            $em->flush();

            return $this->getJsonResponse('{"description":"Request success"}');
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return $this->getJsonResponse('Request failed', $e->getCode());
        }
    }

    /**
     * @Route("/ping", name="library_tag_ping", methods="POST")
     */
    public function ping(): JsonResponse
    {
        try {
            return $this->getJsonResponse('{"description":"Ping"}');
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return $this->getJsonResponse($e->getMessage(), $e->getCode());
        }
    }
}
