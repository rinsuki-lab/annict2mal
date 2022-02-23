<?php

require_once __DIR__ . "/../lib/annict.php";

header("Location: " . Annict::get_authorize_url());