<?php

namespace App\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\Required;

class ConditionController extends AbstractController
{
    #[Route('/cgv', name: 'app_condition')]
    public function index(Request $request, EntityManagerInterface $Em, UserPasswordHasher $userPasswordHasher): Response
    {
        $User = $this->getUser();
        return $this->render('condition/index.html.twig', [
            'controller_name' => 'ConditionController',
        ]);
    }
}
