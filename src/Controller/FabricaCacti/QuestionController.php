<?php

namespace App\Controller\FabricaCacti;

use App\Entity\FabricaCacti\Question;
use App\Form\FabricaCacti\QuestionType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/fabrica-cacti/question")
 */
class QuestionController extends Controller
{
    /**
     * @Route("/", name="fabrica_cacti_question_index", methods="GET")
     * @Security("has_role('ROLE_QUESTION_ADMIN')")
     */
    public function index(): Response
    {
        $questions = $this->getDoctrine()
            ->getRepository(Question::class)
            ->findAll();

        return $this->render('fabrica_cacti/question/index.html.twig', ['questions' => $questions]);
    }

    /**
     * @Route("/new", name="fabrica_cacti_question_new", methods="GET|POST")
     * @Security("has_role('ROLE_QUESTION_ADMIN')")
     */
    public function new(Request $request): Response
    {
        $question = new Question();
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($question);
            $em->flush();

            return $this->redirectToRoute('fabrica_cacti_question_index');
        }

        return $this->render('fabrica_cacti/question/new.html.twig', [
            'question' => $question,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="fabrica_cacti_question_show", methods="GET")
     * @Security("has_role('ROLE_QUESTION_ADMIN')")
     */
    public function show(Question $question): Response
    {
        return $this->render('fabrica_cacti/question/show.html.twig', ['question' => $question]);
    }

    /**
     * @Route("/{id}/edit", name="fabrica_cacti_question_edit", methods="GET|POST")
     * @Security("has_role('ROLE_QUESTION_ADMIN')")
     */
    public function edit(Request $request, Question $question): Response
    {
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('fabrica_cacti_question_edit', ['id' => $question->getId()]);
        }

        return $this->render('fabrica_cacti/question/edit.html.twig', [
            'question' => $question,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="fabrica_cacti_question_delete", methods="DELETE")
     * @Security("has_role('ROLE_QUESTION_ADMIN')")
     */
    public function delete(Request $request, Question $question): Response
    {
        if (!$this->isCsrfTokenValid('delete'.$question->getId(), $request->request->get('_token'))) {
            return $this->redirectToRoute('fabrica_cacti_question_index');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($question);
        $em->flush();

        return $this->redirectToRoute('fabrica_cacti_question_index');
    }

    /**
     * @Route("/answer", name="fabrica_cacti_question_answer", methods="POST")
     */
    public function answer(Request $request, EntityManagerInterface $em): JsonResponse
    {
        try {
            $q = $request->request->get('question');
            /** @var Question|null $question */
            if ($question = $em->getRepository(Question::class)->findOneBy(['question' => $q ,'active' => true])) {
                return new JsonResponse($question->getAnswer());
            }

            throw new \Exception(sprintf('Cant find answer for [%s] question', $q));
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(),404);
        }
    }
}
