<?php
require_once __DIR__ . "/../lib/myanimelist.php";

$mal = MyAnimeList::new_from_code(filter_input(INPUT_GET, "code"));
if ($mal instanceof string) {
?>
<h1>failed to auth with myanimelist</h1>
<a href="/mal_login.php">retry login</a> or <a href="/">back to home</a>
<?php
die();
}

$mal->save_to_session();
header("Location: /");