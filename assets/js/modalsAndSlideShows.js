let slideIndex;
let src = [];

//Attente chargement page web
$(function () {
    let miniatures = $(".hover-shadow");
    for (let miniature of miniatures) {
        src.push( $(miniature).data('image-path') );
        $(miniature).on("click", openModal);
    }
}); // Fin chargement page web

// Open the Modal
function openModal() {
    slideIndex = $(this).data('currentslide');
    document.getElementById("myModal").style.display = "block";
    document.getElementsByClassName("mySlides")[0].style.display = "block";
    $(".close").one("click", closeModal);
    showPicture();
}

function showPicture() {
    $(".mySlides>img").attr('src', src[slideIndex]);
    $(".prev").one("click", plusSlides);
    $(".next").one("click", plusSlides);
    $(window).on("keyup", plusSlidesKey);
}

// Next/previous controls
function plusSlidesKey(e) {
    if (e.key === "ArrowRight") {
        $(this).off('keyup');
        slideIndex ++;
        console.log(slideIndex);
        slideIndex = slideIndex % src.length;
        console.log(slideIndex);
        slideIndex = (slideIndex<0) ? src.length+slideIndex : slideIndex;
        console.log(slideIndex);
        showPicture();
    } else if(e.key === "ArrowLeft" ) {
        $(this).off('keyup');
        slideIndex --;
        console.log(slideIndex);
        slideIndex = slideIndex % src.length;
        console.log(slideIndex);
        slideIndex = (slideIndex<0) ? src.length+slideIndex : slideIndex;
        console.log(slideIndex);
        showPicture();
    } else if(e.key === "Escape" ) {
        $(this).off('keyup');
        document.getElementById("myModal").style.display = "none";
    }
}

// Close the Modal
function closeModal() {
    $(this).off('click');
    document.getElementById("myModal").style.display = "none";
}

// Next/previous controls
function plusSlides() {
    $(this).off('click');
    slideIndex += parseInt(this.dataset.plus);
    console.log(slideIndex);
    slideIndex = slideIndex % src.length;
    console.log(slideIndex);
    slideIndex = (slideIndex<0) ? src.length+slideIndex : slideIndex;
    console.log(slideIndex);
    showPicture();
}