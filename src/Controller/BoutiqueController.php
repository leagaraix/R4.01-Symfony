<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\BoutiqueService;

final class BoutiqueController extends AbstractController
{
    #[Route(
        path: '/{_locale}/boutique',
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
        path: '/{_locale}/boutique/rayon/{idCategorie}',
        name: 'app_boutique_rayon',
        requirements: ['_locale' => '%app.supported_locales%']
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

}
