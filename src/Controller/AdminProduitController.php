<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminProduitController extends AbstractController
{
    #[Route('/admin/produit/ajouter', name: 'admin_produit_ajouter')]
    public function ajouter(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $produit = new Produit();

        $form = $this->createForm(ProduitType::class, $produit);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('app_produit');
        }

        return $this->render('admin_produit/ajouter.html.twig', [
            'form' => $form,
        ]);
    }
}