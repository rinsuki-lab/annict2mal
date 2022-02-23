<?php

require_once __DIR__ . "/shared.php";

class MyAnimeList {
    static function client_id() {
        return (string)getenv("MAL_CLIENT_ID");
    }

    static function client_secret() {
        return (string)getenv("MAL_CLIENT_SECRET");
    }

    static function get_authorize_url() {
        global $PUBLIC_URL;
        $_SESSION["mal_code_verifier"] = bin2hex(random_bytes(32));
        return "https://myanimelist.net/v1/oauth2/authorize?" . http_build_query([
            "client_id" => self::client_id(),
            "response_type" => "code",
            "redirect_uri" => $PUBLIC_URL . "/mal_callback.php",
            "code_challenge" => $_SESSION["mal_code_verifier"],
            "code_challenge_method" => "plain",
        ]);
    }

    static function new_from_code($code) {
        global $PUBLIC_URL, $client;
        $res = $client->post("https://myanimelist.net/v1/oauth2/token", [
            "form_params" => [
                "client_id" => self::client_id(),
                "client_secret" => self::client_secret(),
                "code" => $code,
                "grant_type" => "authorization_code",
                "redirect_uri" => $PUBLIC_URL . "/mal_callback.php",
                "code_verifier" => $_SESSION["mal_code_verifier"],
            ],
        ]);
        $res = json_decode($res->getBody());
        if (!isset($res->access_token)) {
            return $res->error;
        }
        $user_res = $client->get("https://api.myanimelist.net/v2/users/@me", [
            "headers" => [
                "Authorization" => "Bearer " . $res->access_token,
            ],
        ]);
        $user_res = json_decode($user_res->getBody());
        return new MyAnimeList($res->access_token, $user_res->name);
    }

    function __construct(public string $access_token, public string $username)
    {
    }

    function save_to_session()
    {
        $_SESSION["mal"] = [
            "access_token" => $this->access_token,
            "username" => $this->username,
        ];
    }

    static function from_session() {
        if (!isset($_SESSION["mal"])) {
            return null;
        }
        return new MyAnimeList($_SESSION["mal"]["access_token"], $_SESSION["mal"]["username"]);
    }
}