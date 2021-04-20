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

    if ($($('#upcoming-concerts-fields-list').find('option')[0]).val() == '') {
        $('#upcoming-concerts-fields-list').hide();
    }

    if ($($('#previous-concerts-fields-list').find('option')[0]).val() == '') {
        $('#previous-concerts-fields-list').hide();
    }

    if ($($('#news-fields-list').find('option')[0]).val() == '') {
        $('#news-fields-list').hide();
    }

    if ($($('#external-link-field').find('input')[0]).val() == '') {
        $('#external-link-field').hide();
    }

    //Ecouteur d'événements sur le champ "type de liens"
    $('#links_type').find('option').on('click', showSelectFields);
});

/**
 * Fonction qui appel une requete ajax suivant le type de liens selectionné par l'utilisateur
 */
function showSelectFields() {
    let url;
    let targetElement;
    $("#links_hyperlink").val(this.value);
    $("#links_title").val(this.textContent);
    $("#links_content").val(this.textContent);
    $("#links_alt").val(this.textContent);

    switch (this.value) {
        case '/upcoming/concerts/':
            targetElement = $('#upcoming-concerts-fields-list');
            $('#news-fields-list').hide();
            $('#previous-concerts-fields-list').hide();
            $('#external-link-field').hide();
            $(targetElement).show();
            url =  $(targetElement).data('url-path');
            break;
        case '/news/':
            targetElement = $('#news-fields-list');
            $('#previous-concerts-fields-list').hide();
            $('#external-link-field').hide();
            $('#upcoming-concerts-fields-list').hide();
            $(targetElement).show();
            url = $(targetElement).data('url-path')+'?isPastConcert=0';
            break;
        case '/previous/concerts/':
            targetElement = $('#previous-concerts-fields-list');
            $('#external-link-field').hide();
            $('#upcoming-concerts-fields-list').hide();
            $('#news-fields-list').hide();
            $(targetElement).show();
            url = $(targetElement).data('url-path')+'?isPastConcert=1';
            break;
        case 'external':
            $('#previous-concerts-fields-list').hide();
            $('#upcoming-concerts-fields-list').hide();
            $('#news-fields-list').hide();
            $('#external-link-field').show();
            break;
        default:
            $('#previous-concerts-fields-list').hide();
            $('#upcoming-concerts-fields-list').hide();
            $('#news-fields-list').hide();
            $('#external-link-field').hide();
    }

    if(url != null && targetElement != null){
        ajaxCall(url, targetElement);
    }
    
}

function ajaxCall(url, targetElement){
    fetch(
        url,
        {
            method: 'GET',
            mode: 'same-origin',
            headers: {
                "X-Requested-With": 'XMLHttpRequest',
                "Authorization": $("#links__token").val()
            }
        }
    ).then(
        response => response.text()
    ).then(
        value => {
            let items = JSON.parse(value);
            if (items.data.length == 0) {
                //Message utilisateur au cas où la requete ajax retourne un tableau vide 
                targetElement.find('option').val('Aucun élément de ce type en bdd');
                targetElement.find('option').text('Aucun élément de ce type en bdd');
            } else {
                //On vide l'élément de ses balises option
                $(targetElement).find('select')[0].innerHTML = '';
                //Création de balises <option> dans la balise <select>
                for (let item of items.data) {
                    let option = document.createElement("option");
                    option.value = item.slug;
                    option.textContent = item.title;
                    $(targetElement).find('select')[0].appendChild(option);
                    //Ecouteur d'événement sur chacune des balises <option>
                    $(option).on('click', fillTextInput);
                }
            }
        }
    );
}

/**
 * Fonction qui pré-remplit les champs <input> de titre du liens et de contenu texte du lien
 */
function fillTextInput() {
    console.log(this.value)
    console.log(this.textContent)

    $("#links_title").val(this.textContent);
    $("#links_content").val(this.textContent);
    $("#links_alt").val(this.textContent);
    $("#links_hyperlink").val($("#links_hyperlink").val()+this.value);
}


/**
 * Fonction qui affiche l'image sélectionnée par l'utilisateur dans le formulaire
 */
function pickFileName() {
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
