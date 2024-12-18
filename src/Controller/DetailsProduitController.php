<?php

namespace App\Controller;

use App\Repository\ProduitsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DetailsProduitController extends AbstractController
{
    #[Route('/details/produit/{id}', name: 'app_details_produit')]
    public function index($id, ProduitsRepository $produitsRepository): Response
    {
        $produit = $produitsRepository->find($id);
        return $this->render('details_produit/index.html.twig', [
            'controller_name' => 'DetailsProduitController',
            'produit' => $produit, 
        ]);
    }
}
