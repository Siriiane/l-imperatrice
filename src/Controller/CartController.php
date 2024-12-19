<?php

namespace App\Controller;

use App\Repository\ProduitsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);

        // Calcul des totaux
        $totalPrice = 0;
        foreach ($cart as &$item) {
            $item['totalItemPrice'] = $item['produit']->getPrix() * $item['quantite'];
            $totalPrice += $item['totalItemPrice'];
        }

        return $this->render('cart/index.html.twig', [
            'produits' => $cart,
            'totalPrice' => $totalPrice,
        ]);
    }

    #[Route('/cart/add', name: 'cart_add')]
    public function add(SessionInterface $session, ProduitsRepository $produitsRepository, Request $request): JsonResponse
    {
        $id = json_decode($request->getContent(), true);

        $product = $produitsRepository->find($id);

        if (!$product) {
            return new JsonResponse(['error' => 'Produit non trouvé'], 404);
        }

        $cart = $session->get('cart', []);
        $found = false;

        foreach ($cart as &$c) {
            if ($c['produit']->getId() == $product->getId()) {
                $c['quantite']++;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $cart[] = ['produit' => $product, 'quantite' => 1];
        }

        $session->set('cart', $cart);
        
        
        // Calcul du total des articles dans le panier
        $totalItems = array_sum(array_column($cart, 'quantite'));
        $session->set('nb', $totalItems);
        return new JsonResponse([
            'cart' => $cart,
            'totalItems' => $totalItems,
            'message' => 'Produit ajouté au panier',
        ]);
    }

    #[Route('/cart/remove', name: 'cart_remove')]
    public function remove(SessionInterface $session, Request $request): JsonResponse
    {
        $array = json_decode($request->getContent(), true);
        $id = $array['id'];
        $cart = $session->get('cart', []);

        foreach ($cart as $key => $item) {
            if ($item['produit']->getId() == $id) {
                unset($cart[$key]);
                break;
            }
        }

        $session->set('cart', $cart);

        // Recalcul du total général et des quantités
        $totalPrice = array_reduce($cart, function ($sum, $item) {
            return $sum + $item['produit']->getPrix() * $item['quantite'];
        }, 0);

        $totalItems = array_sum(array_column($cart, 'quantite'));
        $session->set('nb', $totalItems);
        return new JsonResponse([
            'cart' => array_values($cart), // Réindexer le tableau pour le JSON
            'totalPrice' => $totalPrice,
            'totalItems' => $totalItems,
            'message' => 'Produit supprimé du panier',
        ]);
    }
}
