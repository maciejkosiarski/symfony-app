<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\NotificationQueuePosition;
use App\Event\NotificationActivatedEvent;
use App\Event\NotificationBlockedEvent;
use App\Form\NotificationType;
use App\Repository\NotificationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/notification")
 * @Security("has_role('ROLE_USER')")
 */
class NotificationController extends Controller
{
	/**
	 * @Route("/all/{page}/{limit}",name="notification_index",methods="GET",defaults={
	 *  	"page"=1,
	 *  	"limit"=5
	 *	},
	 *  requirements={
	 *		"page"="\d+",
	 * 		"limit"="\d+"
	 *  })
	 * @param NotificationRepository $notificationRepository
	 * @param int $page
	 * @param int $limit
	 * @return Response
	 * @throws \ReflectionException
	 */
    public function index(NotificationRepository $notificationRepository, int $page, int $limit): Response
    {
		$paginator = $notificationRepository->findPaginateByUser($page, $limit,$this->getUser());

		return $this->render('notification/index.html.twig', [
			'notifications' => $paginator->getIterator(),
			'types'			=> (new Notification())->getTypesLabels(),
			'totalPages'    => ceil($paginator->count() / $limit),
			'currentPage'   => $page,
			'limit'         => $limit,
		]);
    }

    /**
     * @Route("/new", name="notification_new", methods="GET|POST")
	 * @param Request $request
	 * @return Response
	 */
    public function new(Request $request): Response
    {
        $notification = new Notification();
		$notification->setUser($this->getUser());

        $form = $this->createForm(NotificationType::class, $notification);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($notification);
            $em->flush();

			$this->addFlash(
				'success',
				'Notification successfully created!'
			);

            return $this->redirectToRoute('notification_index');
        }

        return $this->render('notification/new.html.twig', [
            'notification' => $notification,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="notification_show", methods="GET")
	 * @param Notification $notification
	 * @return Response
	 * @throws \ReflectionException
	 */
    public function show(Notification $notification): Response
    {
        return $this->render('notification/show.html.twig', [
        	'notification' => $notification,
			'statusLabels' => (new NotificationQueuePosition())->getStatusLabels(),
		]);
    }

    /**
     * @Route("/{id}/edit", name="notification_edit", methods="GET|POST")
	 * @param Request      $request
	 * @param Notification $notification
	 * @return Response
	 */
    public function edit(Request $request, Notification $notification): Response
    {
        $form = $this->createForm(NotificationType::class, $notification);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

			$this->addFlash(
				'success',
				'Notification successfully edited!'
			);

            return $this->redirectToRoute('notification_edit', ['id' => $notification->getId()]);
        }

        return $this->render('notification/edit.html.twig', [
            'notification' => $notification,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="notification_delete", methods="DELETE")
	 * @param Request      $request
	 * @param Notification $notification
	 * @return Response
	 */
    public function delete(Request $request, Notification $notification): Response
    {
        if (!$this->isCsrfTokenValid('delete'.$notification->getId(), $request->request->get('_token'))) {
			$this->addFlash(
				'warning',
				'We have some trouble, Try again later'
			);

            return $this->redirectToRoute('notification_index');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($notification);
        $em->flush();

		$this->addFlash(
			'success',
			'Notification successfully removed!'
		);

        return $this->redirectToRoute('notification_index');
    }

	/**
	 * @Route("/{id}/toggle/active", name="notification_toggle_active", methods="GET")
	 * @param Notification $notification
	 * @param EventDispatcherInterface $dispatcher
	 * @return Response
	 */
	public function activeToggle(Notification $notification, EventDispatcherInterface $dispatcher): Response
	{
		$notification->activeToggle();

		$em = $this->getDoctrine()->getManager();

		$em->flush();

		$this->activeToggleDispatch($notification, $dispatcher);

		return $this->redirectToRoute('notification_index');
    }

	/**
	 * @Route("/{id}/toggle/recurrent", name="notification_toggle_recurrent", methods="GET")
	 * @param Notification $notification
	 * @return Response
	 */
	public function recurrentToggle(Notification $notification): Response
	{
		$notification->recurrentToggle();

		$em = $this->getDoctrine()->getManager();

		$em->flush();

		return $this->redirectToRoute('notification_index');
	}

	/**
	 * @param Notification             $notification
	 * @param EventDispatcherInterface $dispatcher
	 */
	private function activeToggleDispatch(Notification $notification, EventDispatcherInterface $dispatcher)
	{
		if ($notification->isActive()) {
			$dispatcher->dispatch(
				NotificationActivatedEvent::NAME,
				new NotificationActivatedEvent($notification)
			);
		} else {
			$dispatcher->dispatch(
				NotificationBlockedEvent::NAME,
				new NotificationBlockedEvent($notification)
			);
		}
	}
}
