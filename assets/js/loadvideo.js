let player;

$(function () {

    $("a.player").on("click", onYouTubeIframeAPIReady);
    // onYouTubeIframeAPIReady();
});

// 3. This function creates an <iframe> (and YouTube player)
//    after the API code downloads.
function onYouTubeIframeAPIReady(e) {
    e.preventDefault();
    let sreenWidth = screen.width-60;
    player = new YT.Player(this.id, {
        height: Math.round(sreenWidth*9/16),
        width: sreenWidth,
        videoId: this.dataset.videoid,
        events: {
            onReady: onPlayerReady
        },
    });
}

// 4. The API will call this function when the video player is ready.
function onPlayerReady(event) {
    event.target.playVideo();
}