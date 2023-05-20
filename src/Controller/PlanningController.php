<?php

namespace App\Controller;

use App\Repository\GeneralConstraintsRepository;
use App\Service\FiliereGetterService;
use App\Service\PlanningGeneratorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class PlanningController extends AbstractController
{
    #[Route('/planning', name: 'app_planning')]
    public function index(FiliereGetterService $FiliereGetterService): Response
    {
        $location = $this->getParameter('kernel.project_dir') . '/public/planning';
        $fileList = scandir($location);
        // Remove . and .. from the list
        $files = array_diff($fileList, ['.', '..']);
        $plannings = []; // list of plannings to display from folders /public/planning
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
                'filename' => explode('.', $filename)[0],
            ];
        }

        $filieres = $FiliereGetterService->getGroups();
        // here, we check if there is a planning for each filiere of the current user
        foreach ($filieres as $filiere) {
            $filename_s1 = $filiere['cn'] . '_s1.json';
            $filename_s2 = $filiere['cn'] . '_s2.json';
            if (!in_array($filename_s1, $files)) {
                $plannings[] = [
                    'cn' => $filiere['cn'],
                    'semester' => '1',
                    'description' => $filiere['description'],
                    'filename' => explode('.', $filename_s1)[0],
                ];
            }
            if (!in_array($filename_s2, $files)) {
                $plannings[] = [
                    'cn' => $filiere['cn'],
                    'semester' => '2',
                    'description' => $filiere['description'],
                    'filename' => explode('.', $filename_s2)[0],
                ];
            }
        }
        // TODO: si eleve et prof limiter le nombre de plannings, faire une boucle pour checker les CN

        return $this->render('planning/index.html.twig', [
            'controller_name' => 'Planning Index',
            'plannings' => $plannings,
        ]);
    }

    #[Route('/planning/{filiere}/{file}', name: 'app_planning_show')]
    public function get(
        $filiere,
        $file,
        PlanningGeneratorService $planningGeneratorService,
        GeneralConstraintsRepository $generalConstraintsRepository,
        SerializerInterface $serializer
        ): Response {
        // TODO: vérifier si l'utilisateur a le droit de voir ce planning

        // Read the file
        $filename = $this->getParameter('kernel.project_dir') . '/public/planning/' . $file . '.json';
        $semester = explode('_', $file)[1] == 's1' ? '1' : '2';
        $plannings = [];

        // Get the timeslots
        $generalConstraints = $generalConstraintsRepository->find(1);
        $startHour = $generalConstraints->getStartHour();
        $endHour = $generalConstraints->getEndHour();
        $interval = new \DateInterval('PT15M'); // 15 minutes interval
        $timePeriod = new \DatePeriod($startHour, $interval, $endHour);
        $times = [];
        foreach ($timePeriod as $time) {
            $times[] = $time;
        }

        // if the file doesn't exist, we generate it
        if (! file_exists($filename)) {
            $plannings = $planningGeneratorService->loadPlanning($filename, $semester);
        } else {
            $plannings = json_decode(file_get_contents($filename), true);
        }
        
        return $this->render('planning/show.html.twig', [
            'controller_name' => 'Planning Show',
            'plannings' => $plannings,
            'timeslots' => $times,
            'filiere' => $filiere,
        ]);
    }
    public function generatePlanning(): Response
    {
        return $this->render('planning/index.html.twig', [
            'controller_name' => 'PlanningController',
        ]);
    }
}
