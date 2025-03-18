<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\CommandeProduit;
use App\Entity\User;
use App\Form\AdresseType;
use App\Form\PaymentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class CommandeController extends AbstractController
{
    #[Route('/commande', name: 'app_commande')]
    public function index(): Response
    {
        return $this->render('commande/index.html.twig', [
            'controller_name' => 'CommandeController',
        ]);
    }

    #[Route('/livraison', name: 'app_adresse')]
    public function livraison(Request $request, SessionInterface $sessionInterface, EntityManagerInterface $entityManagerInterface): Response
    {
        $commandeData = [];
        $prixTotal = 0;
        $panier = $sessionInterface->get('cart', []);

        // On récupère le formulaire
        $form = $this->createForm(AdresseType::class, new Commande());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Création du tableau simplifié pour la commande
            foreach ($panier as &$p) {
                $commandeData[] = [
                    'produit_id' => $p['produit']->getId(),
                    'quantite' => $p['quantite'],
                    'prix' => $p['produit']->getPrix(),
                ];
                $prixTotal += $p['produit']->getPrix() * $p['quantite'];
            }

            // On ajoute les informations de la commande (adresse, etc.)
            $commandeData['adresse'] = $form->getData()->getAdresse();
            $commandeData['code_postal'] = $form->getData()->getCodePostal();
            $commandeData['ville'] = $form->getData()->getVille();
            $commandeData['pays'] = $form->getData()->getPays();
            $commandeData['prixTotal'] = $prixTotal;

            // On sauvegarde cette représentation dans la session
            $sessionInterface->set('commande', $commandeData);
            return $this->redirectToRoute('app_paiement');
        }

        return $this->render('commande/livraison.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
