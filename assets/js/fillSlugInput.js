/**
 * Code qui prérempli le champ slug dans les formulaires "pages" et "categories"
 */
$(function () {
    $("#posts_title").on('change', fillTextInput);
    $("#shows_title").on('change', fillTextInput);
});

function fillTextInput(){
    let id = this.id;
    $("#"+id.replace('title', 'slug')).val(this.value);
}