<?php

namespace App\Controller;

use App\Entity\Produit;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProduitController extends AbstractController
{
    #[Route('/produit', name: 'app_produit')]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        PaginatorInterface $paginator
    ): Response {
        $tri = $request->query->get('tri', 'designation');

        $ordresAutorises = [
            'prix_asc' => ['prix', 'ASC'],
            'prix_desc' => ['prix', 'DESC'],
            'designation' => ['designation', 'ASC'],
            'disponibilite' => ['disponible', 'DESC'],
        ];

        if (!isset($ordresAutorises[$tri])) {
            $tri = 'designation';
        }

        [$champ, $ordre] = $ordresAutorises[$tri];

        $query = $entityManager
            ->getRepository(Produit::class)
            ->createQueryBuilder('p')
            ->orderBy('p.' . $champ, $ordre)
            ->getQuery();

        $produits = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('produit/index.html.twig', [
            'produits' => $produits,
            'tri' => $tri,
        ]);
    }
}