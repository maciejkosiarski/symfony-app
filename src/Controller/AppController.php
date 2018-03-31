<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
/**
 * Class DefaultController
 * @package App\Controller
 * @author  Maciej Kosiarski <maciek.kosiarski@gmail.com>
 */
class AppController extends Controller {

	/**
	 * @Route("/")
	 * @return Response
	 */
	public function index(): Response
	{
		return new Response(
			'<html><body>Index page</body></html>'
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