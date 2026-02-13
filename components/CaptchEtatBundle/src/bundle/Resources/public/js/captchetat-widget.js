function getApiUrl(get, type) {
    return "/api/simple-captcha-endpoint?get=" + get + "&c=" + type;
}

const captchaEtat = (function () {
    function _init(container = document) {
        const widgets = container.querySelectorAll('.js-captcha-widget');

        for (const widget of widgets) {
            if(widget.dataset.initialized === "true") {
                continue;
            }
            widget.dataset.initialized = "true"
            const htmlContainer = widget.querySelector('.captcha-html-container');
            const idInput = widget.querySelector('.captcha-input [name*="[captcha_id]"]')
            const captchaType = widget.dataset.type
            fetch(
                getApiUrl("image", captchaType),
                {
                    method: "GET",
                    headers: {"Content-Type": "application/json"}
                }
            ).then((response => {
                if (!response.ok) {
                    throw new Error("La réponse n'est pas OK");
                }
                return response.json()
            })).then((response => {
                let uuid = response.uuid;

                const captchaContainer = document.createElement("div");
                captchaContainer.style.display = "flex";
                captchaContainer.style.flexDirection = "row";
                htmlContainer.prepend(captchaContainer);

                const imageElement = document.createElement("img");
                imageElement.src = `${response.imageb64}`;
                imageElement.alt = "Rewrite the captcha code";
                captchaContainer.appendChild(imageElement);

                const refreshCaptcha = () => {
                    fetch(
                        getApiUrl("image", captchaType),
                        {
                            method: "GET",
                            headers: {"Content-Type": "application/json"}
                        }
                    ).then((refreshResponse => {
                        if (!refreshResponse.ok) throw new Error("La réponse n'est pas OK");
                        return refreshResponse.json()
                    })).then((refreshResponse => {
                        imageElement.src = refreshResponse.imageb64;
                        uuid = refreshResponse.uuid;
                        idInput.setAttribute("value", uuid)
                    })).catch((e => {
                        console.error("Erreur lors de la récupération du captcha")
                    }))
                }

                const logosElement = document.createElement("div");
                logosElement.style.marginLeft = "10px";
                logosElement.style.display = "flex";
                logosElement.style.flexDirection = "column";
                logosElement.style.justifyContent = "space-evenly";
                captchaContainer.appendChild(logosElement);

                const playButtonElement = document.createElement("button");
                playButtonElement.type = "button";
                playButtonElement.title = "Speak the captcha code";
                playButtonElement.alt = "Speak the captcha code";
                playButtonElement.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" version="1.0" width="25" height="25" viewBox="0 0 75 75"\nid="iconSound">\n<path d="M39.389,13.769 L22.235,28.606 L6,28.606 L6,47.699 L21.989,47.699 L39.389,62.75 L39.389,13.769z"\n style="stroke:#111;stroke-width:5;stroke-linejoin:round;fill:#111;"/>\n<path d="M48,27.6a19.5,19.5 0 0 1 0,21.4M55.1,20.5a30,30 0 0 1 0,35.6M61.6,14a38.8,38.8 0 0 1 0,48.6"\n style="fill:none;stroke:#111;stroke-width:5;stroke-linecap:round"/>\n</svg>';
                logosElement.appendChild(playButtonElement),
                    playButtonElement.addEventListener("click", () => {
                        try {
                            new Audio(getApiUrl("sound", captchaType) + "&t=" + uuid).play()
                        } catch (e) {
                            console.error("Erreur lors de la récupération du son");
                            refreshCaptcha()
                        }
                    });

                const refreshButtonElement = document.createElement("button");
                refreshButtonElement.type = "button";
                refreshButtonElement.title = "Generate a new captcha";
                refreshButtonElement.alt = "Generate a new captcha";
                refreshButtonElement.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 17 20" fill="none"\nid="iconReload">\n<path d="M9.00003 4.0001C11.1 4.0001 13.1 4.8001 14.6 6.3001C17.7 9.4001 17.7 14.5001 14.6 17.6001C12.8 19.5001 10.3 20.2001 7.90003 19.9001L8.40003 17.9001C10.1 18.1001 11.9 17.5001 13.2 16.2001C15.5 13.9001 15.5 10.1001 13.2 7.7001C12.1 6.6001 10.5 6.0001 9.00003 6.0001V10.6001L4.00003 5.6001L9.00003 0.600098V4.0001ZM3.30003 17.6001C0.700029 15.0001 0.300029 11.0001 2.10003 7.9001L3.60003 9.4001C2.50003 11.6001 2.90003 14.4001 4.80003 16.2001C5.30003 16.7001 5.90003 17.1001 6.60003 17.4001L6.00003 19.4001C5.00003 19.0001 4.10003 18.4001 3.30003 17.6001V17.6001Z"\n fill="black"/>\n</svg>';
                logosElement.appendChild(refreshButtonElement);
                refreshButtonElement.addEventListener("click", refreshCaptcha);

                idInput.setAttribute("value", uuid)

                if (captchaType.includes("FR")) {
                    playButtonElement.setAttribute("title", "Énoncer le code du captcha")
                    refreshButtonElement.setAttribute("title", "Générer un nouveau captcha")
                    imageElement.alt = "Recopier le code de sécurité de cette image";
                    playButtonElement.title = "Enoncer le code du captcha";
                    playButtonElement.alt = "Enoncer le code du captcha";
                    refreshButtonElement.title = "Générer un nouveau captcha";
                    refreshButtonElement.alt = "Générer un nouveau captcha";
                }
            })).catch((e => {
                console.error("Erreur lors de la récupération du captcha")
            }))
        }
    }

    return {init: _init};
})();

export default captchaEtat;
