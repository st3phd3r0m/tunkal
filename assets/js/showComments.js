
let maxNumberOfPages =  $("#seeComments").data('numberofpages');
let page;

//Attente chargement page web
$(function () {
    page = 0;
    $("#seeComments").on("click", showComments);

}); // Fin chargement page web

function showComments() {
    if(maxNumberOfPages>page){
        page++;
        getComments(document.querySelector("section.comments").dataset.slug);
    }else{
        $(".comment").show();
        $(this).html('Fermer les commentaires <i class="fas fa-caret-up"></i>').on("click", hideComments);
    }    
}

function hideComments() {
    $(".comment").hide();
    $(this).html('Voir les commentaires <i class="fas fa-caret-down"></i>').on("click", showComments);
}

function getComments(slug) {
    fetch(
        '/api/comments/post/' + slug + '?page=' + page,
        {
            method: 'GET',
            mode: 'same-origin',
            headers: {
                "X-Requested-With": 'XMLHttpRequest'
            }
        }
    ).then(
        response => response.text()
    ).then(
        value => {
            let object = JSON.parse(value);
            fillComments(object.data);
        }
    );
}

function fillComments(array) {
    for (let index = 0; index < array.length; index++) {
        let commentElement = $(".comment:last-of-type");
        let clone;
        if ((index < array.length-1) || (page < maxNumberOfPages)) {
            clone = commentElement.clone();
        }
        commentElement.find(".pseudo").text(array[index].pseudo);
        commentElement.find(".sent_at").text(array[index].sent_at);
        commentElement.find(".comment_content").text(array[index].content);
        if ((index < array.length-1) || (page < maxNumberOfPages)) {
            commentElement.after(clone);
        }
        commentElement.show();
    }
    upDateCommentCaret();
}

function upDateCommentCaret() {
    let element = $("#seeComments");
    if (page > 0 && page < maxNumberOfPages) {
        element.html('Voir les commentaires suivants <i class="fas fa-caret-down"></i>');
    } else if (page == maxNumberOfPages) {
        element.html('Fermer les commentaires <i class="fas fa-caret-up"></i>').on("click", hideComments);
    }
}