<?php
require_once __DIR__ . "/../lib/annict.php";
require_once __DIR__ . "/../lib/myanimelist.php";

$annict = Annict::from_session();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>annict2mal</title>
    </head>
    <body>
        <h1>annict2mal</h1>
        <p><a href="https://twitter.com/intent/tweet?text=annict2mal&url=https://annict2mal.herokuapp.com/&hashtags=annict2mal">Tweet</a>・<a href="https://github.com/rinsuki-lab/annict2mal">GitHub</a></p>
        <p>Annictの「視聴済み」作品の情報をMyAnimeListにコピーします</p>
        <p>MyAnimeList側で「視聴済み」にした作品数が1000件を越えると正しく動かないかもしれません</p>
        <?php if ($annict === null): ?>
            <a href="/login.php">Login with Annict</a>
        <?php else: ?>
<?php
            $mal = MyAnimeList::from_session();
?>
            <p>Annict: @<?= $annict->username ?>でログイン中</p>
            <?php if ($mal === null): ?>
                <a href="/mal_login.php">Login with MyAnimeList</a>
            <?php else: ?>
                <p>MyAnimeList: @<?= $mal->username ?>でログイン中</p>
                <button id="start" data-csrf="<?= get_csrf_token(); ?>">同期開始</button>
                <pre id="log"></pre>
                <script src="/sync.js?ver=2"></script>
            <?php endif; ?>
        <?php endif; ?>
    </body>
</html>
