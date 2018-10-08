<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\CompanyWatcher;
use App\Form\CompanyType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/company")
 */
class CompanyController extends Controller
{
    /**
     * @Route("/", name="company_index", methods="GET")
     */
    public function index(): Response
    {
        $companies = $this->getDoctrine()
            ->getRepository(Company::class)
            ->findAll();

        return $this->render('company/index.html.twig', ['companies' => $companies]);
    }

    /**
     * @Route("/new", name="company_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $company = new Company();
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($company);
            $em->flush();

            return $this->redirectToRoute('company_index');
        }

        return $this->render('company/new.html.twig', [
            'company' => $company,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="company_show", methods="GET")
     */
    public function show(Company $company): Response
    {
        return $this->render('company/show.html.twig', ['company' => $company]);
    }

    /**
     * @Route("/{id}/edit", name="company_edit", methods="GET|POST")
     */
    public function edit(Request $request, Company $company): Response
    {
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('company_edit', ['id' => $company->getId()]);
        }

        return $this->render('company/edit.html.twig', [
            'company' => $company,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="company_delete", methods="DELETE")
     */
    public function delete(Request $request, Company $company): Response
    {
        if (!$this->isCsrfTokenValid('delete'.$company->getId(), $request->request->get('_token'))) {
            return $this->redirectToRoute('company_index');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($company);
        $em->flush();

        return $this->redirectToRoute('company_index');
    }

    /**
     * @Route("/{id}/watcher", name="company_watcher", methods="GET")
     */
    public function watcher(Company $company)
    {
        $watcher = new CompanyWatcher();
        $watcher->setCompany($company);
        $watcher->setUser($this->getUser());

        $em = $this->getDoctrine()->getManager();
        $em->persist($watcher);
        $em->flush();

        return $this->redirectToRoute('company_index');
    }
}
