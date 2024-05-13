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
    const collectionHolder = doc.querySelector('.items-rss');
    const containerId = collectionHolder;
    const templateValues = doc.querySelector('#template-values');
    const rssFieldsIndexes = JSON.parse(templateValues.dataset.rssFieldsIndexes)
    collectionHolder.dataset.index = rssFieldsIndexes.length;

    for (const rssFieldsIndex of rssFieldsIndexes ) {
        setCTEvent(rssFieldsIndex);
    }

    doc.querySelector('#open-child-form').addEventListener('click', function (e) {
        e.preventDefault();
        addChildForm(collectionHolder, containerId);
    });


    doc.addEventListener('rss.item.add', function (e) {
        const dropdowns = e.detail.selector.querySelectorAll('.ibexa-dropdown');
        dropdowns.forEach((dropdownContainer, index) => {
            const dropdown = new window.ibexa.core.Dropdown({
                container: dropdownContainer,
            });

            dropdown.init();
        });
    })

    function handleSelectLocationClick(clickedButton) {
        const token = doc.querySelector('meta[name="CSRF-Token"]').content;
        const siteaccess = doc.querySelector('meta[name="SiteAccess"]').content;
        const udwContainer = doc.querySelector("#react-udw");
        const configFromYaml = JSON.parse(clickedButton.dataset.udwConfig);
        ReactDOM.render(React.createElement(ibexa.modules.UniversalDiscovery, {
            ...configFromYaml,
            onCancel: function () {
                ReactDOM.unmountComponentAtNode(udwContainer)
            },
            restInfo: {token: token, siteaccess: siteaccess},
            confirmLabel: 'Add locations',
            title: 'Choose locations',
            onConfirm: function (data) {
                const selectedItems = data.reduce((total, item) =>
                    total + `<li class="path-location">
                        <div class="pull-left">${item.ContentInfo.Content.Name}</div>
                    </li>`, '');
                doc.querySelector(clickedButton.dataset.locationInputSelector).value = data.map(item => item.id).join();
                doc.querySelector(clickedButton.dataset.selectedLocationListSelector).innerHTML = selectedItems;
                ReactDOM.unmountComponentAtNode(udwContainer);
            }
        }), udwContainer);
    }

    function addChildForm(collectionHolder, containerId) {
        const prototype = collectionHolder.dataset.prototype;
        const index = collectionHolder.dataset.index;
        const newForm = prototype.replace(/__name__/g, index);
        const newRow = htmlToElement('<fieldset class="ibexa-container">' + newForm + '</fieldset>');
        containerId.append(newRow);
        collectionHolder.dataset.index = index + 1;
        doc.dispatchEvent(new CustomEvent("rss.item.add", {detail : {"selector": newRow}}));
        setCTEvent(index);
    }

    function setCTEvent(index) {
        const itemContainer = doc.querySelector(`#rss_feeds_feed_items_${index}`)
        itemContainer.querySelector(`#rss_feeds_feed_items_${index}_contenttype_id`)
            .addEventListener('change', function (e) {
                const val = e.currentTarget.value;
                const prefixItem = "#rss_feeds_feed_items";
                const selectFields = ["title", "description", "category", "media"];
                const loader = htmlToElement('<div class="loading-image">' +
                    '<img src="' + templateValues.dataset.loaderPath + '" class="img-responsive"  alt=""/>' +
                    '</div>');
                e.currentTarget.after(loader);

                const xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function () {
                if (this.readyState !== 4) return;

                if (this.status === 200) {
                    for (const fieldName of selectFields) {
                        const mainSelector = document.querySelector(prefixItem + "_" + index + "_" + fieldName);
                        mainSelector.innerHTML = '';
                        mainSelector.append(htmlToElement("<option value='' selected>[Passer]</option>"));
                        const response = JSON.parse(this.responseText);
                        for (const responseElement in response) {
                            const option = htmlToElement('<option value="">' + responseElement + '</option>');
                            option.setAttribute("value", response[responseElement]);
                            mainSelector.append(option);
                        }
                    }
                    loader.remove();
                }
            };

            xhr.open('POST', templateValues.dataset.rssFieldsPath + '?contenttype_id=' + val, true);
            xhr.send();
        });


        const selectLocationButton = itemContainer.querySelector('.js-novaezrssfeed-select-location-id');
        const selectedLocationId = doc.querySelector(selectLocationButton.dataset.locationInputSelector).value
        if(selectedLocationId) {
            fetch(templateValues.dataset.rssInfoLocation + '/' + selectedLocationId)
                .then( (response) => response.json())
                .then( function (data) {
                    if (typeof data.content !== undefined && typeof data.content.name !== undefined) {
                        const selectedLocation = `<li class="path-location">
                <div class="pull-left">${data.content.name}</div>
            </li>`;
                        doc.querySelector(selectLocationButton.dataset.selectedLocationListSelector).innerHTML = selectedLocation;
                    }
                });
        }


        itemContainer.querySelector('.js-novaezrssfeed-select-location-id').addEventListener('click', function (e) {
            e.preventDefault();
            handleSelectLocationClick(e.currentTarget);
        });


        itemContainer.querySelector('.delete-rss-items').addEventListener('click', function (e) {
            e.preventDefault();
            try {
                e.currentTarget.parentNode.parentNode.remove();
            } catch (e) {
                console.log(e)
            }
        });
    }

    /**
     * @param html
     * @returns {HTMLElement}
     */
    function htmlToElement(html) {
        const template = doc.createElement('template');
        html = html.trim(); // Never return a text node of whitespace as the result
        template.innerHTML = html;
        return template.content.firstChild;
    }
})(window, document);
