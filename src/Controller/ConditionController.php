<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ConditionController extends AbstractController
{
    #[Route('/cvg', name: 'app_cvg')]
    public function index(): Response
    {
        return $this->render('condition/index.html.twig', [
            'controller_name' => 'ConditionController',
        ]);
    }
}



<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConditionController extends AbstractController
{
    #[Route('/cgv', name: 'app_cgv')]
    public function index(): Response
    {
        return $this->render('condition/index.html.twig', [
            'controller_name' => 'ConditionController',
        ]);
    }
}
