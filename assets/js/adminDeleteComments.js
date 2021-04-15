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
        fetch(
            '/admin/api/comments/' + comment.id,
            {
                method: 'DELETE',
                mode: 'same-origin',
                headers: {
                    "Authorization": comment.token,
                    "X-Requested-With": 'XMLHttpRequest'
                }
            }
        ).then((response) => {
            if (response.status == 200) {
                $("#comment_" + comment.id).parents("tr").remove();
                $("#message-api").html("Elément(s) supprimés");
                $("#message-api").removeClass("d-none").addClass('alert-success');
            } else {
                $("#message-api").html("Une erreur est survenue: " + response.status + " " + response.statusText);
                $("#message-api").removeClass("d-none").addClass('alert-warning');
            }
        }).catch((error) => {
            $("#message-api").html("Une erreur est survenue: " + error.message);
            $("#message-api").removeClass("d-none").addClass('alert-danger');
        });
    }
    commentsToDelete = [];
    setTimeout(function () {
        $("#message-api").addClass("d-none");
    }, 2000);
}