<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\BoutiqueService;

#[Route(path: '/{_locale}/boutique', requirements: ['_locale' => '%app.supported_locales%'])]
final class BoutiqueController extends AbstractController
{
    #[Route(
        path: '/',
        name: 'app_boutique_index',
        requirements: ['_locale' => '%app.supported_locales%']
    )]
    public function index(BoutiqueService $boutique): Response
    {
        // Utiliser le service pour récupérer les catégories
        $categories = $boutique->findAllCategories();

        // Rendre le template, auquel on transmet les catégories
        return $this->render('boutique/index.html.twig', [
            'controller_name' => 'BoutiqueController',
            'categories' => $categories
        ]);
    }

    #[Route(
        path: '/rayon/{idCategorie}',
        name: 'app_boutique_rayon',
        requirements: ['_locale' => '%app.supported_locales%', 'idCategorie' => '\d+']
    )]
    public function rayon(int $idCategorie, BoutiqueService $boutique): Response
    {
        $categorie = $boutique->findCategorieById($idCategorie);
        $produits = $boutique->findProduitsByCategorie($idCategorie);

        return $this->render('boutique/rayon.html.twig', [
            'produits' => $produits,
            'categorie' => $categorie
        ]);
    }

    #[Route(
        path: '/chercher/{recherche}',
        name: 'app_boutique_chercher',
        requirements: [
            '_locale' => '%app.supported_locales%',
            'recherche' => '.+' // .+ est le regexp pour avoir tous les car, / compris
        ],
        defaults: ['recherche' => ''] // Valeur par défaut de recherche doit être une chaîne vide
    )]
    public function chercher(boutiqueService $boutique, string $recherche): Response
    {
        $recherche = urldecode($recherche);
        $produits = $boutique->findProduitsByLibelleOrTexte($recherche);

        return $this->render('boutique/chercher.html.twig', [
            'recherche' => $recherche,
            'produits' => $produits
        ]);
    }

}
