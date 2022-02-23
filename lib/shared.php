<?php

require_once __DIR__ . "/../vendor/autoload.php";

session_start();
$PUBLIC_URL = getenv("PUBLIC_URL");
$client = new GuzzleHttp\Client();

function get_csrf_token() {
    if (!isset($_SESSION["csrf_token"])) {
        $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
    }
    return $_SESSION["csrf_token"];
}

function check_csrf_token() {
    if (!isset($_GET["ct"])) {
        return false;
    }
    if ($_GET["ct"] !== get_csrf_token()) {
        return false;
    }
    return true;
}