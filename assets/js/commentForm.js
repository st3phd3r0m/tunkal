
$(function () {

    $(".success, .form-error-message").delay(1500).hide(200);

    let textarea = document.querySelector("#comments_content");
    let span = document.createElement("span");
    textarea.parentElement.insertBefore(span, textarea);
    textarea.addEventListener('keyup', checkTextareaLength);

});

function checkTextareaLength() {
    let span = $(this).prev();
    if (this.value.length > 240) {
        span.text("Votre texte est trop long : " + this.value.length);
        span.css('color', 'red');
    } else if (this.value.length < 10) { 
        span.text("Votre texte est trop court : " + this.value.length);
        span.css('color', 'red');
    }
    else {
        span.text("");
    }
}