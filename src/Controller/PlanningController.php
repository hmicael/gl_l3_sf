<?php

namespace App\Controller;

use App\Service\FiliereGetterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlanningController extends AbstractController
{
    #[Route('/planning', name: 'app_planning')]
    public function index(FiliereGetterService $FiliereGetterService): Response
    {
        $location = $this->getParameter('kernel.project_dir') . '/public/planning';
        $fileList = scandir($location);
        // Remove . and .. from the list
        $files = array_diff($fileList, ['.', '..']);
        $plannings = [];
        foreach ($files as $file) {
            // split the file name to get the description
            $filename = $file;
            $file = explode('_', $file);
            $cn = $file[0];
            $semestre = explode('.', $file[1])[0] == 's1' ? '1' : '2';
            $plannings[] = [
                'cn' => $cn,
                'semester' => $semestre,
                'description' => $FiliereGetterService->getDescription($cn),
                'filename' => $filename,
            ];
        }

        return $this->render('planning/index.html.twig', [
            'controller_name' => 'Planning Index',
            'plannings' => $plannings,
        ]);
    }

    #[Route('/planning/{file}', name: 'app_planning_show')]
    public function get($file) {
        // Read the file
        $location = $this->getParameter('kernel.project_dir') . '/public/planning/' . $file;
        // if the file doesn't exist, return a 404
        if (!file_exists($location)) {
            throw $this->createNotFoundException('The file does not exist');
        }
        $file = file_get_contents($location);
        // return the file
        return new Response($file, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'inline; filename="' . $file . '"'
        ]);
        
    }
    public function generatePlanning(): Response
    {
        return $this->render('planning/index.html.twig', [
            'controller_name' => 'PlanningController',
        ]);
    }
}
