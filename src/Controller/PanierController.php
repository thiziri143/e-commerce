<?php

namespace App\Controller;

use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PanierController extends AbstractController
{
    #[Route('/panier', name: 'app_panier')]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $session = $request->getSession();
        $panier = $session->get('panier', []);

        $produits = [];
        $total = 0;

        foreach ($panier as $id => $quantite) {
            $produit = $entityManager
                ->getRepository(Produit::class)
                ->find($id);

            if ($produit) {
                $sousTotal = $produit->getPrix() * $quantite;

                $produits[] = [
                    'produit' => $produit,
                    'quantite' => $quantite,
                    'sousTotal' => $sousTotal,
                ];

                $total += $sousTotal;
            }
        }

        return $this->render('panier/index.html.twig', [
            'produits' => $produits,
            'total' => $total,
        ]);
    }

    #[Route('/panier/ajouter/{id}', name: 'panier_ajouter')]
    public function ajouter(
        Produit $produit,
        Request $request
    ): Response {
        $session = $request->getSession();
        $panier = $session->get('panier', []);

        $id = $produit->getId();

        $quantiteActuelle = $panier[$id] ?? 0;

        if (
            $produit->isDisponible()
            && $quantiteActuelle < $produit->getQuantite()
        ) {
            $panier[$id] = $quantiteActuelle + 1;
        }

        $session->set('panier', $panier);

        return $this->redirectToRoute('app_panier');
    }

    #[Route('/panier/diminuer/{id}', name: 'panier_diminuer')]
    public function diminuer(
        Produit $produit,
        Request $request
    ): Response {
        $session = $request->getSession();
        $panier = $session->get('panier', []);

        $id = $produit->getId();

        if (isset($panier[$id])) {
            $panier[$id]--;

            if ($panier[$id] <= 0) {
                unset($panier[$id]);
            }
        }

        $session->set('panier', $panier);

        return $this->redirectToRoute('app_panier');
    }

    #[Route('/panier/supprimer/{id}', name: 'panier_supprimer')]
    public function supprimer(
        Produit $produit,
        Request $request
    ): Response {
        $session = $request->getSession();
        $panier = $session->get('panier', []);

        unset($panier[$produit->getId()]);

        $session->set('panier', $panier);

        return $this->redirectToRoute('app_panier');
    }
}