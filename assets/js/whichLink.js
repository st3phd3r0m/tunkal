/**
 * Code qui gére la sélection du type de liens et le pré-remplissage du formulaire de création/modification de liens  
 */

$(function () {

    let inputFile = $("#links_imageFile_file");
    //Création d'une balise <img>
    let img = document.createElement('img');
    $(img).css('width', '200px').addClass('img-fluid mb-1 d-none');
    //Insertion de l'élément <img> dans le parent
    inputFile.parents('fieldset').after($(img));
    inputFile.on('change', pickFileName);

    //Suivant le type de liens que souhaite ajouter l'utilisateur, on
    //fait apparaître ultérieurement les champs corespondants

    if( $($('#shows-fields-list').find('option')[0]).val() == '' ){
        $('#shows-fields-list').hide();
    }

    if( $($('#posts-fields-list').find('option')[0]).val() == '' ){
        $('#posts-fields-list').hide();
    }

    if( $($('#external-link-field').find('input')[0]).val() == '' ){
        $('#external-link-field').hide();
    }

    //Ecouteur d'événements sur le champ "type de liens"
    $('#links_type').on('click',ajaxCall);
});

/**
 * Fonction qui appel une requete ajax suivant le type de liens selectionné par l'utilisateur
 */
function ajaxCall(){

    //On vide les balises <input> de titre et de contenu
    $("#links_title").val('');
    $("#links_content").val('');
    $("#links_alt").val('');

    let url;
    let targetElement;

    if(this.value === '/show/'){
        //Si l'utilisateur choisit d'ajouter un lien vers une page "catégorie"
        targetElement = $('#shows-fields-list');
        //On cache les autres balises <input> et <select>
        $('#posts-fields-list').hide();
        $('#external-link-field').hide();
        //On fait apparaitre la balise cible
        $(targetElement).show();
        //L'url cible de la requete ajax 
        url = $('#shows-fields-list').data('url-path');
    }else if(this.value === '/post/'){
        //Si l'utilisateur choisit d'ajouter un lien vers une page "publication"
        targetElement = $('#posts-fields-list');
        //On cache les autres balises <input> et <select>
        $('#shows-fields-list').hide();
        $('#external-link-field').hide();
        //On fait apparaitre la balise cible
        $(targetElement).show();
        //L'url cible de la requete ajax 
        url = $('#posts-fields-list').data('url-path');
    }else if(this.value === 'external'){
        //Si l'utilisateur choisit d'ajouter un lien externe
        //On cache les balises <select>
        $('#shows-fields-list').hide();
        $('#posts-fields-list').hide();
        //On fait apparaitre la balise <input> cible
        $('#external-link-field').show();
        return false;
    }else{
        $('#posts-fields-list').hide();
        $('#shows-fields-list').hide();
        $('#external-link-field').hide();
        return false;
    }

    //Requete ajax methode get
    $.ajax({
        url: url,
        method: "get"
    }).done((response) => {
        // Récupération des catégories ou pages de publication
        items = JSON.parse(response);
        
        if (items.length == 0) {
            //Message utilisateur au cas où la requete ajax retourne un tableau vide 
            let option = document.createElement("option");
            option.value = "Aucun élément de ce type en bdd";
            option.textContent = "Aucun élément de ce type en bdd";
            targetElement.children.appendChild(option);
        } else {
            //On vide l'élément de ses balises option
            $(targetElement).find('select')[0].innerHTML = '';
            //Création de balises <option> dans la balise <select>
            for (let item of items) {
                let option = document.createElement("option");
                option.value = item.slug;
                option.textContent = item.title;
                $(targetElement).find('select')[0].appendChild(option);
                //Ecouteur d'événement sur chacune des balises <option>
                $(option).on('click', fillTextInput);
            }
        }
    });
}

/**
 * Fonction qui pré-remplit les champs <input> de titre du liens et de contenu texte du lien
 */
function fillTextInput(){
    $("#links_title").val(this.textContent);
    $("#links_content").val(this.textContent);
    $("#links_alt").val(this.textContent);
}


/**
 * Fonction qui affiche l'image sélectionnée par l'utilisateur dans le formulaire
 */
function pickFileName(){
    //Instanciation de FileReader
    var reader = new FileReader();
    //L'instance lit le fichier image
    reader.readAsDataURL(this.files[0]);

    //Récupération de l'élément <img>
    let img = $($(this).parents('fieldset')[0]).next()[0];

    //On met un écouteur d'événements sur l'instance reader
    reader.addEventListener("load", function () {
        //Une fois la lecture du fichier image par reader est terminée, on récupère l'url de l'image que l'on charge dans l'attribut src de la balise <img>
        img.src = reader.result;
      }, false);

    //On fait apparaître la balise <img> dans le DOM
    $(img).removeClass('d-none').addClass('d-inline');
}
