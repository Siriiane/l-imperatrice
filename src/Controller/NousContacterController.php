<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class NousContacterController extends AbstractController
{
    #[Route('/contact', name: 'app_nous_contacter', methods: ['GET', 'POST'])]
public function index(Request $request): Response
{
    if ($request->isMethod('POST')) {
        // Récupération des données du formulaire
        $name = $request->request->get('name');
        $email = $request->request->get('email');
        $message = $request->request->get('message');

        // Logique de traitement des données (ex. envoi d'email ou stockage)
        // Exemple simple de retour :
        $this->addFlash('success', 'Merci pour votre message, ' . htmlspecialchars($name) . '. Nous vous répondrons rapidement.');
    }

    return $this->render('nous_contacter/index.html.twig', [
        'controller_name' => 'NousContacterController',
    ]);
  
} 
 }