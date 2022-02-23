<?php
require_once __DIR__ . "/shared.php";

class Annict {

    static function client_id() {
        return (string)getenv("ANNICT_CLIENT_ID");
    }

    static function client_secret() {
        return (string)getenv("ANNICT_CLIENT_SECRET");
    }

    static function get_authorize_url() {
        global $PUBLIC_URL;
        return "https://annict.com/oauth/authorize?" . http_build_query([
            "client_id" => self::client_id(),
            "response_type" => "code",
            "redirect_uri" => $PUBLIC_URL . "/callback.php",
            "scope" => "read",
        ]);
    }

    static function new_from_code($code) {
        global $PUBLIC_URL, $client;
        $res = $client->post("https://annict.com/oauth/token", [
            "form_params" => [
                "client_id" => self::client_id(),
                "client_secret" => self::client_secret(),
                "code" => $code,
                "grant_type" => "authorization_code",
                "redirect_uri" => $PUBLIC_URL . "/callback.php",
            ],
        ]);

        $json = json_decode($res->getBody());
        var_dump($json);
        if (!isset($json->access_token)) {
            return $json->error;
        }
        $user_res = $client->get("https://api.annict.com/v1/me", [
            "headers" => [
                "Authorization" => "Bearer " . $json->access_token,
            ],
        ]);
        $user_res = json_decode($user_res->getBody());
        var_dump($user_res);
        return new Annict($json->access_token, $user_res->username);
    }

    function __construct(public string $access_token, public string $username)
    {
    }

    function save_to_session()
    {
        $_SESSION["annict"] = [
            "access_token" => $this->access_token,
            "username" => $this->username,
        ];
    }

    static function from_session()
    {
        if (!isset($_SESSION["annict"])) {
            return null;
        }
        return new Annict($_SESSION["annict"]["access_token"], $_SESSION["annict"]["username"]);
    }
}
