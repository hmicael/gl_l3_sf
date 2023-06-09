<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Entity\UE;
use App\Form\CoursType;
use App\Form\UEType;
use App\Repository\CoursRepository;
use App\Repository\UERepository;
use App\Service\PlanningGeneratorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Service\FiliereGetterService;

#[Route('/ue')]
class UEController extends AbstractController
{
    #[Route('/', name: 'app_ue_index', methods: ['GET'])]
    public function index(UERepository $uERepository, FiliereGetterService $FiliereGetterService): Response
    {
        $ues = $uERepository->findAll();
        foreach ($ues as $ue) {
            $filieres = $FiliereGetterService->getGroups($ue->getFilieres());
            $filieres = array_column($filieres, 'description');
            $ue->setFilieres($filieres);
        }
        return $this->render('ue/index.html.twig', [
            'ues' => $ues,
        ]);
    }

    #[Route('/new', name: 'app_ue_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UERepository $uERepository): Response
    {
        $uE = new UE();
        $form = $this->createForm(UEType::class, $uE);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uE->setTeacher($this->getUser());
            $uERepository->save($uE, true);

            return $this->redirectToRoute('app_ue_show', ['id' => $uE->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ue/new.html.twig', [
            'ue' => $uE,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ue_show', methods: ['GET'])]
    public function show(UE $uE, FiliereGetterService $FiliereGetterService): Response
    {
        $filieres = $FiliereGetterService->getGroups($uE->getFilieres());
        $filieres = array_column($filieres, 'description');
        $uE->setFilieres($filieres);
        return $this->render('ue/show.html.twig', [
            'ue' => $uE,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_ue_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UE $uE, UERepository $uERepository): Response
    {
        $form = $this->createForm(UEType::class, $uE);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uE->setConstraintsApplied(false);
            $uERepository->save($uE, true);

            return $this->redirectToRoute('app_ue_show', ['id' => $uE->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ue/edit.html.twig', [
            'ue' => $uE,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ue_delete', methods: ['POST'])]
    public function delete(Request $request, UE $uE, UERepository $uERepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$uE->getId(), $request->request->get('_token'))) {
            $uERepository->remove($uE, true);
        }

        return $this->redirectToRoute('app_ue_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/cours/new', name: 'app_cours_new', methods: ['GET', 'POST'])]
    public function newCours(Request $request, UE $uE, CoursRepository $coursRepository, UERepository $uERepository): Response
    {
        $cour = new Cours();
        $cour->setUe($uE);
        $form = $this->createForm(CoursType::class, $cour, ['data' => $cour]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($uE->addCour($cour)) {
                $uE->setConstraintsApplied(false);
                $cour->setSemester($uE->getSemester());
                $coursRepository->save($cour, true);
                $uERepository->save($uE, true);

                return $this->redirectToRoute('app_ue_show', ['id' => $uE->getId()], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash(
                    'notice',
                    'You can\'t add any more ' . $cour->getType() . ' to this UE'
                );
            }
        }

        return $this->renderForm('cours/new.html.twig', [
            'cour' => $cour,
            'form' => $form,
            'ue' => $uE,
        ]);
    }

    #[Route('/{id}/cours/{courId}', name: 'app_cours_show', methods: ['GET'])]
    #[ParamConverter('cour', class: Cours::class, options: ['id' => 'courId'])]
    public function showCours(UE $uE ,Cours $cour): Response
    {
        return $this->render('cours/show.html.twig', [
            'cour' => $cour,
            'ue' => $uE,
        ]);
    }

    #[Route('/{id}/cours/{courId}/edit', name: 'app_cours_edit', methods: ['GET', 'POST'])]
    #[ParamConverter('cour', class: Cours::class, options: ['id' => 'courId'])]
    public function editCours(Request $request, Ue $uE, Cours $cour, CoursRepository $coursRepository, UERepository $uERepository): Response
    {
        $form = $this->createForm(CoursType::class, $cour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uE->setConstraintsApplied(false);
            $coursRepository->save($cour, true);
            $uERepository->save($uE, true);

            return $this->redirectToRoute('app_ue_show', ['id' => $uE->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('cours/edit.html.twig', [
            'cour' => $cour,
            'form' => $form,
            'ue' => $uE,
        ]);
    }

    #[Route('/{id}/cours/{courId}/delete', name: 'app_cours_delete', methods: ['POST'])]
    #[ParamConverter('cour', class: Cours::class, options: ['id' => 'courId'])]
    
    public function deleteCours(Request $request, UE $uE, Cours $cour, CoursRepository $coursRepository, UERepository $uERepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cour->getId(), $request->request->get('_token'))) {
            $uE->setConstraintsApplied(false);
            $coursRepository->remove($cour, true);
            $uERepository->save($uE, true);
        }

        return $this->redirectToRoute('app_ue_show', ['id' => $uE->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/apply-constraint', name: 'app_ue_apply_constraint', methods: ['POST'])]
    public function applyConstraint(
        Request $request,
        UE $uE, UERepository
        $uERepository,
        PlanningGeneratorService $planningGeneratorService
    ): Response
    {
        if ($this->isCsrfTokenValid('apply_constraint'.$uE->getId(), $request->request->get('_token'))) {
            $uE->setConstraintsApplied(true);
            $uERepository->save($uE, true);
        }
        
        foreach($uE->getFilieres() as $filiere) {
            $filename = $this->getParameter('kernel.project_dir') . '/public/planning/' . $filiere . '_s'. $uE->getSemester() . '.json';
            $planningGeneratorService->loadPlanning($filename, $uE->getSemester());
        }

        return $this->redirectToRoute('app_ue_index', [], Response::HTTP_SEE_OTHER);
    }
}
