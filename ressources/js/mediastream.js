var monAudio; 
var niveauVolume;
$(document).ready(function() {
    monAudio = new Audio("ressources/sounds/chinois.mp3");
    var isPlaying = false; 

    $("#btnPlay").click(function() {
        if (isPlaying) {
            monAudio.pause();
            $("#imgBtnPlay").attr("src", "ressources/ico/play-fill.svg");
            isPlaying = false;
        } else {
            monAudio.play();
            $("#imgBtnPlay").attr("src", "ressources/ico/pause-circle-fill.svg");
            isPlaying = true;
        }
    });

    $('.parentSoundbar').click(function(e) {
        var posClick = e.pageX;
        var largeur = $('.parentSoundbar').width();
        var position = $('.parentSoundbar').offset(); // Récupère les positions top et left
        var posLeft = position.left; // Affiche la position horizontale du début de l'élément
        
        var percentage = (posClick - posLeft) / largeur;
        monAudio.volume = percentage;

        $('.soundbar').css('width', percentage*100 + '%');
    });


    $('#btnMute').click(function() {
    if (monAudio.volume != 0)
        {
            niveauVolume = monAudio.volume;
            $('#btnMute').attr("src", "ressources/ico/volume-mute-fill")
            monAudio.volume = 0;
        }
    else{
        monAudio.volume = niveauVolume;
        $('#btnMute').attr("src", "ressources/ico/volume-up-fill")
    }
    })


    $('.parentPlaybar').click(function(e){
        var posClick = e.pageX;
        var largeur = $('.parentPlaybar').width();
        var position = $('.parentPlaybar').offset(); 
        var posLeft = position.left; 
    
        var percentage = (posClick - posLeft) / largeur;
        var dureeSelected = percentage * monAudio.duration ;
        console.log(dureeSelected);
        monAudio.currentTime = dureeSelected;


    })

    monAudio.addEventListener('timeupdate', function() {
        var percentage = (this.currentTime / this.duration) * 100;
        $('.playbar').css('width', percentage + '%');
    });
});
