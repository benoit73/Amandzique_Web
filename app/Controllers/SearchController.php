<?php

namespace App\Controllers;

use Google_Client;
use Google\Service\YouTube;
use Google_Service_Exception;
use Google_Exception;
use mysqli;
use Exception;
use Stringable;
class SearchController extends BaseController
{ 
    public function searchVideo()
    {
        $results = "";
        $recherche = $this->request->getPost('recherche');

        $mysqli = new mysqli("localhost", "root", "", "amandzique");
        if ($mysqli->connect_error) {
            die('Erreur de connexion : ' . $mysqli->connect_error);
        }

        // Préparation de la requête SQL avec une syntaxe correcte
        $query = $mysqli->prepare("SELECT * FROM musiques 
                                   JOIN musiquesRecherches ON musiques.id = musiquesrecherches.idMusique
                                   JOIN recherches ON recherches.id = musiquesrecherches.idRecherche
                                   WHERE recherches.recherche = ?");
        if (!$query) {
            die('Erreur de préparation : ' . $mysqli->error);
        }
    
        $query->bind_param("s", $recherche);
        $query->execute();
    
        // Gestion des résultats
        $resultsQuery = $query->get_result(); // Récupération des résultats
        file_put_contents('mesLog.txt', $resultsQuery, FILE_APPEND);

        if ($resultsQuery->num_rows > 0) {
            $results = []; // Initialisation d'un tableau vide
            $results['monformat'] = 'sql'; // Ajout de la propriété 'monformat'
        
            while ($row = $resultsQuery->fetch_assoc()) {
                $results['items'][] = $row; // Stockage des résultats dans une clé 'items'
            }
        
            // Pas besoin de json_encode ici
            $query->close();
            $mysqli->close();
            return $this->response->setJSON($results); // Retourne un tableau de vidéos sous forme JSON
        }
        

        else {
            file_put_contents('mesLog.txt', 'pas de résultats', FILE_APPEND);
            $client = new Google_Client();
            $client->setDeveloperKey('');
            $youtube = new YouTube($client);
        
            try {
                $queryParams = [
                    'q' => $recherche,  // Assurez-vous que '$recherche' est correctement définie et nettoyée
                    'type' => 'video',
                    'maxResults' => 20,
                ];
        
                $response = $youtube->search->listSearch('snippet', $queryParams);
                $freshResponse['items'] = $response->items;  // Assurez-vous que l'accès aux 'items' est correct
        
                try {
                    $this->saveResearch($recherche, $freshResponse);  // Gestion de la sauvegarde de la recherche
                } catch (Exception $e) {
                    // Gestion des exceptions pour la fonction saveResearch, par exemple log l'erreur
                    error_log('Erreur lors de la sauvegarde de la recherche : ' . $e->getMessage());
                }
        
                $freshResponse['monformat'] = 'api'; 
                return json_encode($freshResponse);
        
            } catch (Google_Service_Exception $e) {
                // En cas d'erreur du service Google, retourne un message d'erreur
                return json_encode(['error' => 'Erreur du service Google : ' . htmlspecialchars($e->getMessage())]);
            } catch (Google_Exception $e) {
                // Autres exceptions Google, retourne également un message d'erreur
                return json_encode(['error' => 'Erreur Google : ' . htmlspecialchars($e->getMessage())]);
            } catch (Exception $e) {
                // Gestion des exceptions imprévues
                return json_encode(['error' => 'Erreur inattendue : ' . htmlspecialchars($e->getMessage())]);
            }
        }
        
    }


    public function saveResearch($recherche, $results) {
        $mysqli = new mysqli("localhost", "root", "", "amandzique");
        if ($mysqli->connect_error) {
            die('Erreur de connexion : ' . $mysqli->connect_error);
        }
    
        $mysqli->autocommit(FALSE); // Turn off autocommit mode
    
        try {
            // Insertion dans la table recherches
            $stmtRecherche = $mysqli->prepare("INSERT INTO recherches (recherche, dateRecherche) VALUES (?, NOW());");
            if (!$stmtRecherche) {
                throw new Exception('Erreur de préparation pour recherches: ' . $mysqli->error);
            }
            $stmtRecherche->bind_param("s", $recherche);
            $stmtRecherche->execute();
            if ($stmtRecherche->error) {
                throw new Exception('Erreur d’insertion dans recherches: ' . $stmtRecherche->error);
            }
            $idRecherche = $stmtRecherche->insert_id;
    
            // Préparation pour l'insertion dans la table musiques et la table de jointure
            $stmtMusique = $mysqli->prepare("INSERT INTO musiques (lienYoutube, lienImage, titre, chaine, datePublication) VALUES (?, ?, ?, ?, ?)");
            if (!$stmtMusique) {
                throw new Exception('Erreur de préparation pour musiques: ' . $mysqli->error);
            }
    
            // Parcours de chaque musique et insertion
            foreach ($results['items'] as $musique) {
                $lienYoutube = $musique['id']['videoId']; // Corrected data access for associative arrays
                $lienImage = $musique['snippet']['thumbnails']['high']['url'];
                $titre = $musique['snippet']['title'];
                $chaine = $musique['snippet']['channelTitle'];
                $datePublication = $musique['snippet']['publishedAt'];
    
                $stmtMusique->bind_param("sssss", $lienYoutube, $lienImage, $titre, $chaine, $datePublication);
                $stmtMusique->execute();
    
                if ($stmtMusique->error) {
                    throw new Exception('Erreur d’insertion dans musiques: ' . $stmtMusique->error);
                }
                $idMusique = $stmtMusique->insert_id;
    
                // Insertion dans la table de jointure
                $stmtJointure = $mysqli->prepare("INSERT INTO musiquesRecherches (idMusique, idRecherche) VALUES (?, ?)");
                if (!$stmtJointure) {
                    throw new Exception('Erreur de préparation pour musiquesRecherches: ' . $mysqli->error);
                }
                $stmtJointure->bind_param("ii", $idMusique, $idRecherche);
                $stmtJointure->execute();
                if ($stmtJointure->error) {
                    throw new Exception('Erreur d’insertion dans musiquesRecherches: ' . $stmtJointure->error);
                }
                $stmtJointure->close();
            }
    
            $mysqli->commit();
        } catch (Exception $e) {
            $mysqli->rollback();
            error_log($e->getMessage()); // Log the error message
            die('Erreur: ' . $e->getMessage());
        } finally {
            // Fermeture des statements et de la connexion
            if (isset($stmtRecherche)) {
                $stmtRecherche->close();
            }
            if (isset($stmtMusique)) {
                $stmtMusique->close();
            }
            $mysqli->close();
        }
    }
    
}
