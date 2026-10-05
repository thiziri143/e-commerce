<?php

namespace App\Controller;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminClientController extends AbstractController
{
    #[Route('/admin/clients', name: 'admin_clients')]
    public function index(
        EntityManagerInterface $entityManager
    ): Response {
        $clients = $entityManager
            ->getRepository(Client::class)
            ->findBy(
                [],
                ['nom' => 'ASC']
            );

        return $this->render('admin_client/index.html.twig', [
            'clients' => $clients,
        ]);
    }
}