<?php

require_once __DIR__ . "/../lib/annict.php";

$annict = Annict::new_from_code(filter_input(INPUT_GET, "code"));
if ($annict instanceof string) {
?>
<h1>failed to auth with annict</h1>
<a href="/login.php">retry login</a> or <a href="/">back to home</a>
<?php
die();
}

$annict->save_to_session();
header("Location: /");