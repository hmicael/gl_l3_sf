<?php

namespace App\Controller;

use App\Entity\GeneralConstraints;
use App\Form\GeneralConstraintsType;
use App\Repository\GeneralConstraintsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/general-constraints')]
class GeneralConstraintsController extends AbstractController
{
    #[Route('/', name: 'app_general_constraints_index', methods: ['GET'])]
    public function index(GeneralConstraintsRepository $generalConstraintsRepository): Response
    {
        return $this->render('general_constraints/index.html.twig', [
            'general_constraints' => $generalConstraintsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_general_constraints_new', methods: ['GET', 'POST'])]
    public function new(Request $request, GeneralConstraintsRepository $generalConstraintsRepository): Response
    {
        $generalConstraint = new GeneralConstraints();
        $form = $this->createForm(GeneralConstraintsType::class, $generalConstraint);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $generalConstraintsRepository->save($generalConstraint, true);

            return $this->redirectToRoute('app_general_constraints_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('general_constraints/new.html.twig', [
            'general_constraint' => $generalConstraint,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_general_constraints_show', methods: ['GET'])]
    public function show(GeneralConstraints $generalConstraint): Response
    {
        return $this->render('general_constraints/show.html.twig', [
            'general_constraint' => $generalConstraint,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_general_constraints_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, GeneralConstraints $generalConstraint, GeneralConstraintsRepository $generalConstraintsRepository): Response
    {
        $form = $this->createForm(GeneralConstraintsType::class, $generalConstraint);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $generalConstraintsRepository->save($generalConstraint, true);

            return $this->redirectToRoute('app_general_constraints_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('general_constraints/edit.html.twig', [
            'general_constraint' => $generalConstraint,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_general_constraints_delete', methods: ['POST'])]
    public function delete(Request $request, GeneralConstraints $generalConstraint, GeneralConstraintsRepository $generalConstraintsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$generalConstraint->getId(), $request->request->get('_token'))) {
            $generalConstraintsRepository->remove($generalConstraint, true);
        }

        return $this->redirectToRoute('app_general_constraints_index', [], Response::HTTP_SEE_OTHER);
    }
}
