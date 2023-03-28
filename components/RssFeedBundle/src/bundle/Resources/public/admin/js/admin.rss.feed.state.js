/*
 * NovaeZRssFeedBundle.
 *
 * @package   NovaeZRssFeedBundle
 *
 * @author    Novactive
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZRssFeedBundle/blob/master/LICENSE
 *
 */

(function (global, doc) {

    const updateVisibility = (event) => {
        let urlChangeVisibilityFeed = doc.getElementById('template-values').dataset.changeVisibilityFeedUrl;

        const params = {
            "feedId": event.currentTarget.value
        };

        let queryString = Object.keys(params).map(key => key + '=' + params[key]).join('&');

        let request = new XMLHttpRequest();

        request.onload = function (response) {
            const data = JSON.parse(response.target.responseText);

            if (data.success === true) {
                event.target.closest('.ibexa-checkbox-icon').classList.toggle('is-checked')
            }
        };

        request.open('POST', urlChangeVisibilityFeed, true);
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        request.send(queryString);

    }

    [...doc.querySelectorAll('.ibexa-checkbox-icon__checkbox')].forEach(btn => {
        btn.addEventListener('click', updateVisibility, false);
    });


})(window, document);