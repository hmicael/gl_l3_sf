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
use App\Repository\HolidayRepository;
use App\Repository\UERepository;
use DateTime;
use DateTimeImmutable;
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
    private HolidayRepository $holidayRepository;
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
        HolidayRepository $holidayRepository,
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
        $this->holidayRepository = $holidayRepository;
        $this->generalConstraintsRepository = $generalConstraintsRepository;
        $this->filesystem = $filesystem;
        $this->serializer = $serializer;
    }

    public function loadPlanning($filename, $semester) {
        $data = [];
        $generalConstraints = $this->generalConstraintsRepository->find(1);
        $startHour = $generalConstraints->getStartHour();
        $endHour = $generalConstraints->getEndHour();
        $data['day_interval'] = [
            'day_start_hour' => $startHour->format('H:i'),
            'day_end_hour' => $endHour->format('H:i')
        ];
        $startDate = null;
        $endDate = null;
        if ($semester == 1) {
            $startDate = $generalConstraints->getSemesterOneStart();
            $endDate = $generalConstraints->getSemesterOneEnd();
        } else {
            $startDate = $generalConstraints->getSemesterTwoStart();
            $endDate = $generalConstraints->getSemesterTwoEnd();
        }
        $data['school_year_interval'] = [
            'year_start_date' => $startDate->format('d/m/Y'),
            'year_end_date' => $endDate->format('d/m/Y')
        ];
        $data['lunch'] = [
            'lunch_start_hour' => $generalConstraints->getBreakStartHour()->format('H:i'),
            'lunch_end_hour' => $generalConstraints->getBreakEndHour()->format('H:i')
        ];
        foreach ($this->roomRepository->findAll() as $room) {
            $data['classrooms'][] = [
                'id' => $room->getId(),
                'capacity' => $room->getCapacity(),
                'session_types' => [$room->getType()],
                'availabilities' => [
                    'start_time' => $startDate->format('d/m/Y'),
                    'end_time' => $endDate->format('d/m/Y')
                ]
            ];
        }
        foreach($this->holidayRepository->getExams($semester) as $exam) {
            $data['tests_dates'][] = [
                'session_type' => 'exam',
                'start_date_time' => $exam->getBeginning()->format('d/m/Y'),
                'end_date_time' => $exam->getEnd()->format('d/m/Y')
            ];
        }
        foreach($this->ueRepository->findBy(['semester' => $semester]) as $ue) {
            $nbGroup = $ue->getNbGroup();
            $nbStudent = $ue->getNbStudent();
            $nStudentPerGroup = ceil($nbStudent / $nbGroup);
            $groups = [];
            for ($i=1; $i <= $nbGroup; $i++) { 
                $groups[] = [
                    'group_id' => $i,
                    'size' => $nStudentPerGroup
                ];
            }
            $data['courses'][] = [
                'id' => $ue->getId(),
                'course_start' => $ue->getStartDate()->format('d/m/Y'),
                'studentsPerGroup' => $groups
            ];
            foreach ($ue->getCours() as $cours) {
                $timeString = $cours->getDuration()->format('H:i');
                list($hours, $minutes) = explode(':', $timeString); // Split the time string into hours and minutes
                $totalMinutes = (int)$hours * 60 + (int)$minutes;
                $data['order_position'][] = [
                    'order_position' => $cours->getPosition(),
                    'session_type' => $cours->getType(),
                    'course_id' => $ue->getId(),
                    'duration' => $totalMinutes,
                ];
            }
        }

        $apiUrl = $this->parameterBag->get('CONSTRAINT_API_URL');
        // TODO: faire la requete vers l'API avec les bons parametres

        $interval = new \DateInterval('P1D'); // 1 day interval
        $datePeriod = new \DatePeriod($startDate, $interval, $endDate);
        $responses = [];

        foreach ($datePeriod as $date) {
            if ($date->format('N') === '6' || $date->format('N') === '7') {
                continue;
            }
            $responses[$date->format('d/m/Y')] = [
                [
                    'start_time' => DateTimeImmutable::createFromFormat('H:i', '08:00'),
                    'end_time' => DateTimeImmutable::createFromFormat('H:i', '09:30'),
                    'room_id' => 1,
                    'course_id' => 5,
                ],
                [
                    'start_time' => DateTimeImmutable::createFromFormat('H:i', '10:00'),
                    'end_time' => DateTimeImmutable::createFromFormat('H:i', '11:30'),
                    'room_id' => 1,
                    'course_id' => 6,
                ],
                [
                    'start_time' => DateTimeImmutable::createFromFormat('H:i', '14:00'),
                    'end_time' => DateTimeImmutable::createFromFormat('H:i', '15:30'),
                    'room_id' => 1,
                    'course_id' => 7,
                ],
                [
                    'start_time' => DateTimeImmutable::createFromFormat('H:i', '16:00'),
                    'end_time' => DateTimeImmutable::createFromFormat('H:i', '17:30'),
                    'room_id' => 1,
                    'course_id' => 8,
                ]
            ];
        }

        $plannings = [];
        foreach($responses as $date => $response) {
            foreach($response as $r) {
                $slot = new Slot();
                $slot->setStartHour($r['start_time']->format('H:i'));
                $slot->setEndHour($r['end_time']->format('H:i'));
                $cours = $this->coursRepository->find($r['course_id']);
                $cours->getUE()->cleanCours();
                $slot->setCours($cours);
                $room = $this->roomRepository->find($r['room_id']);
                $slot->setRoom($room);
                $slot->setDate($date);
                $plannings[$date][] = $slot;
            }
        }

        $plannings_1 = [];
        // Loop through the plannings
        foreach ($plannings as $date => $data) {
            $date = DateTime::createFromFormat('d/m/Y', $date);
            $weekNumber = $date->format('W');

            // Check if the week number exists in the array
            if (!isset($plannings_1[$weekNumber])) {
                $plannings_1[$weekNumber] = [];
            }

            // Add the data to the week number
            $plannings_1[$weekNumber][$date->format('d/m/Y')] = $data;
        }
        
        $this->filesystem->touch($filename);
        $this->filesystem->dumpFile(
            $filename,
            $this->serializer->serialize($plannings_1, 'json', ['json_encode_options' => JSON_PRETTY_PRINT])
        );

        return $plannings_1;
    }
}
