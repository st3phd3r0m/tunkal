/**Code js destiné à la gestion d'ajout dynamiques d'éléments de fomulaire pour l'édition et la création d'un produit ou d'une catégorie.
 * Les ajouts d'éléments permettent d'associer plusieurs images, plusieurs catégories ou plusieurs attributs à un produit lors de son édition/création.
 * On peut aussi ajouter plusieurs images à une catégorie lors de son édition/création.
 * 
*/

let counter = 0;

$(function () {

    let inputMetaName = document.querySelector("#posts_metaTitle");
    let p1 = document.createElement("p");
    inputMetaName.parentElement.insertBefore(p1, inputMetaName);
    inputMetaName.addEventListener('keyup', checkMetaNameFieldLength);

    let inputMetaDescription = document.querySelector("#posts_metaDescription");
    let p2 = document.createElement("p");
    inputMetaDescription.parentElement.insertBefore(p2, inputMetaDescription);
    inputMetaDescription.addEventListener('keyup', checkMetaDescriptionFieldLength);


    //Ecouteur d'évémenents sur le bouton d'ajout de champ (en lien avec les collectionType) 
    //Ajout champ image du produit
    $('.add-another-image-collection-widget').on('click', addAnotherCollectionWidget);

    //Pour les champs images pré-remplis (mapping des images qui sont déjà liés au produit en bdd) :
    //Selection des éléments li de la collection des images
    let imagesFormElements = $('#images-fields-list>li');
    
    for (let imagesFormElement of imagesFormElements) {
        //Unne balise <a> dans le template sert accéssoirement à supprimer une catégorie du produit
        //Un écouteur d'événement y est adossé
        $(imagesFormElement).find('a').on('click', removeElementFromProduct);
    }
});

/**
 * Cette fonction ajoute un groupe de champs "catégories" ou "images", à l'image des prototypes délivrés 
 * par les collectionType du formulaire ProductsType 
 */
function addAnotherCollectionWidget(event) {

    event.preventDefault();

    let list = $($(this).data('list-selector'));
    //Donne le nombre d'éléments dans la liste de collection, soit via le data dans l'élément 'list', soit
    //en utilisant la méthode children
    counter = list.data('widget-counter') || list.children().length;

    // Récupération en data du prototype qui se trouve dans l'élément 'list'
    let newWidget = list.data('prototype');

    // Remplacement, dans le prototype (qui est très basiquement une chaine de caractères), de 
    //"__name__" par un identifiant numérique unique qu'on incrémente par la suite : counter
    newWidget = newWidget.replace(/__name__/g, counter);
    // Incrémentation de counter
    counter++;
    // Ecrasement de counter en data dans l'élément 'list'
    list.data('widget-counter', counter);

    // Création d'un nouvel élément encapsulé dans un élément li grace au prototype encodé dans 'newWidget'
    let newElement = $(list.data('widget-tags')).html(newWidget);
    //Insertion du nouvel élément dans l'élément 'list' 
    newElement.appendTo(list);

    //Ajout d'un écouteur d'évènements sur les balises option de la première balise select dans le nouvel élément
    let selectElement = $(newElement).children()[0];


    //On veut afficher l'image que l'utilisateur vient de sélectionner
    if(selectElement.id.includes('_images_')){
        //Champ input d'ajout d'image
        let input = $(selectElement).find('input')[0];
        //Création d'une balise <img>
        let img = document.createElement('img');
        $(img).css('width', '200px').addClass('img-fluid mb-1 d-none');
        //Insertion de l'élément <img> dans le parent
        selectElement.appendChild(img);
        //Ecouteur d'événements sur le champ <input>
        $(input).on('change', pickFileName);

        //Insertion dans le DOM d'une balise <a> pour la suppression individuelle d'images
        let deleteLink = document.createElement('a');
        deleteLink.setAttribute('href','#');
        deleteLink.classList.add("ml-5");
        deleteLink.textContent ="Retirer l'image ?";
        newElement.prepend(deleteLink);
        deleteLink.addEventListener('click', removeElementFromProduct);
    }
}

/**
 * Fonction qui efface un par un les champs d'une catégorie, d'une image ou d'un attribut (même ceux mappés par les collectionType sur symfony) que l'utilisateur veut enlever d'un produit
 */
function removeElementFromProduct(event){
    event.preventDefault();
    this.parentElement.remove();
}

/**
 * Fonction qui fait apparaître le groupe de champs "nom d'attribut" ("name") si un groupe d'attributs a été sélectionné par l'utilisateur, ou, le groupe de champs "valeur d'attribut" ("value") si un nom d'attribut a été sélectionné par l'utilisateur
 */
function showField(){
    $(this).next().show();
    $(this).next().on('keyup',showField);
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


function checkMetaNameFieldLength(){
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