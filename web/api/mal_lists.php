<?php

require_once __DIR__ . "/../../lib/myanimelist.php";

$mal = MyAnimeList::from_session();
$res = json_decode($client->get("https://api.myanimelist.net/v2/users/@me/animelist", [
    "query" => [
        "status" => "completed",
        "limit" => 1000,
    ],
    "headers" => [
        "Authorization" => "Bearer " . $mal->access_token,
    ]
])->getBody());

$res = array_map(function ($input) {
    return $input->node->id;
}, $res->data);

header("Content-Type: application/json; charset=UTF-8");
echo json_encode($res);