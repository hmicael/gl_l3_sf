<?php

// src/Service/PlanningGeneratorService.php
namespace App\Service;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PlanningGeneratorService {
    private HttpClientInterface $client;
    private ParameterBagInterface $parameterBag;
    private Security $security;

    public function __construct(ParameterBagInterface $parameterBag, Security $security, HttpClientInterface $httpClientInterface)
    {
        $this->parameterBag = $parameterBag;
        $this->security = $security;
        $this->client = $httpClientInterface;
    }

    public function test() {
        return "success";
    }
}