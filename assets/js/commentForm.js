let fields;
let observer = new MutationObserver(refresh);

$(function () {
    $(".success, .form-error-message").delay(1500).hide(200);

    /*Validation des champs du formulaire */
    fields = $("#comments .form-control");
    onCheckFields();
    fields.on('keyup', onCheckFields);
    observerConnect();

    document.querySelector("form>button").addEventListener("click", extractFormData);
});

function checkField(elementObject) {
    let minLength = $(elementObject).attr('minlength');
    let maxLength = $(elementObject).attr('maxlength');
    let str = $(elementObject).val();
    if (str.length > maxLength) {
        return false;
    } else if (str.length < minLength) {
        return false;
    } else if ( /[&<>"']/.test(str) ) {
        return false;
    } else if (/^[a-zA-Z]{2,240}/.test(str)) {
        return true;
    }
    return false;
}

function onCheckFields() {
    for (let index = 0; index < fields.length; index++) {
        if (!checkField(fields[index])) {
            observer.disconnect();
            document.querySelector("form>button").disabled = true;
            observerConnect();
            return false;
        }
        if (index == fields.length - 1) {
            observer.disconnect();
            document.querySelector("form>button").disabled = false;
            observerConnect();
        }
    }
    return true;
}

function refresh() {
    window.location.reload();
}

function observerConnect() {
    let form = document.querySelector("form");
    observer.observe(form, { attributes: true, childList: true, characterData: true, subtree: true });
}

function extractFormData(e) {
    e.preventDefault();
    let _token = $("#comments__token").val();
    let nosiar = $('#comments_nosiar').val();
    if (this.disabled == false && onCheckFields && _token != "" && nosiar === "") {
        let formData = {};
        for (let field of fields) {
            formData[field.id.replace('comments_', '')] = escapeHtml($(field).val());
        }
        formData.nosiar = escapeHtml(nosiar);
        formData._token = escapeHtml(_token);
        let slug = document.querySelector("section.comments").dataset.slug;
        ajaxCall(formData, slug);
    } else {
        $("#comment_sent").text('Un problème est surnenu. Le commentaire n\'a pas été enregistré.').addClass("form-error-message").show();
        setTimeout(function () {
            $("#comment_sent").hide();
        }, 2000);
    }
}

function ajaxCall(formData, slug) {
    fetch(
        '/api/comments/post/' + slug,
        {
            method: 'POST',
            mode: 'same-origin',
            headers: {
                "Authorization": formData._token,
                "Content-type": 'application/json',
                "X-Requested-With": 'XMLHttpRequest'
            },
            body: JSON.stringify(formData)
        }
    ).then(
        response => {
            if (response.status == 201) {
                $("#comments_pseudo").val('');
                $("#comments_content").val('');
                $("#comment_sent").text('Commentaire enregistré').addClass("success").show();
                setTimeout(function () {
                    $("#comment_sent").hide();
                }, 2000);
            } else {
                $("#comment_sent").text('Un problème est surnenu. Le commentaire n\'a pas été enregistré.').addClass("form-error-message").show();
                setTimeout(function () {
                    $("#comment_sent").hide();
                }, 2000);
            }
            onCheckFields();
        },
    );
}

function escapeHtml(str) {
    let map =
    {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return str.replace(/[&<>"']/g, function (m) { return map[m]; });
}