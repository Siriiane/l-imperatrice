<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\CommandeProduit;
use App\Entity\Produits;
use App\Form\PaymentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;


class PaiementController extends AbstractController
{
    #[Route('/paiement', name: 'app_paiement')]
    public function index(SessionInterface $sessionInterface, EntityManagerInterface $entityManagerInterface): Response
    {
        // Récupère les données simplifiées de la commande depuis la session
        $commandeData = $sessionInterface->get('commande', []);

        if (!$commandeData) {
            // Gérer l'erreur si la commande n'existe pas dans la session
            $this->addFlash('error', 'Aucune commande trouvée');
            return $this->redirectToRoute('app_cart');
        }

        // Créer l'objet Commande
        $commande = new Commande();
        $commande->setNumeroCommande(uniqid('_') . rand(1000, 100000));
        $commande->setDateCommande(new \DateTimeImmutable());
        $commande->setAdresse($commandeData['adresse']);
        $commande->setCodePostal($commandeData['code_postal']);
        $commande->setVille($commandeData['ville']);
        $commande->setPays($commandeData['pays']);
        $commande->setPrixTotal($commandeData['prixTotal']);
        $commande->setUser($this->getUser());
        // Ajouter les produits à la commande
        foreach ($commandeData as $data) {
            if (isset($data['produit_id'])) {
                $produit = $entityManagerInterface->find(Produits::class, $data['produit_id']);
                if ($produit) {
                    $commandeProduit = new CommandeProduit();
                    $commandeProduit->setProduit($produit);
                    $commandeProduit->setQuantite($data['quantite']);
                    $commande->addCommandeProduit($commandeProduit);
                }
            }
        }

        // Persister la commande dans la base de données
        $entityManagerInterface->persist($commande);
        $entityManagerInterface->flush();

        // Suppression de la commande de la session une fois le paiement effectué
        $sessionInterface->remove('commande');
        $sessionInterface->remove('cart');
        $sessionInterface->remove('nb');

        return $this->render('paiement/confirm_paiement.html.twig', [
            'commande' => $commande,
        ]);
    }


    #[Route('/confirm_payment', 'app_confirm_payment')]
    public function confirm()
    {
        return $this->render('paiement/confirm_paiement.html.twig', []);
    }
}
