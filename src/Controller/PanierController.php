<?php

namespace App\Controller;

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
        name: 'app_panier_index',
        requirements: ['_locale' => '%app.supported_locales%']
    )]
    public function index(PanierService $panier): Response {
        return $this->render('panier/index.html.twig', [
            'controller_name' => 'PanierController',
            'total' => $panier->getTotal(),
            'nombreProduits' => $panier->getNombreProduits(),
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
            '_locale' => '%app.supported_locales%',
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
            '_locale' => '%app.supported_locales%',
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
        requirements: [
            '_locale' => '%app.supported_locales%',
            'idProduit' => '\d+'
        ]
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
        name: 'app_panier_vider',
        requirements: [
            '_locale' => '%app.supported_locales%'
        ]
    )]
    public function vider(PanierService $panier): Response {
        $panier->vider();
        return $this->redirectToRoute('app_panier_index');
    }

    /**
     * Contrôleur imbriqué : permet d'afficher le nombre de produits du panier dans la barre de navigation
     */
    public function nombreProduits(PanierService $panier): Response {
        return new Response($panier->getNombreProduits());
    }

}
