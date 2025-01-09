<?php

namespace App\Controller;

use App\Form\ProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function index(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasherInterface): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfilType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();

            if (!empty($plainPassword)) {
                $hasherPassword = $passwordHasherInterface->hashpassword($user, $plainPassword);
                $user->setPassword($hasherPassword);
            }
            try {
                $em->flush();
                $this->addFlash('success', 'Votre profil a été mis a jour avec succès.');
            } catch (\Exception) {
                $this->addFlash('error', 'Une erreur est survenue lors de la mise a jour de votre profil');
            }
        }
        return $this->render('profil/index.html.twig', [
            // 'controller_name' => 'ProfilController',
            'form' => $form->createView(),
            'bodyClass' => null,
        ]);
    }
}
