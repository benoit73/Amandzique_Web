var monAudio; 
var niveauVolume;
var urlMusiqueLecteur = "";
$(document).ready(function() {
    monAudio = new Audio();
    var isPlaying = false; 

    $(".contenu").on('click', '.videoContainer', function() {
        // Mettre en pause l'objet Audio existant
        if (monAudio) {
            monAudio.pause();
        }

        var url = $(this).attr("data-url"); // Récupération de l'attribut 'data-url'
        var jsonMsg = JSON.stringify({url: url});

        $.ajax({
            url: 'http://localhost:5000/apimusique',
            type: 'POST',
            contentType: 'application/json',  // Définition explicite du Content-Type
            data: jsonMsg,  // Envoi direct du JSON
            async: false,
            success: function(response) {
                urlMusiqueLecteur = (JSON.parse(response)).urlMusique;
                console.log(urlMusiqueLecteur);
            },
            error: function(xhr, status, error) {
                alert("Erreur lors de l'appel AJAX: " + error);
            }
        });

        // Réinitialiser l'objet Audio existant
        if (monAudio) {
            monAudio.currentTime = 0;
            monAudio.src = "";
        }

        // Créer un nouvel objet Audio pour la nouvelle musique
        monAudio = new Audio(urlMusiqueLecteur);
        monAudio.addEventListener('timeupdate', bougerlabarre);
        monAudio.play();
        isPlaying = true;
        $("#imgBtnPlay").attr("src", "ressources/ico/pause-circle-fill.svg");

    });
    
    


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


    function bougerlabarre() { // Définir la fonction bougerlabarre
        console.log("la barre bouge");
        var percentage = (this.currentTime / this.duration) * 100;
        $('.playbar').css('width', percentage + '%');
    }




   

});
