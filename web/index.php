<?php
require_once __DIR__ . "/../lib/annict.php";
require_once __DIR__ . "/../lib/myanimelist.php";
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>annict2mal</title>
    </head>
    <body>
        <h1>annict2mal</h1>
        <p>Annictの「視聴済み」作品の情報をMyAnimeListにコピーします</p>
        <p>MyAnimeList側で「視聴済み」にした作品数が1000件を越えると正しく動かないかもしれません</p>
<?php
$annict = Annict::from_session();
if ($annict == NULL) {
    ?><a href="/login.php">Login with Annict</a><?php
} else {
    ?><p>Annict: @<?= $annict->username ?>でログイン中</p><?php
    $mal = MyAnimeList::from_session();
    if ($mal == NULL) {
        ?><a href="/mal_login.php">Login with MyAnimeList</a><?php
    } else {
        ?><p>MyAnimeList: @<?= $mal->username ?>でログイン中</p><?php
        ?><button id="start" data-csrf="<?= get_csrf_token(); ?>">同期開始</button><pre id="log"></pre><script src="/sync.js"></script><?php
    }
}
?>
    </body>
</html>
