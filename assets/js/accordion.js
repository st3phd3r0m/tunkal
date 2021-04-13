let pointTablette = 768;
let pointDesktop = 992;

$(function () {
    // Sur le burger, on met un ecouteur, pour derouler/enrouler le menus principal
    $("#navMenu").on("click", openMenu);

    //Ecouteur d'événement sur la fenêtre
    $(window).on('resize', switchBanner);

    if (screen.width > pointTablette) {
        let src = $(".logoheader").data('desktop-image');
        $(".logoheader").attr('src', src);
    }

    $(".logoheader").on('resize', toogleLogo);

});


function toogleLogo() {
    let src;
    if (screen.width > pointTablette) {
        src = $(this).data('desktop-image');
    }else if(screen.width <= pointTablette){
        src = $(this).data('mobile-image');
    }
    $(".logoheader").attr('src', src);
}

/**
 * Fonction qui déroule/enroule l'élément html suivant
 */
function openMenu() {
    $(this).next().slideDown(200, documentCloseMenu);
}

/**
 * Fonction qui ajoute un event "click" sur le document pour fermer le menu
 */
function documentCloseMenu() {
    $(document).one("click", closeMenu);
}

/**
 * Fonction qui enroule les menus secondaire et principal
 */
function closeMenu() {
    $(".menuPrincipal").slideUp(200);
}


function switchBanner(){
    if ($(window).width() > pointDesktop ) {
        $(".menuPrincipal").slideDown();
    } else {
        $(".menuPrincipal").slideUp();
    }
}