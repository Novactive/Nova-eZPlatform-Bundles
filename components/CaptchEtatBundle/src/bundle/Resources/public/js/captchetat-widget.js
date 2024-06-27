const captchaEtat = (function () {
    function _init(container = document) {
        const widgets = container.querySelectorAll('.js-captcha-widget');

        for (const widget of widgets) {
            const soundLink = widget.querySelector('.BDC_SoundLink');
            const answerInput = widget.querySelector('.captcha-input input[type="text"]');
            soundLink.addEventListener('click', function (e) {
                if(answerInput) {
                    answer.removeAttribute('disabled');
                    answerInput.focus()
                }
            })
        }
    }

    return { init: _init };
})();

export default captchaEtat;
