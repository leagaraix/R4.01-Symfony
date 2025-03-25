<?php
namespace App\Service;

use App\Entity\Categorie;
use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Entity\Produit;
use App\Entity\Usager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

// Service pour manipuler le panier et le stocker en session
class PanierService
{
    ////////////////////////////////////////////////////////////////////////////
    private $session;   // Le service session
    private $produitRepository;
    private $panier;    // Tableau associatif, la clé est un idProduit, la valeur associée est une quantité
                        //   donc $this->panier[$idProduit] = quantité du produit dont l'id = $idProduit
    const PANIER_SESSION = 'panier'; // Le nom de la variable de session pour faire persister $this->panier

    // Constructeur du service
    public function __construct(RequestStack $requestStack, ManagerRegistry $managerRegistry)
    {
        // Récupération du service session et du Repository Produit
        $this->produitRepository = $managerRegistry->getRepository(Produit::class);
        $this->session = $requestStack->getSession();

        // Récupération du panier en session s'il existe, init. à vide sinon
        $this->panier = $this->session->get('panier', array());
    }

    // Renvoie le montant total du panier
    public function getTotal() : float
    {
        $total = 0.0;
        foreach($this->panier as $idProduit => $quantite) {
           $produit = $this->produitRepository->find($idProduit);
           // Vérifier que le produit existe bien pour éviter les erreurs
           if ($produit) { $total += $produit->getPrix() * $quantite; }
        }
        return $total;
    }

    // Renvoie le nombre de produits dans le panier
    public function getNombreProduits() : int
    {
        $nbProduits = 0;
        foreach($this->panier as $produit => $quantite) {
            $nbProduits++;
        }
        return $nbProduits;
    }

    // Ajouter au panier le produit $idProduit en quantite $quantite 
    public function ajouterProduit(int $idProduit, int $quantite = 1) : void
    {
        if (isset($this->panier[$idProduit])) {
            $this->panier[$idProduit] += $quantite;
        } else {
            $this->panier[$idProduit] = $quantite;
        }

        // Sauvegarder le panier dans la session
        $this->session->set('panier', $this->panier);
    }

    // Enlever du panier le produit $idProduit en quantite $quantite 
    public function enleverProduit(int $idProduit, int $quantite = 1) : void
    {
        $this->panier[$idProduit] -= $quantite;
        if($this->panier[$idProduit] <= 0) {
            $this->supprimerProduit($idProduit);
        }

        // Sauvegarder le panier dans la session
        $this->session->set('panier', $this->panier);
    }

    // Supprimer le produit $idProduit du panier
    public function supprimerProduit(int $idProduit) : void
    {
        unset($this->panier[$idProduit]);
        $this->session->set('panier', $this->panier);
    }

    // Vider complètement le panier
    public function vider() : void
    {
        $this->panier = [];

        // Sauvegarder le panier dans la session
        $this->session->set('panier', $this->panier);
    }

    // Renvoie le contenu du panier dans le but de l'afficher
    //   => un tableau d'éléments [ "produit" => un objet produit, "quantite" => sa quantite ]
    public function getContenu() : array
    {
        $contenuPanier = array();

        foreach($this->panier as $idProduit => $quantite) {

            $produit = $this->produitRepository->find($idProduit);

            if ($produit) {
                $contenuPanier[] = [
                    "produit" => $produit,
                    "quantite" => $quantite
                ];
            }
        }
        return $contenuPanier;
    }

    // Créer une commande à partir du contenu du panier (s'il n'est pas vide)
    public function panierToCommande(Usager $usager) : ?Commande {

        // Vérifier que le panier n'est pas vide
        if ($this->getNombreProduits() == 0) {
            return null;
        }

        // Création de la commande
        $commande = new Commande();
        $commande->setUsager($usager);
        $commande->setId(3); // !!!!!!!!!!!!
        $commande->setDateCreation(new \DateTime());

        // Parcourir le panier, ajouter une ligne de commande à la commande à chaque nouveau produit
        foreach($this->panier as $idProduit => $quantite) {

            // Récupérer le produit concerné
            $produit = $this->produitRepository->find($idProduit);

            // Création de la ligne de commande pour ce produit
            $ligneCommande = new LigneCommande();
            $ligneCommande->setProduit($produit);
            $ligneCommande->setCommande($commande);
            $ligneCommande->setQuantite($quantite);
            $ligneCommande->setPrix($produit->getPrix());

            // Ajouter la ligne à la commande
            $commande->addLignesCommande($ligneCommande);
        }

        // TODO : voir cours 4 p.12, il manque sûrement persist() et flush()
        // déjà fait usagerController, en bas, donc il doit y avoir des façons de faire




        // Vider le panier
        $this->vider();

        return $commande;
    }

}