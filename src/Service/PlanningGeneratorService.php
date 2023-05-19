<?php

// src/Service/PlanningGeneratorService.php
namespace App\Service;

use App\Repository\GeneralConstraintsRepository;
use App\Repository\RoomRepository;
use App\Repository\UERepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PlanningGeneratorService {
    private HttpClientInterface $client;
    private ParameterBagInterface $parameterBag;
    private Security $security;
    private UERepository $ueRepository;
    private RoomRepository $roomRepository;
    private GeneralConstraintsRepository $generalConstraintsRepository;

    public function __construct(
        ParameterBagInterface $parameterBag,
        Security $security, 
        HttpClientInterface $httpClientInterface,
        RoomRepository $roomRepository,
        UERepository $ueRepository,
        GeneralConstraintsRepository $generalConstraintsRepository,
    ) {
        $this->parameterBag = $parameterBag;
        $this->security = $security;
        $this->client = $httpClientInterface;
        $this->roomRepository = $roomRepository;
        $this->ueRepository = $ueRepository;
        $this->generalConstraintsRepository = $generalConstraintsRepository;
    }

    public function generate() {
        $apiUrl = $this->parameterBag->get('CONSTRAINT_API_URL');
        
    }
}