let fields;
let observer = new MutationObserver(refresh);

$(function () {
    $(".success, .form-error-message").delay(1500).hide(200);

    /*Validation des champs du formulaire */
    fields = $("#comments .form-control");
    onCheckFields();
    fields.on('keyup', onCheckFields);

    observerConnect();
});

function checkField(elementObject) {
    let minLength = $(elementObject).attr('minlength');
    let maxLength = $(elementObject).attr('maxlength');
    let str = $(elementObject).val();
    if (str.length > maxLength) {
        return false;
    } else if (str.length < minLength) {
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
            break;
        }
        if (index == fields.length - 1) {
            observer.disconnect();
            document.querySelector("form>button").disabled = false;
            observerConnect();
        }
    }
}

function refresh(){
    window.location.reload();
}

function observerConnect(){
    let form = document.querySelector("form");
    observer.observe(form, { attributes: true, childList: true , characterData: true, subtree: true});
}