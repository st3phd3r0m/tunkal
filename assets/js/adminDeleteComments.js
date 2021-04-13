let commentsToDelete = [];

$(function () {
    $("input[name^='delete_comment_']").on('click', onStoreCommentsToDelete);

    $("#confirm_delete").on('click', deleteComments);
});


function onStoreCommentsToDelete() {
    let object = {
        'id': this.dataset.commentid,
        'token': $(this).siblings("input[type='hidden']").val()
    };
    if (this.checked) {
        commentsToDelete.push(object);
    } else {
        commentsToDelete = commentsToDelete.filter(isItStored, object);
    }
    toogleTrash();
}

function isItStored(el) {
    return (el.id != this.id) || (el.token != this.token);
}

function toogleTrash() {
    if (commentsToDelete.length == 0) {
        $("#trash").addClass('d-none');
    } else {
        $("#trash").removeClass('d-none');
    }
}

function deleteComments() {
    for (let comment of commentsToDelete) {
        $.ajax({
            url: window.location.pathname+this.dataset.apiurl+comment.id,
            method: 'DELETE',
            type: 'DELETE',
            headers: {"Authorization": comment.token},
        }).done(function () {
            $("#comment_" + comment.id).parents("tr").remove();
        });
    }
    commentsToDelete=[];
}