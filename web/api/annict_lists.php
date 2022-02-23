<?php

require_once __DIR__ . "/../../lib/annict.php";

$annict = Annict::from_session();
$r = json_decode($client->post("https://api.annict.com/graphql", [
    "headers" => [
        "Authorization" => "Bearer " . $annict->access_token,
    ],
    "json" => ["query" => "query {
        viewer {
          works(state: WATCHED) {
            nodes {
              annictId	
              title
              malAnimeId	
            }
          }
        }
      }
    "],
])->getBody());

header("Content-Type: application/json; charset=UTF-8");
echo json_encode($r->data->viewer->works->nodes, JSON_UNESCAPED_UNICODE);