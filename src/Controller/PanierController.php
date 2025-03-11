<?php

namespace App\Controller;

use App\Entity\Usager;
use App\Service\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/{_locale}/panier', requirements: ['_locale' => '%app.supported_locales%'])]
final class PanierController extends AbstractController
{
    /**
     * Affichage de la page Panier.
     */
    #[Route(
        path: '/',
        name: 'app_panier_index'
    )]
    public function index(PanierService $panier): Response {
        return $this->render('panier/index.html.twig', [
            'controller_name' => 'PanierController',
            'total' => $panier->getTotal(),
            'nombreProduits' => $panier->getNombreProduits(), // TODO : Est-ce qu'il vaut mieux passer un paramètre nombreProduits, ou appeler contenuPanier|length dans le Twig ?
            'contenuPanier' => $panier->getContenu()
        ]);
    }

    /**
     * Ajout d'un produit au panier, redirection vers la page Panier.
     */
    #[Route(
        path: '/ajouter/{idProduit}/{quantite}',
        name: 'app_panier_ajouter',
        requirements: [
            'idProduit' => '\d+',
            'quantite' => '\d+'
        ],
        defaults: ['quantite' => 1]
    )]
    public function ajouter(int $idProduit, int $quantite, PanierService $panier): Response {
        $panier->ajouterProduit($idProduit, $quantite);
        return $this->redirectToRoute('app_panier_index');
    }

    /**
     * Retire un exemplaire d'un produit du panier.
     */
    #[Route(
        path: '/enlever/{idProduit}/{quantite}',
        name: 'app_panier_enlever',
        requirements: [
            'idProduit' => '\d+',
            'quantite' => '\d+'
        ],
        defaults: ['quantite' => 1]
    )]
    public function enlever(int $idProduit, int $quantite, PanierService $panier): Response {
        $panier->enleverProduit($idProduit, $quantite);
        return $this->redirectToRoute('app_panier_index');
    }

    /**
     * Suppression d'un produit du panier.
     */
    #[Route(
        path: '/supprimer/{idProduit}',
        name: 'app_panier_supprimer',
        requirements: ['idProduit' => '\d+']
    )]
    public function supprimer(int $idProduit, PanierService $panier): Response {
        $panier->supprimerProduit($idProduit);
        return $this->redirectToRoute('app_panier_index');
    }

    /**
     * Vide le panier.
     */
    #[Route(
        path: '/vider',
        name: 'app_panier_vider'
    )]
    public function vider(PanierService $panier): Response {
        $panier->vider();
        return $this->redirectToRoute('app_panier_index');
    }

    /**
     * Lance la commande du panier.
     */
    #[Route(
        path: '/commande',
        name: 'app_panier_commander',
    )]
    public function commander(PanierService $panier): Response {
        $panier->panierToCommande();
        return $this->render('panier/commande.html.twig');
    }

    /**
     * Contrôleur imbriqué : permet d'afficher le nombre de produits du panier dans la barre de navigation
     */
    public function nombreProduits(PanierService $panier): Response {
        return new Response($panier->getNombreProduits());
    }

}
