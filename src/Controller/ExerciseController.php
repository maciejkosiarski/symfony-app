<?php

namespace App\Controller;

use App\Entity\Exercise;
use App\Form\ExerciseType;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/exercise")
 * @Security("has_role('ROLE_USER')")
 */
class ExerciseController extends Controller
{
    /**
     * @Route("/", name="exercise_index", methods="GET")
	 * @return Response
	 */
    public function index(): Response
    {
		$exerciseRepository = $this->getDoctrine()->getRepository(Exercise::class);

        return $this->render('exercise/index.html.twig', ['exercises' => $exerciseRepository->findByUser($this->getUser()->getId())]);
    }

    /**
     * @Route("/new", name="exercise_new", methods="GET|POST")
	 * @param Request $request
	 * @return Response
	 * @throws \App\Exception\InvalidExerciseTypeException
	 * @throws \ReflectionException
	 */
    public function new(Request $request): Response
    {
        $exercise = new Exercise($this->getUser(),1,30);
        $form = $this->createForm(ExerciseType::class, $exercise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($exercise);
            $em->flush();

            return $this->redirectToRoute('exercise_index');
        }

        return $this->render('exercise/new.html.twig', [
            'exercise' => $exercise,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="exercise_show", methods="GET")
	 * @param Exercise $exercise
	 * @return Response
	 */
    public function show(Exercise $exercise): Response
    {
        return $this->render('exercise/show.html.twig', ['exercise' => $exercise]);
    }

    /**
     * @Route("/{id}/edit", name="exercise_edit", methods="GET|POST")
	 * @param Request  $request
	 * @param Exercise $exercise
	 * @return Response
	 */
    public function edit(Request $request, Exercise $exercise): Response
    {
        $form = $this->createForm(ExerciseType::class, $exercise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('exercise_edit', ['id' => $exercise->getId()]);
        }

        return $this->render('exercise/edit.html.twig', [
            'exercise' => $exercise,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="exercise_delete", methods="DELETE")
	 * @param Request  $request
	 * @param Exercise $exercise
	 * @return Response
	 */
    public function delete(Request $request, Exercise $exercise): Response
    {
        if (!$this->isCsrfTokenValid('delete'.$exercise->getId(), $request->request->get('_token'))) {
            return $this->redirectToRoute('exercise_index');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($exercise);
        $em->flush();

        return $this->redirectToRoute('exercise_index');
    }
}
