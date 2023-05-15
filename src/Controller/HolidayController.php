<?php

namespace App\Controller;

use App\Entity\Holiday;
use App\Form\HolidayType;
use App\Repository\HolidayRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/holiday')]
class HolidayController extends AbstractController
{
    #[Route('/', name: 'app_holiday_index', methods: ['GET'])]
    public function index(HolidayRepository $holidayRepository): Response
    {
        return $this->render('holiday/index.html.twig', [
            'holidays' => $holidayRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_holiday_new_old', methods: ['GET', 'POST'])]
    public function new(Request $request, HolidayRepository $holidayRepository): Response
    {
        $holiday = new Holiday();
        $form = $this->createForm(HolidayType::class, $holiday);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $holidayRepository->save($holiday, true);

            return $this->redirectToRoute('app_holiday_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('holiday/new.html.twig', [
            'holiday' => $holiday,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_holiday_show', methods: ['GET'])]
    public function show(Holiday $holiday): Response
    {
        return $this->render('holiday/show.html.twig', [
            'holiday' => $holiday,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_holiday_edit_old', methods: ['GET', 'POST'])]
    public function edit(Request $request, Holiday $holiday, HolidayRepository $holidayRepository): Response
    {
        $form = $this->createForm(HolidayType::class, $holiday);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $holidayRepository->save($holiday, true);

            return $this->redirectToRoute('app_holiday_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('holiday/edit.html.twig', [
            'holiday' => $holiday,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_holiday_delete', methods: ['POST'])]
    public function delete(Request $request, Holiday $holiday, HolidayRepository $holidayRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$holiday->getId(), $request->request->get('_token'))) {
            $holidayRepository->remove($holiday, true);
        }

        return $this->redirectToRoute('app_holiday_index', [], Response::HTTP_SEE_OTHER);
    }
}
