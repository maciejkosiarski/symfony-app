<?php

namespace App\Controller;

use App\Entity\CompanySource;
use App\Form\CompanySourceType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/company/source")
 */
class CompanySourceController extends Controller
{
    /**
     * @Route("/", name="company_source_index", methods="GET")
     */
    public function index(): Response
    {
        $companySources = $this->getDoctrine()
            ->getRepository(CompanySource::class)
            ->findAll();

        return $this->render('company_source/index.html.twig', ['company_sources' => $companySources]);
    }

    /**
     * @Route("/new", name="company_source_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $companySource = new CompanySource();
        $form = $this->createForm(CompanySourceType::class, $companySource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($companySource);
            $em->flush();

            return $this->redirectToRoute('company_source_index');
        }

        return $this->render('company_source/new.html.twig', [
            'company_source' => $companySource,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="company_source_show", methods="GET")
     */
    public function show(CompanySource $companySource): Response
    {
        return $this->render('company_source/show.html.twig', ['company_source' => $companySource]);
    }

    /**
     * @Route("/{id}/edit", name="company_source_edit", methods="GET|POST")
     */
    public function edit(Request $request, CompanySource $companySource): Response
    {
        $form = $this->createForm(CompanySourceType::class, $companySource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('company_source_edit', ['id' => $companySource->getId()]);
        }

        return $this->render('company_source/edit.html.twig', [
            'company_source' => $companySource,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="company_source_delete", methods="DELETE")
     */
    public function delete(Request $request, CompanySource $companySource): Response
    {
        if (!$this->isCsrfTokenValid('delete'.$companySource->getId(), $request->request->get('_token'))) {
            return $this->redirectToRoute('company_source_index');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($companySource);
        $em->flush();

        return $this->redirectToRoute('company_source_index');
    }
}
