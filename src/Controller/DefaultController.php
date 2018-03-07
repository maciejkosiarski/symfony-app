<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class DefaultController
 * @package App\Controller
 * @author  Maciej Kosiarski <maciek.kosiarski@gmail.com>
 */
class DefaultController extends Controller {

	/**
	 * @Route("/")
	 * @return Response
	 */
	public function index(): Response
	{
		return new Response(
			'<html><body>Hello Symfony4</body></html>'
		);
	}

	/**
	 * @Route("/lucky/number")
	 */
	public function number()
	{
		$number = mt_rand(0, 100);

		return new Response(
			'<html><body>Lucky number: ' . $number . '</body></html>'
		);
	}

	/**
	 * @Route("/user")
	 * @Security("has_role('ROLE_USER')")
	 */
	public function user()
	{
		return new Response('<html><body>User page!</body></html>');
	}


	/**
	 * @Route("/admin")
	 * @Security("has_role('ROLE_ADMIN')")
	 */
	public function admin()
	{
		return new Response('<html><body>Admin page!</body></html>');
	}

	/**
	 * @Route("/super")
	 * @Security("has_role('ROLE_SUPER_ADMIN')")
	 */
	public function super()
	{
		return new Response('<html><body>Super Admin page!</body></html>');
	}
}