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

jQuery(document).ready(function () {
    const $collectionHolder = $('.items-rss');
    const containerId = $collectionHolder;
    const $templateValues = $('#template-values');
    $collectionHolder.data('index', $collectionHolder.find(':input').length);
    // for edition mode
    if ($templateValues.data('rss-fields-indexes') !== undefined) {
        $.each($templateValues.data('rss-fields-indexes'), function (k, v) {
            setCTEvent(v);
        });
    }

    $('#open-child-form').on('click', function (e) {
        e.preventDefault();
        addChildForm($collectionHolder, containerId);

        $('.js-novaezrssfeed-select-location-id').on('click', function (e) {
            e.preventDefault();
            handleSelectLocationClick($(this));
        });
    });

    $(document).on('click', '.delete-rss-items', function (e) {
        e.preventDefault();
        $(this).parent().remove();
    });

    $('.js-novaezrssfeed-select-location-id').each(function () {
        const feedItem = $(this);
        $.get($templateValues.data('rss-info-location') + '/' + $($(feedItem).data('location-input-selector')).val(), function (data) {
            if (typeof data.content !== undefined && typeof data.content.name !== undefined) {
                const selectedLocation = `<li class="path-location">
                    <div class="pull-left">${data.content.name}</div>
                    <a class="btn btn-primary delete-rss-items pull-right">
                        <svg class="ez-icon ez-icon-trash"><use xlink:href="/bundles/ezplatformadminui/img/ez-icons.svg#trash"></use></svg> 
                    </a> 
                </li>`;
                $($(feedItem).data('selected-location-list-selector')).html(selectedLocation);
            }
        });

        $(feedItem).on('click', function (e) {
            e.preventDefault();
            handleSelectLocationClick($(this));
        });
    });

    let $deleteRest = $('#delete-rest');
    if ($deleteRest.length > 0) {
        $deleteRest.hide();
    }
    $('.col-form-label').each(function () {
        if ($(this).html().length < 2) {
            $(this).hide();
        }
    });

    function handleSelectLocationClick(clickedButton) {
        const token = document.querySelector('meta[name="CSRF-Token"]').content;
        const siteaccess = document.querySelector('meta[name="SiteAccess"]').content;
        const udwContainer = $("#react-udw").get(0);
        const configFromYaml = $(clickedButton).data('udw-config');
        ReactDOM.render(React.createElement(eZ.modules.UniversalDiscovery, {
            ...configFromYaml,
            onCancel: function () {
                ReactDOM.unmountComponentAtNode(udwContainer)
            },
            restInfo: {token: token, siteaccess: siteaccess},
            confirmLabel: 'Add locations',
            title: 'Choose locations',
            onConfirm: function (data) {
                const selectedItems = data.reduce((total, item) => total + `<li class="path-location"><div class="pull-left">${item.ContentInfo.Content.Name}</div><a class="btn btn-primary delete-rss-items pull-right"><svg class="ez-icon ez-icon-trash"><use xlink:href="/bundles/ezplatformadminui/img/ez-icons.svg#trash"></use></svg> </a> </li>`, '');
                $($(clickedButton).data('location-input-selector')).val(data.map(item => item.id).join());
                $($(clickedButton).data('selected-location-list-selector')).html(selectedItems);
                ReactDOM.unmountComponentAtNode(udwContainer);
            }
        }), udwContainer);
    }

    function addChildForm($collectionHolder, containerId) {
        const prototype = $collectionHolder.data('prototype');
        const index = $collectionHolder.data('index');
        const removeForm = $('<a class="btn btn-primary delete-rss-items pull-right">' +
            '<svg><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/bundles/ezplatformadminui/img/ez-icons.svg#trash"></use></svg>' +
            '</a>');
        const newForm = prototype.replace(/__name__/g, index);
        const newRow = $('<section class="container mt-4">' + '<div class="card ez-card"><div class="card-body">' + newForm + '</div></div></section>');
        newRow.find('.card-body').prepend(removeForm);
        containerId.append(newRow);
        const newIndex = index + 1;
        $collectionHolder.data('index', newIndex);
        document.dispatchEvent(new CustomEvent("rss.item.add", {"selector": newRow}));
        setCTEvent(index);
    }

    function setCTEvent(index) {
        $(document).on('change', '#rss_feeds_feed_items_' + index + '_contenttype_id', function (e) {
            const val = $(this).val();
            const prefixItem = "#rss_feeds_feed_items";
            const selectFields = ["title", "description", "category", "media"];
            const loader = $('<div class="loading-image">' +
                '<img src="' + $templateValues.data('loader-path') + '" class="img-responsive"  alt=""/>' +
                '</div>');

            $(this).after(loader);
            $('#loading-image').show();
            $.ajax({
                url: $templateValues.data('rss-fields-path') + '?contenttype_id=' + val,
                type: 'POST',
                success: function (response) {
                    $.each(selectFields, function (fieldKey, fieldName) {
                        const $mainSelector = $(prefixItem + "_" + index + "_" + fieldName);
                        $mainSelector.html('');
                        $mainSelector.append($("<option value=''>[Passer]</option>").prop("selected", true));
                        $.each(response, function (k, v) {
                            $mainSelector.append($("<option></option>")
                                .attr("value", v).text(k));
                        });
                    });
                    loader.remove();
                }
            })
        });
    }
});
