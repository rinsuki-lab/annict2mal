<?php
require_once __DIR__ . "/../lib/myanimelist.php";

header("Location: " . MyAnimeList::get_authorize_url());