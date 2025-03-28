<?php

namespace App\Controller;

use App\Entity\Usager;
use App\Repository\UsagerRepository;
use App\Service\PanierService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
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
        path: '/commander',
        name: 'app_panier_commander'
    )]
    public function commander(PanierService $panier, Security $security, EntityManagerInterface $entityManager): Response
    {

        $this->denyAccessUnlessGranted('ROLE_CLIENT');

        $usager = $security->getUser();
        $commande = $panier->panierToCommande($usager, $entityManager);

        if ($commande) {
            return $this->render('panier/commande.html.twig', [
                'usager' => $usager,
                'commande' => $commande
            ]);
        } else {
            return $this->redirectToRoute('app_panier_index');
        }

    }

    /**
     * Contrôleur imbriqué : permet d'afficher le nombre de produits du panier dans la barre de navigation
     */
    public function nombreProduits(PanierService $panier): Response {
        return new Response($panier->getNombreProduits());
    }

}
