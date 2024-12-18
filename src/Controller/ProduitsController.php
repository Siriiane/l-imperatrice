<?php

namespace App\Controller;

use App\Repository\ProduitsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class ProduitsController extends AbstractController
{
    #[Route('/produits', name: 'app_produits')]
    public function index(ProduitsRepository $produitsRepository, SessionInterface $sessionInterface): Response
    {
        $produits = $produitsRepository->findAll();

        return $this->render('produits/index.html.twig', [
            'produits' => $produits
        ]);
    }
}
