<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Form\NotificationType;
use App\Repository\NotificationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
     * @Route("/", name="notification_index", methods="GET")
	 * @param NotificationRepository $notificationRepository
	 * @return Response
     */
    public function index(NotificationRepository $notificationRepository): Response
    {
        return $this->render('notification/index.html.twig', [
        	'notifications' => $notificationRepository->findByUser($this->getUser()),
		]);
    }

    /**
     * @Route("/new", name="notification_new", methods="GET|POST")
	 * @param Request $request
	 * @return Response
	 * @throws \App\Exception\InvalidNotificationTypeException
	 * @throws \ReflectionException
	 */
    public function new(Request $request): Response
    {
        $notification = new Notification($this->getUser(), Notification::TYPE_EMAIL);
        $form = $this->createForm(NotificationType::class, $notification);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($notification);
            $em->flush();

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
	 */
    public function show(Notification $notification): Response
    {
        return $this->render('notification/show.html.twig', ['notification' => $notification]);
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
            return $this->redirectToRoute('notification_index');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($notification);
        $em->flush();

        return $this->redirectToRoute('notification_index');
    }

	/**
	 * @Route("/{id}/toggle/active", name="notification_toggle_active", methods="GET")
	 * @param Notification $notification
	 * @return Response
	 */
	public function activeToggle(Notification $notification)
	{
		$notification->activeToggle();

		$em = $this->getDoctrine()->getManager();

		$em->flush();

		return $this->redirectToRoute('notification_index');
    }
}
