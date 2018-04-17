<?php


namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

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
	 * @return Response
	 */
	public function login(Request $request, AuthenticationUtils $authUtils): Response
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

	/**
	 * @Route("/register", name="user_registration")
	 * @Security("has_role('ROLE_SUPER_ADMIN')")
	 *
	 * @param Request                      $request
	 * @param UserPasswordEncoderInterface $passwordEncoder
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
	 * @throws \App\Exception\InvalidUserRoleException
	 * @throws \ReflectionException
	 */
	public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
	{
		// 1) build the form
		$user = new User();
		$form = $this->createForm(UserType::class, $user);

		// 2) handle the submit (will only happen on POST)
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {

			// 3) Encode the password (you could also do this via Doctrine listener)
			$password = $passwordEncoder->encodePassword($user, $user->getPassword());
			$user->setPassword($password);

			// 4) save the User!
			$entityManager = $this->getDoctrine()->getManager();

			$entityManager->persist(new Role($user, Role::ROLE_USER));
			$entityManager->persist($user);
			$entityManager->flush();

			// ... do any other work - like sending them an email, etc
			// maybe set a "flash" success message for the user

			return $this->redirectToRoute('app_default_super');
		}

		return $this->render(
			'security/register.html.twig',
			['form' => $form->createView()]
		);
	}
}