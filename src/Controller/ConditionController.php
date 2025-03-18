<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ConditionController extends AbstractController
{
    #[Route('/cgv', name: 'app_condition')]
    public function index(): Response
    {
        return $this->render('condition/index.html.twig', [
            'controller_name' => 'ConditionController',
        ]);
    }
}
