<?php

namespace App\Controller;

use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $form = $this->createForm(ContactType::class);


        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $entityManagerInterface->persist($data);
            $entityManagerInterface->flush();

            return $this->redirectToRoute('app_success_message');
        }

        return $this->render('contact/index.html.twig', [
            'contact_form' => $form->createView(),
            'bodyClass' => 'contact-image'
        ]);
    }

    #[Route('/success', name: 'app_success_message')]

    public function successMessage(){
        return $this->render('contact/success.html.twig');
    }
}
