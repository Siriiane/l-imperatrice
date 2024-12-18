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
        return $this->render('cart/index.html.twig', ['produits' => $cart]);
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

        $total = count($cart);

        return new JsonResponse([
            'cart' => $cart,
            'total' => $total,
            'message' => 'Produit ajouté au panier',
        ]);
    }

    #[Route('/cart/remove/{id}', name: 'cart_remove', methods: ['DELETE'])]
    public function remove(int $id, SessionInterface $session): JsonResponse
    {
        $cart = $session->get('cart', []);
        unset($cart[$id]);
        $session->set('cart', $cart);

        $total = array_reduce($cart, fn($sum, $item) => $sum + $item['price'] * $item['quantity'], 0);

        return new JsonResponse([
            'cart' => $cart,
            'total' => $total,
            'message' => 'Produit supprimé du panier',
        ]);
    }
}
