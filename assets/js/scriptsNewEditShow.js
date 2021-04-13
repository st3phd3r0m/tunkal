$(function () {

    let inputMetaTitle = document.querySelector("#shows_metaTitle");
    let p1 = document.createElement("p");
    inputMetaTitle.parentElement.insertBefore(p1, inputMetaTitle);
    inputMetaTitle.addEventListener('keyup', checkMetaTitleFieldLength);

    let inputMetaDescription = document.querySelector("#shows_metaDescription");
    let p2 = document.createElement("p");
    inputMetaDescription.parentElement.insertBefore(p2, inputMetaDescription);
    inputMetaDescription.addEventListener('keyup', checkMetaDescriptionFieldLength);

    let inputFile = $("#shows_imageFile_file");
    //Création d'une balise <img>
    let img = document.createElement('img');
    $(img).css('width', '200px').addClass('img-fluid mb-1 d-none');
    //Insertion de l'élément <img> dans le parent
    inputFile.parents('fieldset').after($(img));
    inputFile.on('change', pickFileName);
});

function checkMetaTitleFieldLength(){
    let p = $(this).prev();
    if(this.value.length < 50){
        p.text("Texte trop court : "+this.value.length);
        p.css('color','red');
    }else if(this.value.length > 70){
        p.text("Texte trop long : "+this.value.length);
        p.css('color','red');
    }else{
        p.text("ok: "+this.value.length);
        p.css('color','green');
    }
}

function checkMetaDescriptionFieldLength(){
    let p = $(this).prev();
    if(this.value.length < 150){
        p.text("Texte trop court : "+this.value.length);
        p.css('color','red');
    }else if(this.value.length > 200){
        p.text("Texte trop long : "+this.value.length);
        p.css('color','red');
    }else{
        p.text("ok: "+this.value.length);
        p.css('color','green');
    }
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