<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Entity\Produit;
use App\Form\ClientType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CommandeController extends AbstractController
{
    #[Route('/commande/valider', name: 'commande_valider')]
    public function valider(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $session = $request->getSession();

        $panier = $session->get('panier', []);

        if (empty($panier)) {
            return $this->redirectToRoute('app_panier');
        }

        $client = new Client();

        $form = $this->createForm(ClientType::class, $client);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $commande = new Commande();

            $commande->setClient($client);
            $commande->setDateCommande(new \DateTime());
            $commande->setLivree(false);

            $total = 0;

            foreach ($panier as $id => $quantite) {

                $produit = $entityManager
                    ->getRepository(Produit::class)
                    ->find($id);

                if (!$produit) {
                    continue;
                }

                $prix = $produit->getPrix();

                $ligne = new LigneCommande();

                $ligne->setProduit($produit);
                $ligne->setQuantite($quantite);
                $ligne->setPrix($prix);
                $ligne->setCommande($commande);

                $commande->addLigneCommande($ligne);
                $entityManager->persist($ligne);

                $total += $prix * $quantite;

                $produit->setQuantite(
                    $produit->getQuantite() - $quantite
                );

                if ($produit->getQuantite() <= 0) {
                    $produit->setQuantite(0);
                    $produit->setDisponible(false);
                }
            }

            $commande->setMontantTotal($total);

            $entityManager->persist($client);
            $entityManager->persist($commande);
            $entityManager->flush();

            $session->remove('panier');

            return $this->render('commande/succes.html.twig', [
                'commande' => $commande,
            ]);
        }

        return $this->render('commande/valider.html.twig', [
            'form' => $form,
        ]);
    }
}