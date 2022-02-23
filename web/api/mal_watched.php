<?php

require_once __DIR__ . "/../../lib/myanimelist.php";

if (!check_csrf_token()) {
    echo "error:csrf";
}

$mal = MyAnimeList::from_session();
if ($mal == NULL) {
    echo "error:mal_session";
}

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

$client->patch("https://api.myanimelist.net/v2/anime/$id/my_list_status", [
    "headers" => [
        "Authorization" => "Bearer " . $mal->access_token,
    ],
    "form_params" => [
        "status" => "completed",
    ],
]);

header("Content-Type: application/json");
echo json_encode(["status" => "ok"]);