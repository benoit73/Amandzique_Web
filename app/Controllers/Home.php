<?php

namespace App\Controllers;

use Google_Client;
use Google\Service\YouTube;
use Google_Service_Exception;
use Google_Exception;
class Home extends BaseController
{ 
    public function index(): string
    {
        $response = null;
        return view('layouts/home_page', ['response' => $response]);
    }

    public function searchVideo($recherche)
    {
        
        $client = new Google_Client();
        $client->setDeveloperKey('AIzaSyAl-kdFvRiXetX3CeMRrVD7UBbxX3gCbNk');
        $youtube = new YouTube($client);

        try {
            $queryParams = [
                'q' => $recherche,
                'type' => 'video',
                'maxResults' => 10,
            ];

            $response = $youtube->search->listSearch('snippet', $queryParams);
            $this->index($response);
            return $response;
        } catch (Google_Service_Exception $e) {
            // En cas d'erreur du service Google, retourne un message d'erreur
            return 'Erreur du service Google : ' . htmlspecialchars($e->getMessage());
        } catch (Google_Exception $e) {
            // Autres exceptions Google, retourne également un message d'erreur
            return 'Erreur Google : ' . htmlspecialchars($e->getMessage());
        }
    }
}
