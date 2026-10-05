<?php

namespace App\Controller;

use App\Entity\Commande;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminCommandeController extends AbstractController
{
    #[Route('/admin/commandes', name: 'admin_commandes')]
    public function index(
        EntityManagerInterface $entityManager
    ): Response {
        $commandes = $entityManager
            ->getRepository(Commande::class)
            ->findBy(
                ['livree' => false],
                ['dateCommande' => 'DESC']
            );

        return $this->render('admin_commande/index.html.twig', [
            'commandes' => $commandes,
        ]);
    }

    #[Route('/admin/commande/{id}', name: 'admin_commande_detail')]
    public function detail(Commande $commande): Response
    {
        return $this->render('admin_commande/detail.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/admin/commande/{id}/livrer', name: 'admin_commande_livrer')]
    public function livrer(
        Commande $commande,
        EntityManagerInterface $entityManager
    ): Response {
        $commande->setLivree(true);

        $entityManager->flush();

        return $this->redirectToRoute('admin_commandes');
    }
}