<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use App\Service\BoutiqueService;

// Service pour manipuler le panier et le stocker en session
class PanierService
{
    ////////////////////////////////////////////////////////////////////////////
    private $session;   // Le service session
    private $boutique;  // Le service boutique
    private $panier;    // Tableau associatif, la clé est un idProduit, la valeur associée est une quantité
                        //   donc $this->panier[$idProduit] = quantité du produit dont l'id = $idProduit
    const PANIER_SESSION = 'panier'; // Le nom de la variable de session pour faire persister $this->panier

    // Constructeur du service
    public function __construct(RequestStack $requestStack, BoutiqueService $boutique)
    {
        // Récupération des services session et BoutiqueService
        $this->boutique = $boutique;
        $this->session = $requestStack->getSession();
        // Récupération du panier en session s'il existe, init. à vide sinon
        $this->panier = $this->session->get('panier', array());
    }

    // Renvoie le montant total du panier
    public function getTotal() : float
    {
        $total = 0;
        foreach($this->panier as $produit => $quantite) {
           $total += $quantite;
        }
        return $total;
    }

    // Renvoie le nombre de produits dans le panier
    public function getNombreProduits() : int
    {
        $nbProduits = 0;
        foreach($this->panier as $produit => $quantite) {                       // MEILLEURE FACON D'ECRIRE CA ?
            $nbProduits++;
        }
        return $nbProduits;
    }

    // Ajouter au panier le produit $idProduit en quantite $quantite 
    public function ajouterProduit(int $idProduit, int $quantite = 1) : void
    {
        $this->panier[$idProduit] = $quantite;
    }

    // Enlever du panier le produit $idProduit en quantite $quantite 
    public function enleverProduit(int $idProduit, int $quantite = 1) : void
    {
        $this->panier[$idProduit] -= $quantite;
        if($this->panier[$idProduit] < 0) {
            $this->supprimerProduit($idProduit);
        }
    }

    // Supprimer le produit $idProduit du panier
    public function supprimerProduit(int $idProduit) : void
    {
        unset($this->panier[$idProduit]);
    }

    // Vider complètement le panier
    public function vider() : void
    {
        $this->panier = [];
    }

    // Renvoie le contenu du panier dans le but de l'afficher
    //   => un tableau d'éléments [ "produit" => un objet produit, "quantite" => sa quantite ]
    public function getContenu() : array
    {
        $afficherPanier = array();
        foreach($this->panier as $idProduit => $quantite) {
            $produit = $this->boutique->findProduitById($idProduit);
            $afficherPanier[$produit] = $quantite;
        }
        return $afficherPanier;
    }

}