//Attente chargement page web
$(function () {

    // double Flèche en bas à droite de la fenetre en position:fixed pour retour à l'acceuil du site
    $("#scroll").on("click", goToHomeSite);


}); // Fin chargement page web

/**
 * Fonction qui va vers l'acceuil en scrolling
 * @param {*} event 
 */
function goToHomeSite() {
    window.scrollTo({
        top: 0,
        left: 0,
        behavior: 'smooth'
    });
}