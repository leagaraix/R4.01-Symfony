const buttonRecherche = document.querySelector("#searchButton");
const inputRecherche = document.querySelector("#searchString").value;

buttonRecherche.addEventListener("click", (event) => {
    let recherche = encodeURIComponent(inputRecherche);
    console.log("Recherche : " + recherche); // Debug

    // Création de l'URL
    // Prendre l'URL de la variable path (celle dans BoutiqueController.php, au-dessus de la fonction chercher()
    // Y modifier la partie {recherche} : remplacer par recherche
})