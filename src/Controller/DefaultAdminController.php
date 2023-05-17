<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class DefaultAdminController extends AbstractController
{
    #[Route('/admin', name: 'app_default_admin')]
    public function index(): Response
    {
        return $this->render('default_admin/index.html.twig', [
            'controller_name' => 'DefaultAdminController',
        ]);
    }
}
