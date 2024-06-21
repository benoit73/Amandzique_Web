$(document).ready(function() {

    var lastResponse = localStorage.getItem('lastResponse');
    if (lastResponse !== null) {
        lastResponse = JSON.parse(lastResponse);
        AfficherResultats(lastResponse);
    }
    
    
    $('.search-input').on('keypress', function(e) {
        if (e.which == 13) {  // Enter key pressed
            e.preventDefault();  // Prevent the default form submit
            var recherche = $(this).val();  // Get the value from the input
            console.log(recherche);
            if (recherche.trim() !== '') {  // Check if the input is not empty
                $.ajax({
                    url: 'http://localhost/Amandzique_Web/ControllerSearchVideo',
                    type: 'POST',
                    dataType: 'json',  // Expecting JSON response
                    data: { recherche: recherche },
                    success: function(response) {
                        console.log(response);
                        AfficherResultats(response);
                    },
                    error: function(xhr, status, error) {
                        console.log("Erreur AJAX - Statut : ", status);
                        console.log("Détails de l'erreur : ", error);
                        console.log("Réponse complète du serveur : ", xhr.responseText);
                    }
                });
            }
        }
    });


    function AfficherResultats(response) {
        $('.contenu').empty();
        if (response.monformat === "api") {
            console.log("Recherche API");
            response.items.forEach(function(item) {
                item.snippet.title = ReduireTitre(item.snippet.title);
                console.log(item.snippet.title);
                $('.contenu').append(
                    "<div class='videoContainer' data-url='" + item.id.videoId + "'> <div class='thumbnailContainer'> <img class='thumbnail' src='" + item.snippet.thumbnails.high.url + "'> </div> <div class='titre'>" + item.snippet.title + "</div> <div class='infosVid'>" + item.snippet.channelTitle + "</div> <div class='infosVid'>" + item.snippet.publishedAt + "</div> </div>"
                );
            });
        }
        if (response.monformat === "sql") {
            console.log("Recherche SQL");
            response.items.forEach(function(item) {
                var title = ReduireTitre(item.titre); 
                console.log(title);
                $('.contenu').append(
                    "<div class='videoContainer' data-url='" + item.lienYoutube + "'> <div class='thumbnailContainer'> <img class='thumbnail' src='" + item.lienImage + "'> </div><div class='titre'>" + title + "</div> <div class='infosVid'>" + item.chaine + "</div> <div class='infosVid'>" + item.datePublication + "</div> </div>"
                );
            });
        }
    
        localStorage.setItem('lastResponse', JSON.stringify(response));
    }
    

    function ReduireTitre(titre){
        if (titre.length > 70){
            return titre.slice(0, 68 + "..")
        }
        else{
            return titre;
        }
    }


});



