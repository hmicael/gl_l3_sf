<?php

// src/Service/PlanningGeneratorService.php
namespace App\Service;

use App\Entity\Cours;
use App\Entity\Planning;
use App\Entity\Room;
use App\Entity\Slot;
use App\Repository\GeneralConstraintsRepository;
use App\Repository\RoomRepository;
use App\Repository\CoursRepository;
use App\Repository\UERepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\SerializerInterface;

class PlanningGeneratorService {
    private HttpClientInterface $client;
    private ParameterBagInterface $parameterBag;
    private Security $security;
    private UERepository $ueRepository;
    private CoursRepository $coursRepository;
    private RoomRepository $roomRepository;
    private GeneralConstraintsRepository $generalConstraintsRepository;
    private Filesystem $filesystem;
    private SerializerInterface $serializer;

    public function __construct(
        ParameterBagInterface $parameterBag,
        Security $security, 
        HttpClientInterface $httpClientInterface,
        RoomRepository $roomRepository,
        UERepository $ueRepository,
        CoursRepository $coursRepository,
        GeneralConstraintsRepository $generalConstraintsRepository,
        Filesystem $filesystem,
        SerializerInterface $serializer
    ) {
        $this->parameterBag = $parameterBag;
        $this->security = $security;
        $this->client = $httpClientInterface;
        $this->roomRepository = $roomRepository;
        $this->ueRepository = $ueRepository;
        $this->coursRepository = $coursRepository;
        $this->generalConstraintsRepository = $generalConstraintsRepository;
        $this->filesystem = $filesystem;
        $this->serializer = $serializer;
    }

    public function loadPlanning($filename, $semester) {
        $generalConstraints = $this->generalConstraintsRepository->find(1);
        $startHour = $generalConstraints->getStartHour();
        $endHour = $generalConstraints->getEndHour();
        $interval = new \DateInterval('PT15M'); // 15 minutes interval
        $timePeriod = new \DatePeriod($startHour, $interval, $endHour);
        $startDate = null;
        $endDate = null;

        if ($semester == 1) {
            $startDate = $generalConstraints->getSemesterOneStart();
            $endDate = $generalConstraints->getSemesterOneEnd();
        } else {
            $startDate = $generalConstraints->getSemesterTwoStart();
            $endDate = $generalConstraints->getSemesterTwoEnd();
        }


        $apiUrl = $this->parameterBag->get('CONSTRAINT_API_URL');
        // TODO: faire la requete vers l'API avec les bons parametres

        $interval = new \DateInterval('P1D'); // 1 day interval
        $datePeriod = new \DatePeriod($startDate, $interval, $endDate);
        $mondays = [];
        $plannings = [];

        foreach ($datePeriod as $date) {
            if ($date->format('N') === '1') { // Check if it's Monday (1 is Monday)
                $mondays[] = $date;
            }
        }

        foreach ($mondays as $monday) {
            $slots = [];
        
            $newDates = [];
            for ($i = 0; $i < 5; $i++) {
                $newDate = clone $monday;
                $newDate->add(new \DateInterval('P'.$i.'D'));
                $newDates[] = $newDate;
            }
        
            foreach ($timePeriod as $time) {
                $endTime = clone $time;
                $endTime->add(new \DateInterval('PT15M'));
        
                foreach ($newDates as $newDate) {
                    $slot = new Slot();
                    $slot->setStartHour($time->format('H:i'));
                    $slot->setEndHour($endTime->format('H:i'));
                    $cours = $this->coursRepository->find(4);
                    $cours->getUE()->cleanCours();
                    $slot->setCours($cours);
                    $room = $this->roomRepository->find(1);
                    $slot->setRoom($room);
                    $slot->setDate($newDate->format('d-m-Y'));
                    $slots[$time->format('H:i')][] = $slot;
                }
            }
            $plannings[] = [
                'slots' => $slots,
                'week' => $monday,
            ];
        }

        $this->filesystem->touch($filename);
        $this->filesystem->dumpFile(
            $filename,
            $this->serializer->serialize($plannings, 'json', ['json_encode_options' => JSON_PRETTY_PRINT])
        );

        return $plannings;
    }
}
