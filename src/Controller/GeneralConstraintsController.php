<?php

namespace App\Controller;

use App\Entity\GeneralConstraints;
use App\Entity\Holiday;
use App\Form\GeneralConstraintsType;
use App\Form\HolidayType;
use App\Repository\GeneralConstraintsRepository;
use App\Repository\HolidayRepository;
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

    #[Route('/new-holiday', name: 'app_holiday_new', methods: ['GET', 'POST'])]
    public function newHoliday(Request $request, HolidayRepository $holidayRepository, GeneralConstraintsRepository $generalConstraintsRepository): Response
    {
        $generalConstraint = $generalConstraintsRepository->find(1);
        $holiday = new Holiday();
        $form = $this->createForm(HolidayType::class, $holiday);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $holiday->setGeneralConstraints($generalConstraint);
            if (! $holiday->getEnd()) {
                $holiday->setEnd($holiday->getBeginning());
            }
            $holidayRepository->save($holiday, true);

            return $this->redirectToRoute('app_general_constraints_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('holiday/new.html.twig', [
            'holiday' => $holiday,
            'form' => $form,
        ]);
    }

    #[Route('/holiday/{id}/edit', name: 'app_holiday_edit', methods: ['GET', 'POST'])]
    public function editHoliday(Request $request, Holiday $holiday, HolidayRepository $holidayRepository, GeneralConstraintsRepository $generalConstraintsRepository): Response
    {
        $generalConstraint = $generalConstraintsRepository->find(1);
        $form = $this->createForm(HolidayType::class, $holiday);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $holiday->setGeneralConstraints($generalConstraint);
            if (! $holiday->getEnd()) {
                $holiday->setEnd($holiday->getBeginning());
            }
            $holidayRepository->save($holiday, true);

            return $this->redirectToRoute('app_general_constraints_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('holiday/edit.html.twig', [
            'holiday' => $holiday,
            'form' => $form,
        ]);
    }

    #[Route('/holiday/{id}', name: 'app_holiday_show', methods: ['GET'])]
    public function show(Holiday $holiday): Response
    {
        return $this->render('holiday/show.html.twig', [
            'holiday' => $holiday,
        ]);
    }

    #[Route('/holiday/{id}/delete', name: 'app_holiday_delete', methods: ['POST'])]
    public function delete(Request $request, Holiday $holiday, HolidayRepository $holidayRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$holiday->getId(), $request->request->get('_token'))) {
            $holidayRepository->remove($holiday, true);
        }

        return $this->redirectToRoute('app_general_constraints_index', [], Response::HTTP_SEE_OTHER);
    }
}
