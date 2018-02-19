<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SecurityController
 * @package App\Controller
 * @author  Maciej Kosiarski <maciej.kosiarski@gmail.com>
 */
class SecurityController extends Controller
{

	/**
	 * @Route("/login", name="login")
	 *
	 * @param Request             $request
	 * @param AuthenticationUtils $authUtils
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function login(Request $request, AuthenticationUtils $authUtils)
	{
		// get the login error if there is one
		$error = $authUtils->getLastAuthenticationError();

		// last username entered by the user
		$lastUsername = $authUtils->getLastUsername();

		return $this->render('security/login.html.twig', array(
			'last_username' => $lastUsername,
			'error'         => $error,
		));
	}
}