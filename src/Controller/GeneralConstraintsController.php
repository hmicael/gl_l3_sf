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
        return $this->render('general_constraints/show.html.twig', [
            'general_constraints' => $generalConstraintsRepository->find(1)
        ]);
    }

    #[Route('/edit', name: 'app_general_constraints_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, GeneralConstraintsRepository $generalConstraintsRepository): Response
    {
        $generalConstraint = $generalConstraintsRepository->find(1);
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
}
