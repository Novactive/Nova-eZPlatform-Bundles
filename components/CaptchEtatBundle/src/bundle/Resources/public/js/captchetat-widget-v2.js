!(function () {
    "use strict";
    let e, t, n, a, c;
    async function refreshCaptcha() {
        let n = e + "?get=image&c=" + t;
        await fetch(n, { method: "GET", headers: { "Content-Type": "application/json" } })
            .then((e) => {
                if (!e.ok) throw new Error("La réponse n'est pas OK");
                return e.json();
            })
            .then((e) => {
                (document.getElementById("captchaImage").src = e.imageb64), (c = e.uuid), document.getElementById("captchetat-uuid").setAttribute("value", c);
            })
            .catch((e) => {
                console.error("Erreur lors de la récupération du captcha");
            });
    }
    async function playCaptchaSound() {
        let n = e + "?get=sound&c=" + t + "&t=" + c;
        try {
            await new Audio(n).play();
        } catch (e) {
            console.error("Erreur lors de la récupération du son"), await refreshCaptcha();
        }
    }
    window.addEventListener("load", function () {
        (a = document.getElementById("captchetat")),
        a &&
        ((e = a.getAttribute("urlBackend")),
            (t = a.getAttribute("captchaStyleName")),
            (n = a.getAttribute("altImage")),
            (async function getCaptcha() {
                let a = e + "?get=image&c=" + t;
                await fetch(a, { method: "GET", headers: { "Content-Type": "application/json" } })
                    .then((e) => {
                        if (!e.ok) throw new Error("La réponse n'est pas OK");
                        return e.json();
                    })
                    .then((e) => {
                        (c = e.uuid),
                            (function constructHtml(e) {
                                const t = document.createElement("div");
                                (t.style.display = "flex"), (t.style.flexDirection = "row"), (t.id = "captchetat-container"), document.getElementById("captchetat").appendChild(t);
                                const a = document.createElement("img");
                                (a.src = `${e}`), (a.alt = n ?? "Recopier le code de sécurité de cette image"), (a.id = "captchaImage"), document.getElementById("captchetat-container").appendChild(a);
                                const o = document.createElement("div");
                                (o.id = "logos"),
                                    (o.style.marginLeft = "10px"),
                                    (o.style.display = "flex"),
                                    (o.style.flexDirection = "column"),
                                    (o.style.justifyContent = "space-evenly"),
                                    document.getElementById("captchetat-container").appendChild(o);
                                const d = document.createElement("button");
                                (d.type = "button"),
                                    (d.id = "playSoundIcon"),
                                    (d.title = "Speak the captcha code"),
                                    (d.alt = "Enoncer le code du captcha"),
                                    (d.innerHTML =
                                        '<svg xmlns="http://www.w3.org/2000/svg" version="1.0" width="25" height="25" viewBox="0 0 75 75"\n                     id="iconSound">\n                    <path d="M39.389,13.769 L22.235,28.606 L6,28.606 L6,47.699 L21.989,47.699 L39.389,62.75 L39.389,13.769z"\n                          style="stroke:#111;stroke-width:5;stroke-linejoin:round;fill:#111;"/>\n                    <path d="M48,27.6a19.5,19.5 0 0 1 0,21.4M55.1,20.5a30,30 0 0 1 0,35.6M61.6,14a38.8,38.8 0 0 1 0,48.6"\n                          style="fill:none;stroke:#111;stroke-width:5;stroke-linecap:round"/>\n                </svg>'),
                                    document.getElementById("logos").appendChild(d),
                                    document.getElementById("playSoundIcon").addEventListener("click", playCaptchaSound);
                                const r = document.createElement("button");
                                (r.type = "button"),
                                    (r.id = "reloadCaptchaIcon"),
                                    (r.title = "Generate a new captcha"),
                                    (r.alt = "Générer un nouveau captcha"),
                                    (r.innerHTML =
                                        '<svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 17 20" fill="none"\n                     id="iconReload">\n                    <path d="M9.00003 4.0001C11.1 4.0001 13.1 4.8001 14.6 6.3001C17.7 9.4001 17.7 14.5001 14.6 17.6001C12.8 19.5001 10.3 20.2001 7.90003 19.9001L8.40003 17.9001C10.1 18.1001 11.9 17.5001 13.2 16.2001C15.5 13.9001 15.5 10.1001 13.2 7.7001C12.1 6.6001 10.5 6.0001 9.00003 6.0001V10.6001L4.00003 5.6001L9.00003 0.600098V4.0001ZM3.30003 17.6001C0.700029 15.0001 0.300029 11.0001 2.10003 7.9001L3.60003 9.4001C2.50003 11.6001 2.90003 14.4001 4.80003 16.2001C5.30003 16.7001 5.90003 17.1001 6.60003 17.4001L6.00003 19.4001C5.00003 19.0001 4.10003 18.4001 3.30003 17.6001V17.6001Z"\n                          fill="black"/>\n                </svg>'),
                                    document.getElementById("logos").appendChild(r),
                                    document.getElementById("reloadCaptchaIcon").addEventListener("click", refreshCaptcha);
                                const l = document.createElement("input");
                                (l.id = "captchetat-uuid"), (l.type = "hidden"), (l.value = c), (l.name = "captchetat-uuid"), document.getElementById("captchetat").appendChild(l);
                            })(e.imageb64),
                            (function setButtonTitle() {
                                t.includes("FR") &&
                                (document.getElementById("playSoundIcon").setAttribute("title", "Énoncer le code du captcha"), document.getElementById("reloadCaptchaIcon").setAttribute("title", "Générer un nouveau captcha"));
                            })();
                    })
                    .catch((e) => {
                        console.error("Erreur lors de la récupération du captcha");
                    });
            })());
    }),
        (window.captchetatComponentModule = { refreshCaptcha });
})();
