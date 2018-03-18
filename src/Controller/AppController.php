<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Notification;
use App\Service\Notifier;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class DefaultController
 * @package App\Controller
 * @author  Maciej Kosiarski <maciek.kosiarski@gmail.com>
 */
class AppController extends Controller {

	/**
	 * @Route("/")
	 * @param Notifier $notifier
	 * @return Response
	 */
	public function index(SerializerInterface $serializer, Notifier $notifier): Response
	{
		$notifications = $notifier->find(Notification::TYPE_EMAIL);

		return new JsonResponse(
			$notifications->map(function (Notification $notification) {
				return $notification->getUser()->getEmail() . ' -> ' .$notification->getMessage();
			})->toArray()
		);
	}

	/**
	 * @Route("/test")
	 * @param Notifier $notifier
	 * @return Response
	 */
	public function notifierTest(Notifier $notifier): Response
	{
		try {
			$notifier->notify(Notification::TYPE_EMAIL);
		} catch (\Exception $e) {
			return new JsonResponse($e->getMessage());
		}

		return new Response(
			'<html><body>Hello Symfony4(fetch)</body></html>'
		);
	}

	/**
	 * @Route("/lucky/number")
	 * @return Response
	 */
	public function number(): Response
	{
		$number = mt_rand(0, 100);

		return new Response(
			'<html><body>Lucky number: ' . $number . '</body></html>'
		);
	}

	/**
	 * @Route("/user")
	 * @Security("has_role('ROLE_USER')")
	 * @return Response
	 */
	public function user(): Response
	{
		return new Response('<html><body>User page!</body></html>');
	}


	/**
	 * @Route("/admin")
	 * @Security("has_role('ROLE_ADMIN')")
	 * @return Response
	 */
	public function admin(): Response
	{
		return new Response('<html><body>Admin page!</body></html>');
	}

	/**
	 * @Route("/super")
	 * @Security("has_role('ROLE_SUPER_ADMIN')")
	 * @return Response
	 */
	public function super(): Response
	{
		return new Response('<html><body>Super Admin page!</body></html>');
	}
}