<?php

namespace App\Controller;

use App\Entity\Exercise;
use App\Form\ExerciseType;
use App\Repository\ExerciseRepository;
use Doctrine\ORM\NonUniqueResultException;
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
     * @Route("/all/{page}/{limit}",name="exercise_index",methods="GET",defaults={
	 *  	"page" = 1,
	 *  	"limit" = 5
	 *	},
	 *  requirements={
	 *		"page" = "\d+",
     * 		"limit" = "\d+"
	 *  })
	 * @param ExerciseRepository $exerciseRepository
	 * @param int $page
	 * @param int $limit
	 * @return Response
	 * @throws NonUniqueResultException
	 */
    public function index(ExerciseRepository $exerciseRepository, int $page, int $limit): Response
    {
		$paginator = $exerciseRepository->findPaginateByUser($page, $limit,$this->getUser());

        return $this->render('exercise/index.html.twig', [
        	'exercises'     => $paginator->getIterator(),
			'totalPages'    => ceil($paginator->count() / $limit),
			'currentPage'   => $page,
			'limit'         => $limit,
			'count'		    => $paginator->count(),
			'firstExercise' => $exerciseRepository->findOneBy([], ['createdAt' => 'ASC']),
			'totalTime'     => $exerciseRepository->countTotalHoursByUser($this->getUser()),
		]);
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

			$this->addFlash(
				'success',
				'Exercise successfully created!'
			);

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

			$this->addFlash(
				'success',
				'Exercise successfully edited!'
			);

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
			$this->addFlash(
				'warning',
				'We have some trouble, Try again later'
			);

            return $this->redirectToRoute('exercise_index');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($exercise);
        $em->flush();

		$this->addFlash(
			'success',
			'Exercise successfully removed!'
		);

        return $this->redirectToRoute('exercise_index');
    }
}
