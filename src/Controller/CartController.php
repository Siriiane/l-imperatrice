<?php

namespace App\Controller;

// Importation des classes nécessaires pour gérer les requêtes, les sessions et les réponses
use App\Repository\ProduitsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    // Affichage du panier
    #[Route('/cart', name: 'app_cart')]
    public function index(SessionInterface $session): Response
    {
        // Récupération du panier depuis la session (ou un tableau vide si le panier est vide)
        $cart = $session->get('cart', []);

        // Initialisation du prix total
        $totalPrice = 0;

        // Parcours des éléments du panier pour calculer le prix total
        foreach ($cart as &$item) {
            $item['totalItemPrice'] = $item['produit']->getPrix() * $item['quantite'];
            $totalPrice += $item['totalItemPrice'];
        }

        // Retourne la vue du panier avec les produits et le prix total
        return $this->render('cart/index.html.twig', [
            'produits' => $cart,
            'totalPrice' => $totalPrice,
        ]);
    }

    // Ajout d'un produit au panier
    #[Route('/cart/add', name: 'cart_add')]
    public function add(SessionInterface $session, ProduitsRepository $produitsRepository, Request $request): JsonResponse
    {
        // Récupération de l'ID du produit depuis la requête JSON
        $id = json_decode($request->getContent(), true);

        // Recherche du produit dans la base de données
        $product = $produitsRepository->find($id);

        // Vérification si le produit existe
        if (!$product) {
            return new JsonResponse(['error' => 'Produit non trouvé'], 404);
        }

        // Récupération du panier depuis la session
        $cart = $session->get('cart', []);
        $found = false;

        // Vérification si le produit est déjà dans le panier
        foreach ($cart as &$c) {
            if ($c['produit']->getId() == $product->getId()) {
                $c['quantite']++;
                $found = true;
                break;
            }
        }

        // Si le produit n'est pas encore dans le panier, on l'ajoute
        if (!$found) {
            $cart[] = ['produit' => $product, 'quantite' => 1];
        }

        // Mise à jour du panier dans la session
        $session->set('cart', $cart);

        // Calcul du nombre total d'articles dans le panier
        $totalItems = array_sum(array_column($cart, 'quantite'));
        $session->set('nb', $totalItems);

        return new JsonResponse([
            'cart' => $cart,
            'totalItems' => $totalItems,
            'message' => 'Produit ajouté au panier',
        ]);
    }

    // Suppression d'un produit du panier
    #[Route('/cart/remove', name: 'cart_remove')]
    public function remove(SessionInterface $session, Request $request): JsonResponse
    {
        // Récupération de l'ID du produit depuis la requête JSON
        $array = json_decode($request->getContent(), true);
        $id = $array['id'];

        // Récupération du panier
        $cart = $session->get('cart', []);

        // Parcours du panier pour trouver l'élément à supprimer
        foreach ($cart as $key => $item) {
            if ($item['produit']->getId() == $id) {
                unset($cart[$key]);
                break;
            }
        }

        // Mise à jour du panier dans la session
        $session->set('cart', $cart);

        // Recalcul du prix total du panier
        $totalPrice = array_reduce($cart, function ($sum, $item) {
            return $sum + $item['produit']->getPrix() * $item['quantite'];
        }, 0);

        // Recalcul du nombre total d'articles
        $totalItems = array_sum(array_column($cart, 'quantite'));
        $session->set('nb', $totalItems);

        return new JsonResponse([
            'cart' => array_values($cart), // Réindexation du tableau
            'totalPrice' => $totalPrice,
            'totalItems' => $totalItems,
            'message' => 'Produit supprimé du panier',
        ]);
    }

    // Mise à jour de la quantité d'un produit dans le panier
    #[Route('/cart/update-quantity', name: 'update_quantity')]
    public function updateQuantity(SessionInterface $session, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $id = $data['id'];
        $quantity = $data['quantite'];

        // Vérification que la quantité est valide
        if ($quantity < 1) {
            return new JsonResponse(['error' => 'Quantité invalide'], 400);
        }

        // Mise à jour de la quantité du produit dans le panier
        $cart = $session->get('cart', []);
        foreach ($cart as &$item) {
            if ($item['produit']->getId() == $id) {
                $item['quantite'] = $quantity;
                $item['totalItemPrice'] = $item['produit']->getPrix() * $quantity;
                break;
            }
        }

        $session->set('cart', $cart);

        // Recalcul des totaux
        $totalPrice = array_reduce($cart, function ($sum, $item) {
            $itemTotal = $item['totalItemPrice'] ?? $item['produit']->getPrix() * $item['quantite'];
            return $sum + $itemTotal;
        }, 0);

        $totalItems = array_sum(array_column($cart, 'quantite'));
        $session->set('nb', $totalItems);

        return new JsonResponse([
            'itemId' => $id,
            'totalPrice' => $totalPrice,
            'totalItems' => $totalItems,
        ]);
    }

    // Suppression de tout le panier
    #[Route('/suppCart', name: 'delete_cart')]
    public function deleteAll(SessionInterface $sessionInterface, Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if ($data['supp']) {
            $sessionInterface->remove('cart');
        } else {
            return new JsonResponse(['success' => false]);
        }

        return new JsonResponse(['success' => true]);
    }
}
