// @ts-check
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

function log(text, link) {
    const tn = document.createTextNode(text + (link == null ? "\n" : " "))
    logDiv.append(tn)
    if (link != null) {
        const l = document.createElement("a")
        l.href = link
        l.textContent = link + "\n"
        l.target = "_blank"
        logDiv.appendChild(l)
    }
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
    /** @type {{id: number, title: string}[]} */
    const malList = await call("mal_lists", {})
    const malIDs = malList.map(r => r.id)
    log(`MyAnimeList から視聴済みデータを取得完了 (${malList.length}件)`)
    const needToSync = annictList.filter(r => !malIDs.includes(parseInt(r.malAnimeId, 10)))
    const annictMALIDs = annictList.map(r => parseInt(r.malAnimeId, 10))
    const desync = malList.filter(r => !annictMALIDs.includes(r.id))
    log(`${needToSync.length}件の視聴済みデータが MyAnimeList に存在しないため、同期を開始します`)

    for (const id of needToSync) {
        log(`${id.title} を同期中… MAL ID: ${id.malAnimeId}, Annict:`, `https://annict.com/works/${id.annictId}`)
        await call("mal_watched", {id: parseInt(id.malAnimeId)})
    }

    log(`同期が完了しました`)
    if (annictList.length !== annictListAll.length) {
        log("--- Annict データのうち MyAnimeList ID が指定されていないもの ---")
        for (const a of annictListAll) {
            if (a.malAnimeId == null) log(a.title, `https://annict.com/works/${a.annictId}`)
        }
        log("--- ここまで ---")
    }
    if (desync.length) {
        log("--- MyAnimeList データのうち Annict に存在しないもの ---")
        for (const a of desync) {
            log(a.title, `https://myanimelist.net/ownlist/anime/${a.id}/edit`)
        }
        log("--- ここまで ---")
    }
})