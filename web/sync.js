const startButton = document.getElementById("start")
const csrfToken = startButton.dataset.csrf

async function call(name, data) {
    const r = new URLSearchParams(Object.entries(data))
    return fetch("/api/" + name + ".php?ct=" + csrfToken, {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: r.toString(),
    }).then(r => r.json())
}

const logDiv = document.getElementById("log")

function log(text) {
    logDiv.textContent += text + "\n"
}

startButton.addEventListener("click", async () => {
    logDiv.textContent = "ログ:\n"
    log(`Annict から視聴済みデータを取得中…`)
    /** @type {{annictId: number, title: string, malAnimeId: string | null}[]} */
    const annictListAll = await call("annict_lists", {})
    log(`Annict から視聴済みデータを取得完了 (${annictListAll.length}件)`)
    const annictList = annictListAll.filter(r => r.malAnimeId != null)
    log(`(うち MyAnimeList ID が入っているのは ${annictList.length}件)`)
    log(`MyAnimeList から視聴済みデータを取得中…`)
    /** @type {number[]} */
    const malList = await call("mal_lists", {})
    log(`MyAnimeList から視聴済みデータを取得完了 (${malList.length}件)`)
    const needToSync = annictList.filter(r => !malList.includes(parseInt(r.malAnimeId, 10)))
    log(`${needToSync.length}件の視聴済みデータが MyAnimeList に存在しないため、同期を開始します`)

    for (const id of needToSync) {
        log(`${id.title} (${id.malAnimeId}, https://annict.com/works/${id.annictId}) を同期中…`)
        await call("mal_watched", {id: parseInt(id.malAnimeId)})
    }

    log(`同期が完了しました`)
})