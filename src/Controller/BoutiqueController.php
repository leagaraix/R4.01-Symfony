<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\BoutiqueService;

final class BoutiqueController extends AbstractController
{
    #[Route(
        path: '/boutique',
        name: 'app_boutique_index'
    )]
    public function index(BoutiqueService $boutique): Response
    {
        // Utiliser le service pour récupérer les catégories
        $categories = $boutique->findAllCategories();
        // Rendre le template, auquel on transmet les catégories
        return $this->render('boutique/index.html.twig', ['controller_name' => 'BoutiqueController', 'categories' => $categories]);
    }

    #[Route(
        path: '/boutique/rayon/{idCategorie}',
        name: 'app_boutique_rayon'
    )]
    public function rayon(int $idCategorie, BoutiqueService $boutique): Response
    {
        $categorie = $boutique->findCategorieById($idCategorie);
        // J'EN SUIS ICI, JE CHERCHE A DONNER LE NOM DE LA CATEGORIE A LA VUE
        // ET A RECUPERER LA LISTE DES PRODUITS, QUE JE DOIS AUSSI ENVOYER
        $produits = $categorie->find;
        return $this->render('boutique/rayon.html.twig', [
            'produits' => $produits
            'categorie' => $idCategorie
        ]);
    }

}
